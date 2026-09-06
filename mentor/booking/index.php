<?php
$page_title = 'Booking Requests';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'pending';

$query = "SELECT b.*, s.title as session_title, s.scheduled_at, s.duration, s.meeting_link,
          u.id as student_id, u.full_name as student_name, u.email, u.avatar, u.phone,
          c.id as course_id, c.title as course_title
          FROM bookings b 
          JOIN sessions s ON b.session_id = s.id 
          JOIN users u ON b.fresher_id = u.id 
          LEFT JOIN courses c ON s.course_id = c.id 
          WHERE s.mentor_id = ?";

$params = [$user_id];

if ($filter === 'pending') {
    $query .= " AND b.status = 'pending'";
} elseif ($filter === 'approved') {
    $query .= " AND b.status = 'approved'";
} elseif ($filter === 'completed') {
    $query .= " AND b.status = 'completed'";
} elseif ($filter === 'cancelled') {
    $query .= " AND b.status = 'cancelled'";
} elseif ($filter === 'rejected') {
    $query .= " AND b.status = 'rejected'";
}

$query .= " ORDER BY b.booking_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

// Get counts
$counts = [
    'pending' => 0,
    'approved' => 0,
    'completed' => 0,
    'cancelled' => 0,
    'rejected' => 0
];

foreach ($bookings as $b) {
    if (isset($counts[$b['status']])) {
        $counts[$b['status']]++;
    }
}
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
                <h1 class="h2">Booking Requests</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-warning me-2">
                        <i class="fas fa-clock"></i> <?php echo $counts['pending']; ?> Pending
                    </span>
                </div>
            </div>
            
            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'pending' ? 'active' : ''; ?>" href="?filter=pending">
                        Pending <span class="badge bg-warning"><?php echo $counts['pending']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'approved' ? 'active' : ''; ?>" href="?filter=approved">
                        Approved <span class="badge bg-success"><?php echo $counts['approved']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'completed' ? 'active' : ''; ?>" href="?filter=completed">
                        Completed <span class="badge bg-primary"><?php echo $counts['completed']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'rejected' ? 'active' : ''; ?>" href="?filter=rejected">
                        Rejected <span class="badge bg-danger"><?php echo $counts['rejected']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'cancelled' ? 'active' : ''; ?>" href="?filter=cancelled">
                        Cancelled <span class="badge bg-secondary"><?php echo $counts['cancelled']; ?></span>
                    </a>
                </li>
            </ul>
            
            <!-- Bookings List -->
            <?php if (!empty($bookings)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Session</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo getAvatar($booking); ?>" 
                                                     class="rounded-circle me-2" style="width: 32px; height: 32px;">
                                                <div>
                                                    <div><?php echo htmlspecialchars($booking['student_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($booking['email']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($booking['session_title']); ?></strong>
                                            <?php if ($booking['course_title']): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($booking['course_title']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar"></i> <?php echo formatDateTime($booking['scheduled_at']); ?>
                                            <br><i class="fas fa-clock"></i> <?php echo $booking['duration']; ?> min
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $booking['status'] === 'pending' ? 'warning' : 
                                                    ($booking['status'] === 'approved' ? 'success' : 
                                                    ($booking['status'] === 'completed' ? 'primary' : 
                                                    ($booking['status'] === 'rejected' ? 'danger' : 'secondary'))); 
                                            ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="view.php?id=<?php echo $booking['id']; ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($booking['status'] === 'pending'): ?>
                                                    <a href="approve.php?id=<?php echo $booking['id']; ?>" class="btn btn-success">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="reject.php?id=<?php echo $booking['id']; ?>" class="btn btn-danger" 
                                                       onclick="return confirm('Reject this booking?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5>No bookings found</h5>
                    <p class="text-muted">You have no <?php echo $filter; ?> bookings.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>