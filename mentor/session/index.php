<?php
$page_title = 'My Sessions';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'upcoming';

$query = "SELECT s.*, 
          (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'approved') as booked_count,
          (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'pending') as pending_count,
          c.title as course_title
          FROM sessions s 
          LEFT JOIN courses c ON s.course_id = c.id 
          WHERE s.mentor_id = ?";

$params = [$user_id];

if ($filter === 'upcoming') {
    $query .= " AND s.scheduled_at > NOW() AND s.status IN ('scheduled', 'ongoing')";
} elseif ($filter === 'past') {
    $query .= " AND s.scheduled_at < NOW() AND s.status IN ('completed', 'cancelled')";
} elseif ($filter === 'ongoing') {
    $query .= " AND s.status = 'ongoing'";
} elseif ($filter === 'cancelled') {
    $query .= " AND s.status = 'cancelled'";
}

$query .= " ORDER BY s.scheduled_at " . ($filter === 'upcoming' ? 'ASC' : 'DESC');

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sessions = $stmt->fetchAll();

// Get counts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND scheduled_at > NOW() AND status IN ('scheduled', 'ongoing')");
$stmt->execute([$user_id]);
$upcoming_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND status = 'ongoing'");
$stmt->execute([$user_id]);
$ongoing_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND scheduled_at < NOW() AND status IN ('completed', 'cancelled')");
$stmt->execute([$user_id]);
$past_count = $stmt->fetchColumn();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Sessions</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Schedule Session
                    </a>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-primary"><?php echo $upcoming_count; ?></h3>
                            <small class="text-muted">Upcoming</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-success"><?php echo $ongoing_count; ?></h3>
                            <small class="text-muted">Ongoing</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-secondary"><?php echo $past_count; ?></h3>
                            <small class="text-muted">Past</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'upcoming' ? 'active' : ''; ?>" href="?filter=upcoming">
                        Upcoming <span class="badge bg-primary"><?php echo $upcoming_count; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'ongoing' ? 'active' : ''; ?>" href="?filter=ongoing">
                        Ongoing <span class="badge bg-success"><?php echo $ongoing_count; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'past' ? 'active' : ''; ?>" href="?filter=past">
                        Past <span class="badge bg-secondary"><?php echo $past_count; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'cancelled' ? 'active' : ''; ?>" href="?filter=cancelled">
                        Cancelled
                    </a>
                </li>
            </ul>
            
            <!-- Sessions List -->
            <?php if (!empty($sessions)): ?>
                <div class="row g-4">
                    <?php foreach ($sessions as $session): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5><?php echo htmlspecialchars($session['title']); ?></h5>
                                    <span class="badge bg-<?php 
                                        echo $session['status'] === 'ongoing' ? 'success' : 
                                            ($session['status'] === 'scheduled' ? 'primary' : 
                                            ($session['status'] === 'completed' ? 'secondary' : 'danger')); 
                                    ?>">
                                        <?php echo ucfirst($session['status']); ?>
                                    </span>
                                </div>
                                
                                <?php if ($session['course_title']): ?>
                                    <p class="text-muted small">
                                        <i class="fas fa-book"></i> <?php echo htmlspecialchars($session['course_title']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?>
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-clock"></i> <?php echo $session['duration']; ?> min
                                    </span>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-info">
                                        <i class="fas fa-users"></i> <?php echo $session['booked_count']; ?>/<?php echo $session['max_participants']; ?>
                                    </span>
                                    <?php if ($session['pending_count'] > 0): ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> <?php echo $session['pending_count']; ?> pending
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($session['is_free']): ?>
                                        <span class="badge bg-success">Free</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="view.php?id=<?php echo $session['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="edit.php?id=<?php echo $session['id']; ?>" class="btn btn-sm btn-warning flex-grow-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <?php if ($session['status'] !== 'cancelled'): ?>
                                            <a href="cancel.php?id=<?php echo $session['id']; ?>" class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Cancel this session?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                    <h5>No sessions found</h5>
                    <p class="text-muted">You haven't created any sessions yet.</p>
                    <a href="create.php" class="btn btn-primary">Schedule Your First Session</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>