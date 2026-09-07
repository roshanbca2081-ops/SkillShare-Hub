<?php
$page_title = 'Available Sessions';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'upcoming';

$query = "SELECT s.*, u.full_name as mentor_name, u.avatar, c.title as course_title 
          FROM sessions s 
          JOIN users u ON s.mentor_id = u.id 
          LEFT JOIN courses c ON s.course_id = c.id 
          WHERE s.status != 'cancelled'";
$params = [];

if ($filter === 'upcoming') {
    $query .= " AND s.scheduled_at > NOW() AND s.status = 'scheduled'";
} elseif ($filter === 'ongoing') {
    $query .= " AND s.status = 'ongoing'";
} elseif ($filter === 'completed') {
    $query .= " AND s.status = 'completed'";
} elseif ($filter === 'free') {
    $query .= " AND s.is_free = 1 AND s.scheduled_at > NOW()";
}

if ($search) {
    $query .= " AND (s.title LIKE ? OR u.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY s.scheduled_at ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sessions = $stmt->fetchAll();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Available Sessions</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="my-session.php" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-check"></i> My Sessions
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Search sessions..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="btn-group w-100" role="group">
                        <a href="?filter=upcoming" class="btn btn-outline-primary <?php echo $filter === 'upcoming' ? 'active' : ''; ?>">Upcoming</a>
                        <a href="?filter=ongoing" class="btn btn-outline-primary <?php echo $filter === 'ongoing' ? 'active' : ''; ?>">Ongoing</a>
                        <a href="?filter=free" class="btn btn-outline-success <?php echo $filter === 'free' ? 'active' : ''; ?>">Free</a>
                        <a href="?filter=completed" class="btn btn-outline-secondary <?php echo $filter === 'completed' ? 'active' : ''; ?>">Completed</a>
                    </div>
                </div>
            </div>
            
            <!-- Sessions List -->
            <?php if (!empty($sessions)): ?>
                <div class="row g-4">
                    <?php foreach ($sessions as $session): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="session-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="session-card-title"><?php echo htmlspecialchars($session['title']); ?></h6>
                                <span class="badge bg-<?php echo getStatusBadge($session['status']); ?>">
                                    <?php echo ucfirst($session['status']); ?>
                                </span>
                            </div>
                            
                            <div class="session-card-meta">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($session['mentor_name']); ?></span>
                                <?php if ($session['course_title']): ?>
                                <span><i class="fas fa-book"></i> <?php echo htmlspecialchars($session['course_title']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="session-card-meta">
                                <span><i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo $session['duration']; ?> min</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span>
                                    <?php if ($session['is_free']): ?>
                                        <span class="badge bg-success">Free</span>
                                    <?php else: ?>
                                        <span class="fw-bold">$<?php echo number_format($session['price'], 2); ?></span>
                                    <?php endif; ?>
                                </span>
                                <span class="text-muted small">
                                    <i class="fas fa-users"></i> <?php echo $session['max_participants']; ?> spots
                                </span>
                            </div>
                            
                            <div class="mt-3">
                                <?php if ($session['status'] === 'scheduled' || $session['status'] === 'ongoing'): ?>
                                    <a href="details.php?id=<?php echo $session['id']; ?>" class="btn btn-primary w-100">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="fas fa-lock"></i> Completed
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                    <h5>No sessions found</h5>
                    <p class="text-muted">Check back later for new sessions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>