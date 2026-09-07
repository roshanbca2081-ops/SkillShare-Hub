<?php
$page_title = 'Book Session';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$mentor_id = isset($_GET['mentor']) ? (int)$_GET['mentor'] : 0;
$session_id = isset($_GET['session']) ? (int)$_GET['session'] : 0;

// If session_id is provided, get that session
if ($session_id) {
    $stmt = $pdo->prepare("SELECT s.*, u.full_name as mentor_name, u.id as mentor_id 
                           FROM sessions s 
                           JOIN users u ON s.mentor_id = u.id 
                           WHERE s.id = ? AND s.status = 'scheduled'");
    $stmt->execute([$session_id]);
    $session = $stmt->fetch();
    
    if (!$session) {
        redirect('index.php');
    }
} else {
    // Get mentor's available sessions
    $query = "SELECT s.*, u.full_name as mentor_name 
              FROM sessions s 
              JOIN users u ON s.mentor_id = u.id 
              WHERE s.status = 'scheduled' AND s.scheduled_at > NOW()";
    $params = [];
    
    if ($mentor_id) {
        $query .= " AND s.mentor_id = ?";
        $params[] = $mentor_id;
    }
    
    $query .= " ORDER BY s.scheduled_at";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $sessions = $stmt->fetchAll();
}

// Handle booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_session'])) {
    $session_id = (int)$_POST['session_id'];
    $notes = sanitize($_POST['notes'] ?? '');
    
    $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, notes) VALUES (?, ?, ?)");
    $stmt->execute([getUserId(), $session_id, $notes]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Session booked successfully!'
    ];
    redirect('my-session.php');
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Book a Session</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <?php if (isset($session) && $session): ?>
                <!-- Single Session Booking -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h4>Book: <?php echo htmlspecialchars($session['title']); ?></h4>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Mentor</small>
                                            <p class="mb-0"><i class="fas fa-user text-primary"></i> <?php echo htmlspecialchars($session['mentor_name']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Date & Time</small>
                                            <p class="mb-0"><i class="fas fa-calendar text-primary"></i> <?php echo formatDateTime($session['scheduled_at']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Duration</small>
                                            <p class="mb-0"><i class="fas fa-clock text-primary"></i> <?php echo $session['duration']; ?> minutes</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Price</small>
                                            <p class="mb-0">
                                                <?php if ($session['is_free']): ?>
                                                    <span class="badge bg-success">Free</span>
                                                <?php else: ?>
                                                    <span class="fw-bold">$<?php echo number_format($session['price'], 2); ?></span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <form method="POST">
                                    <input type="hidden" name="session_id" value="<?php echo $session['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Notes (Optional)</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="Any specific topics you'd like to cover?"></textarea>
                                    </div>
                                    <button type="submit" name="book_session" class="btn btn-primary">
                                        <i class="fas fa-calendar-check"></i> Confirm Booking
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Select Session -->
                <div class="row">
                    <?php if (!empty($sessions)): ?>
                        <?php foreach ($sessions as $session): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h5><?php echo htmlspecialchars($session['title']); ?></h5>
                                    <p class="text-muted small">with <?php echo htmlspecialchars($session['mentor_name']); ?></p>
                                    <div class="mb-2">
                                        <span class="badge bg-info"><i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?></span>
                                        <span class="badge bg-secondary"><i class="fas fa-clock"></i> <?php echo $session['duration']; ?> min</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <?php if ($session['is_free']): ?>
                                            <span class="badge bg-success">Free</span>
                                        <?php else: ?>
                                            <span class="fw-bold">$<?php echo number_format($session['price'], 2); ?></span>
                                        <?php endif; ?>
                                        <a href="booking.php?session=<?php echo $session['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-calendar-plus"></i> Book
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5>No sessions available</h5>
                            <p class="text-muted">Check back later for new sessions.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>