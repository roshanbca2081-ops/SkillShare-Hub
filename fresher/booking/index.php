<?php
$page_title = 'My Bookings';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';

// Build query based on filter
$query = "SELECT b.*, s.id as session_id, s.title, s.description, s.scheduled_at, s.duration, 
          s.meeting_link, s.recording_url, s.status as session_status,
          u.full_name as mentor_name, u.id as mentor_id, u.avatar,
          c.id as course_id, c.title as course_title
          FROM bookings b 
          JOIN sessions s ON b.session_id = s.id 
          JOIN users u ON s.mentor_id = u.id 
          LEFT JOIN courses c ON s.course_id = c.id 
          WHERE b.fresher_id = ?";

$params = [$user_id];

switch ($filter) {
    case 'upcoming':
        $query .= " AND b.status = 'approved' AND s.scheduled_at > NOW()";
        break;
    case 'pending':
        $query .= " AND b.status = 'pending'";
        break;
    case 'completed':
        $query .= " AND (b.status = 'completed' OR s.scheduled_at < NOW())";
        break;
    case 'cancelled':
        $query .= " AND b.status = 'cancelled'";
        break;
    case 'rejected':
        $query .= " AND b.status = 'rejected'";
        break;
    default:
        // All bookings
        break;
}

$query .= " ORDER BY s.scheduled_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

// Get counts for each status
$counts = [
    'all' => count($bookings),
    'upcoming' => 0,
    'pending' => 0,
    'completed' => 0,
    'cancelled' => 0,
    'rejected' => 0
];

foreach ($bookings as $booking) {
    if ($booking['status'] === 'pending') $counts['pending']++;
    elseif ($booking['status'] === 'cancelled') $counts['cancelled']++;
    elseif ($booking['status'] === 'rejected') $counts['rejected']++;
    elseif ($booking['status'] === 'completed' || strtotime($booking['scheduled_at']) < time()) {
        $counts['completed']++;
    } elseif ($booking['status'] === 'approved' && strtotime($booking['scheduled_at']) > time()) {
        $counts['upcoming']++;
    }
}

// Handle cancellation
if (isset($_GET['cancel']) && isset($_GET['id'])) {
    $booking_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND fresher_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    
    // Get session details for notification
    $stmt = $pdo->prepare("SELECT s.id as session_id, s.title, u.id as mentor_id 
                           FROM bookings b 
                           JOIN sessions s ON b.session_id = s.id 
                           JOIN users u ON s.mentor_id = u.id 
                           WHERE b.id = ?");
    $stmt->execute([$booking_id]);
    $booking_data = $stmt->fetch();
    
    if ($booking_data) {
        // Notify mentor
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) 
                               VALUES (?, 'booking_cancelled', 'Booking Cancelled', 
                                       CONCAT(?, ' has cancelled the session: ', ?),
                                       'mentor/bookings/view.php?id=' || ?)");
        $stmt->execute([
            $booking_data['mentor_id'],
            getUserName(),
            $booking_data['title'],
            $booking_data['session_id']
        ]);
    }
    
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Booking cancelled successfully.'
    ];
    redirect('index.php');
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
                <h1 class="h2">My Bookings</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Booking
                    </a>
                </div>
            </div>
            
            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'all' ? 'active' : ''; ?>" href="?filter=all">
                        All <span class="badge bg-secondary"><?php echo $counts['all']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'upcoming' ? 'active' : ''; ?>" href="?filter=upcoming">
                        Upcoming <span class="badge bg-success"><?php echo $counts['upcoming']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'pending' ? 'active' : ''; ?>" href="?filter=pending">
                        Pending <span class="badge bg-warning"><?php echo $counts['pending']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'completed' ? 'active' : ''; ?>" href="?filter=completed">
                        Completed <span class="badge bg-info"><?php echo $counts['completed']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'cancelled' ? 'active' : ''; ?>" href="?filter=cancelled">
                        Cancelled <span class="badge bg-danger"><?php echo $counts['cancelled']; ?></span>
                    </a>
                </li>
            </ul>
            
            <!-- Bookings List -->
            <?php if (!empty($bookings)): ?>
                <div class="row g-4">
                    <?php foreach ($bookings as $booking): ?>
                        <?php
                        $is_upcoming = $booking['status'] === 'approved' && strtotime($booking['scheduled_at']) > time();
                        $is_pending = $booking['status'] === 'pending';
                        $is_completed = $booking['status'] === 'completed' || strtotime($booking['scheduled_at']) < time();
                        $is_cancelled = $booking['status'] === 'cancelled';
                        $is_rejected = $booking['status'] === 'rejected';
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                <?php if ($is_upcoming): ?>
                                    <div class="card-header bg-success text-white">
                                        <i class="fas fa-calendar-check"></i> Upcoming
                                    </div>
                                <?php elseif ($is_pending): ?>
                                    <div class="card-header bg-warning text-dark">
                                        <i class="fas fa-clock"></i> Pending Approval
                                    </div>
                                <?php elseif ($is_completed): ?>
                                    <div class="card-header bg-secondary text-white">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </div>
                                <?php elseif ($is_cancelled): ?>
                                    <div class="card-header bg-danger text-white">
                                        <i class="fas fa-times-circle"></i> Cancelled
                                    </div>
                                <?php elseif ($is_rejected): ?>
                                    <div class="card-header bg-danger text-white">
                                        <i class="fas fa-times-circle"></i> Rejected
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5><?php echo htmlspecialchars($booking['title']); ?></h5>
                                    <p class="text-muted small"><?php echo substr($booking['description'] ?? '', 0, 100); ?>...</p>
                                    
                                    <div class="booking-meta">
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="<?php echo getAvatar($booking); ?>" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                            <span><?php echo htmlspecialchars($booking['mentor_name']); ?></span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-calendar"></i> <?php echo formatDateTime($booking['scheduled_at']); ?>
                                            </span>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-clock"></i> <?php echo $booking['duration']; ?> min
                                            </span>
                                            <?php if ($booking['course_title']): ?>
                                                <span class="badge bg-light text-dark border">
                                                    <i class="fas fa-book"></i> <?php echo htmlspecialchars($booking['course_title']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="booking-status mt-2">
                                            <span class="badge bg-<?php 
                                                echo $is_upcoming ? 'success' : 
                                                    ($is_pending ? 'warning' : 
                                                    ($is_completed ? 'secondary' : 'danger')); 
                                            ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                            <?php if ($booking['status'] === 'pending'): ?>
                                                <small class="text-muted">Waiting for mentor approval</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <div class="d-grid gap-2">
                                        <a href="view.php?id=<?php echo $booking['id']; ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        
                                        <?php if ($is_upcoming && $booking['meeting_link']): ?>
                                            <a href="<?php echo $booking['meeting_link']; ?>" target="_blank" class="btn btn-success">
                                                <i class="fas fa-video"></i> Join Session
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($is_pending): ?>
                                            <a href="?cancel=1&id=<?php echo $booking['id']; ?>" class="btn btn-outline-danger" 
                                               onclick="return confirm('Cancel this booking request?')">
                                                <i class="fas fa-times"></i> Cancel Request
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($is_upcoming): ?>
                                            <a href="?cancel=1&id=<?php echo $booking['id']; ?>" class="btn btn-outline-danger" 
                                               onclick="return confirm('Cancel this booking?')">
                                                <i class="fas fa-times"></i> Cancel Booking
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($is_completed && !$booking['rating']): ?>
                                            <button class="btn btn-warning" onclick="rateBooking(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-star"></i> Rate Session
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5>No bookings found</h5>
                    <p class="text-muted">You haven't made any bookings yet.</p>
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Book a Session
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function rateBooking(bookingId) {
    const rating = prompt('Rate this session (1-5 stars):');
    if (rating && rating >= 1 && rating <= 5) {
        window.location.href = 'rate.php?booking=' + bookingId + '&rating=' + rating;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>