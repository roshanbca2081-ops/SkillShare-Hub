<?php
$page_title = 'Session Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$session_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT s.*, u.full_name as mentor_name, u.avatar, u.id as mentor_id, 
                       c.title as course_title, c.id as course_id
                       FROM sessions s 
                       JOIN users u ON s.mentor_id = u.id 
                       LEFT JOIN courses c ON s.course_id = c.id 
                       WHERE s.id = ?");
$stmt->execute([$session_id]);
$session = $stmt->fetch();

if (!$session) {
    redirect('index.php');
}

// Check if user has booked this session
$user_id = getUserId();
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE fresher_id = ? AND session_id = ?");
$stmt->execute([$user_id, $session_id]);
$booking = $stmt->fetch();

// Check if user is enrolled in the course
$is_enrolled = false;
if ($session['course_id']) {
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE fresher_id = ? AND course_id = ? AND status != 'dropped'");
    $stmt->execute([$user_id, $session['course_id']]);
    $is_enrolled = $stmt->fetch();
}

// Handle booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book'])) {
    if (!$booking) {
        $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$user_id, $session_id]);
        
        // Create notification for mentor
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) 
                               VALUES (?, 'booking', 'New Booking Request', 
                                       CONCAT(?, ' has requested to book your session: ', ?),
                                       'mentor/bookings/view.php?id=' || ?)");
        $stmt->execute([$session['mentor_id'], getUserName(), $session['title'], $session_id]);
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Session booking request sent successfully!'
        ];
        redirect('details.php?id=' . $session_id);
    }
}

// Cancel booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    if ($booking) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$booking['id']]);
        
        $_SESSION['alert'] = [
            'type' => 'warning',
            'icon' => 'exclamation-circle',
            'message' => 'Booking cancelled successfully.'
        ];
        redirect('details.php?id=' . $session_id);
    }
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
                <h1 class="h2">Session Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sessions
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3><?php echo htmlspecialchars($session['title']); ?></h3>
                                <span class="badge bg-<?php echo getStatusBadge($session['status']); ?> fs-6">
                                    <?php echo ucfirst($session['status']); ?>
                                </span>
                            </div>
                            
                            <?php if ($session['course_title']): ?>
                            <p class="text-muted">
                                <i class="fas fa-book"></i> Part of: 
                                <a href="../../public/course-detail.php?id=<?php echo $session['course_id']; ?>">
                                    <?php echo htmlspecialchars($session['course_title']); ?>
                                </a>
                            </p>
                            <?php endif; ?>
                            
                            <div class="session-meta mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Date & Time</small>
                                            <p class="mb-0"><i class="fas fa-calendar text-primary"></i> <?php echo formatDateTime($session['scheduled_at']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Duration</small>
                                            <p class="mb-0"><i class="fas fa-clock text-primary"></i> <?php echo $session['duration']; ?> minutes</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
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
                            </div>
                            
                            <h5>About this Session</h5>
                            <p><?php echo nl2br(htmlspecialchars($session['description'] ?? 'No description available.')); ?></p>
                            
                            <div class="d-flex gap-2">
                                <span class="badge bg-info"><i class="fas fa-users"></i> Max: <?php echo $session['max_participants']; ?> participants</span>
                                <?php if ($session['meeting_link']): ?>
                                    <span class="badge bg-success"><i class="fas fa-video"></i> Online Session</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mentor Info -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>Mentor</h5>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo getAvatar($session); ?>" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($session['mentor_name']); ?></h6>
                                      <a href="../mentor/details.php?id=<?php echo $session['mentor_id']; ?>" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="fas fa-user"></i> View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar - Booking -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h5>Book This Session</h5>
                            
                            <?php if ($booking && $booking['status'] === 'pending'): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock"></i> Booking request pending approval
                                </div>
                                <form method="POST">
                                    <button type="submit" name="cancel" class="btn btn-danger w-100" onclick="return confirm('Cancel this booking?')">
                                        <i class="fas fa-times"></i> Cancel Booking
                                    </button>
                                </form>
                            <?php elseif ($booking && $booking['status'] === 'approved'): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> Booking confirmed!
                                </div>
                                <?php if ($session['meeting_link']): ?>
                                    <a href="<?php echo $session['meeting_link']; ?>" target="_blank" class="btn btn-success w-100">
                                        <i class="fas fa-video"></i> Join Session
                                    </a>
                                <?php endif; ?>
                                <form method="POST" class="mt-2">
                                    <button type="submit" name="cancel" class="btn btn-outline-danger w-100" onclick="return confirm('Cancel this booking?')">
                                        <i class="fas fa-times"></i> Cancel Booking
                                    </button>
                                </form>
                            <?php elseif ($booking && $booking['status'] === 'completed'): ?>
                                <div class="alert alert-secondary">
                                    <i class="fas fa-check-circle"></i> Session completed
                                </div>
                                <?php if ($session['recording_url']): ?>
                                    <a href="<?php echo $session['recording_url']; ?>" target="_blank" class="btn btn-primary w-100">
                                        <i class="fas fa-play"></i> Watch Recording
                                    </a>
                                <?php endif; ?>
                            <?php elseif ($booking && $booking['status'] === 'cancelled'): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle"></i> Booking cancelled
                                </div>
                                <form method="POST">
                                    <button type="submit" name="book" class="btn btn-primary w-100">
                                        <i class="fas fa-redo"></i> Re-book Session
                                    </button>
                                </form>
                            <?php elseif ($booking && $booking['status'] === 'rejected'): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle"></i> Booking was rejected
                                </div>
                            <?php else: ?>
                                <?php if ($session['status'] === 'scheduled' || $session['status'] === 'ongoing'): ?>
                                    <?php if ($session['is_free'] || $is_enrolled): ?>
                                        <form method="POST">
                                            <button type="submit" name="book" class="btn btn-primary w-100">
                                                <i class="fas fa-calendar-plus"></i> Book Session
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> This session requires payment or course enrollment.
                                        </div>
                                         <a href="../payment/index.php?session=<?php echo $session['id']; ?>" class="btn btn-success w-100">
                                            <i class="fas fa-credit-card"></i> Pay $<?php echo number_format($session['price'], 2); ?>
                                        </a>
                                        <?php if ($session['course_id']): ?>
                                            <a href="../../public/course-detail.php?id=<?php echo $session['course_id']; ?>" class="btn btn-outline-primary w-100 mt-2">
                                                <i class="fas fa-book"></i> Enroll in Course
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-secondary">
                                        <i class="fas fa-lock"></i> This session is no longer available
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>