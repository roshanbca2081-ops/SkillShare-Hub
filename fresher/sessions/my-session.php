<?php
$page_title = 'My Sessions';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

// Upcoming sessions
$stmt = $pdo->prepare("SELECT b.*, s.id as session_id, s.title, s.description, s.scheduled_at, s.duration, 
                       s.meeting_link, s.recording_url, u.full_name as mentor_name, u.id as mentor_id
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON s.mentor_id = u.id 
                       WHERE b.fresher_id = ? AND b.status = 'approved' AND s.scheduled_at > NOW()
                       ORDER BY s.scheduled_at");
$stmt->execute([$user_id]);
$upcoming = $stmt->fetchAll();

// Pending bookings
$stmt = $pdo->prepare("SELECT b.*, s.title, s.scheduled_at, u.full_name as mentor_name 
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON s.mentor_id = u.id 
                       WHERE b.fresher_id = ? AND b.status = 'pending'
                       ORDER BY s.scheduled_at");
$stmt->execute([$user_id]);
$pending = $stmt->fetchAll();

// Past sessions
$stmt = $pdo->prepare("SELECT b.*, s.id as session_id, s.title, s.scheduled_at, s.duration, 
                       s.recording_url, u.full_name as mentor_name, u.id as mentor_id,
                       b.rating as user_rating
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON s.mentor_id = u.id 
                       WHERE b.fresher_id = ? AND (b.status = 'completed' OR s.scheduled_at < NOW())
                       ORDER BY s.scheduled_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$past = $stmt->fetchAll();

// Cancelled bookings
$stmt = $pdo->prepare("SELECT b.*, s.title, s.scheduled_at, u.full_name as mentor_name 
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON s.mentor_id = u.id 
                       WHERE b.fresher_id = ? AND b.status = 'cancelled'
                       ORDER BY b.updated_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$cancelled = $stmt->fetchAll();

// Handle cancellation
if (isset($_GET['cancel']) && isset($_GET['id'])) {
    $booking_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND fresher_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Booking cancelled successfully.'
    ];
    redirect('my-sessions.php');
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
                <h1 class="h2">My Sessions</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Browse Sessions
                    </a>
                </div>
            </div>
            
            <!-- Pending Bookings -->
            <?php if (!empty($pending)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <i class="fas fa-clock"></i> Pending Requests
                </div>
                <div class="card-body">
                    <?php foreach ($pending as $booking): ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($booking['title']); ?></h6>
                            <small class="text-muted">with <?php echo htmlspecialchars($booking['mentor_name']); ?></small>
                            <br><small class="text-muted"><i class="fas fa-calendar"></i> <?php echo formatDateTime($booking['scheduled_at']); ?></small>
                        </div>
                        <span class="badge bg-warning">Pending</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Upcoming Sessions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-calendar-check"></i> Upcoming Sessions
                </div>
                <div class="card-body">
                    <?php if (!empty($upcoming)): ?>
                        <?php foreach ($upcoming as $session): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($session['title']); ?></h6>
                                <small class="text-muted">with <?php echo htmlspecialchars($session['mentor_name']); ?></small>
                                <br><small><i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?></small>
                                <br><small><i class="fas fa-clock"></i> <?php echo $session['duration']; ?> minutes</small>
                            </div>
                            <div class="text-end">
                                <?php if ($session['meeting_link'] && strtotime($session['scheduled_at']) <= strtotime('+1 hour')): ?>
                                    <a href="<?php echo $session['meeting_link']; ?>" target="_blank" class="btn btn-success btn-sm mb-1">
                                        <i class="fas fa-video"></i> Join
                                    </a>
                                <?php endif; ?>
                                <a href="details.php?id=<?php echo $session['session_id']; ?>" class="btn btn-outline-primary btn-sm d-block">
                                    <i class="fas fa-eye"></i> Details
                                </a>
                                <a href="?cancel=1&id=<?php echo $session['id']; ?>" class="btn btn-outline-danger btn-sm d-block mt-1" 
                                   onclick="return confirm('Cancel this session?')">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No upcoming sessions.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Past Sessions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-history"></i> Past Sessions
                </div>
                <div class="card-body">
                    <?php if (!empty($past)): ?>
                        <?php foreach ($past as $session): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($session['title']); ?></h6>
                                <small class="text-muted">with <?php echo htmlspecialchars($session['mentor_name']); ?></small>
                                <br><small class="text-muted"><i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?></small>
                            </div>
                            <div class="text-end">
                                <?php if ($session['recording_url']): ?>
                                    <a href="<?php echo $session['recording_url']; ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-play"></i> Recording
                                    </a>
                                <?php endif; ?>
                                <?php if (!$session['user_rating'] && $session['status'] === 'completed'): ?>
                                    <button class="btn btn-sm btn-warning" onclick="rateSession(<?php echo $session['id']; ?>)">
                                        <i class="fas fa-star"></i> Rate
                                    </button>
                                <?php elseif ($session['user_rating']): ?>
                                    <span class="badge bg-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $session['user_rating'] ? '' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No past sessions.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Cancelled Sessions -->
            <?php if (!empty($cancelled)): ?>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-times-circle"></i> Cancelled Sessions
                </div>
                <div class="card-body">
                    <?php foreach ($cancelled as $session): ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($session['title']); ?></h6>
                            <small class="text-muted">with <?php echo htmlspecialchars($session['mentor_name']); ?></small>
                            <br><small class="text-muted"><i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?></small>
                        </div>
                        <span class="badge bg-danger">Cancelled</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function rateSession(bookingId) {
    const rating = prompt('Rate this session (1-5 stars):');
    if (rating && rating >= 1 && rating <= 5) {
        window.location.href = 'rate.php?booking=' + bookingId + '&rating=' + rating;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>