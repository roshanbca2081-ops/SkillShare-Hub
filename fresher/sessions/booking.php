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
                           WHERE s.id = ? AND s.status = 'scheduled' AND s.scheduled_at > NOW() AND u.role = 'mentor' AND u.is_active = 1");
    $stmt->execute([$session_id]);
    $session = $stmt->fetch();
    
    if (!$session) {
        redirect('index.php');
    }
} else {
    // Get mentor's available sessions
    $query = "SELECT s.*, u.full_name as mentor_name, u.id as mentor_id 
              FROM sessions s 
              JOIN users u ON s.mentor_id = u.id 
              WHERE s.status = 'scheduled' AND s.scheduled_at > NOW() AND u.role = 'mentor' AND u.is_active = 1";
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
    $user_id = getUserId();

    $stmt = $pdo->prepare("SELECT s.id, s.mentor_id, s.status, s.scheduled_at, s.is_free, s.course_id, s.max_participants, s.title
                           FROM sessions s JOIN users u ON u.id = s.mentor_id
                           WHERE s.id = ? AND s.status = 'scheduled' AND s.scheduled_at > NOW()
                             AND u.role = 'mentor' AND u.is_active = 1 LIMIT 1");
    $stmt->execute([$session_id]);
    $bookableSession = $stmt->fetch();

    if (!$bookableSession) {
        $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'This mentor session is no longer available. Please choose another scheduled session.'];
        redirect('booking.php' . ($mentor_id ? '?mentor=' . $mentor_id : ''));
    }

    $hasEnrollment = false;
    if (!empty($bookableSession['course_id'])) {
        $enrollmentStmt = $pdo->prepare("SELECT 1 FROM enrollments WHERE fresher_id = ? AND course_id = ? AND status != 'dropped' LIMIT 1");
        $enrollmentStmt->execute([$user_id, $bookableSession['course_id']]);
        $hasEnrollment = (bool)$enrollmentStmt->fetchColumn();
    }
    if (empty($bookableSession['is_free']) && !$hasEnrollment) {
        $_SESSION['alert'] = ['type' => 'info', 'icon' => 'info-circle', 'message' => 'Enroll in the linked course before booking this paid session.'];
        redirect('booking.php?session=' . $session_id);
    }

    $seatStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE session_id = ? AND status IN ('pending', 'approved')");
    $seatStmt->execute([$session_id]);
    if ((int)$seatStmt->fetchColumn() >= max(1, (int)$bookableSession['max_participants'])) {
        $_SESSION['alert'] = ['type' => 'warning', 'icon' => 'exclamation-circle', 'message' => 'This session is full. Please choose another session.'];
        redirect('booking.php' . ($mentor_id ? '?mentor=' . $mentor_id : ''));
    }

    $existingStmt = $pdo->prepare('SELECT id, status FROM bookings WHERE fresher_id = ? AND session_id = ? LIMIT 1');
    $existingStmt->execute([$user_id, $session_id]);
    $existingBooking = $existingStmt->fetch();
    if ($existingBooking && in_array($existingBooking['status'], ['pending', 'approved'], true)) {
        $_SESSION['alert'] = ['type' => 'info', 'icon' => 'info-circle', 'message' => 'You already have a ' . $existingBooking['status'] . ' booking for this session.'];
        redirect('my-session.php');
    }

    try {
        if ($existingBooking) {
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'pending', notes = ?, booking_date = CURRENT_TIMESTAMP WHERE id = ? AND fresher_id = ?");
            $stmt->execute([$notes, $existingBooking['id'], $user_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, notes, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $session_id, $notes]);
        }

        $notificationStmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link)
                                           VALUES (?, 'booking', 'New Booking Request', CONCAT(?, ' requested your session: ', ?), CONCAT('mentor/booking/view.php?id=', ?))");
        $notificationStmt->execute([$bookableSession['mentor_id'], getUserName(), $bookableSession['title'], $session_id]);

        $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Booking request sent successfully. Waiting for mentor approval.'];
        redirect('my-session.php');
    } catch (PDOException $e) {
        error_log('Session booking failed: ' . $e->getMessage());
        $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'The booking could not be completed. Please refresh and try again.'];
        redirect('booking.php?session=' . $session_id);
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
                            <h5><?php echo $mentor_id ? 'This mentor has no sessions scheduled yet' : 'No sessions are currently scheduled'; ?></h5>
                            <p class="text-muted">Mentors need to schedule an upcoming session before learners can book it. You can message the mentor to ask when they will be available.</p>
                            <?php if ($mentor_id): ?>
                                <a href="../message/index.php?user_id=<?php echo $mentor_id; ?>" class="btn btn-primary"><i class="fas fa-envelope me-1"></i> Message Mentor</a>
                                <a href="../mentor/index.php" class="btn btn-outline-primary ms-2">Browse Mentors</a>
                            <?php else: ?>
                                <a href="../mentor/index.php" class="btn btn-primary">Browse Mentors</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>