<?php
$page_title = 'Session Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$session_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT s.*, c.title as course_title,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'approved') as approved_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'pending') as pending_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'completed') as completed_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'cancelled') as cancelled_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND attended = 1) as attended_count
                       FROM sessions s 
                       LEFT JOIN courses c ON s.course_id = c.id 
                       WHERE s.id = ? AND s.mentor_id = ?");
$stmt->execute([$session_id, $user_id]);
$session = $stmt->fetch();

if (!$session) {
    redirect('index.php');
}

// Get bookings
$stmt = $pdo->prepare("SELECT b.*, u.full_name, u.email, u.avatar 
                       FROM bookings b 
                       JOIN users u ON b.fresher_id = u.id 
                       WHERE b.session_id = ? 
                       ORDER BY b.booking_date DESC");
$stmt->execute([$session_id]);
$bookings = $stmt->fetchAll();

// Get attendance
$stmt = $pdo->prepare("SELECT u.full_name, u.email, u.avatar,
                              b.booking_date AS joined_at, NULL AS left_at
                       FROM bookings b
                       JOIN users u ON b.fresher_id = u.id
                       WHERE b.session_id = ? AND b.attended = 1
                       ORDER BY b.booking_date DESC");
$stmt->execute([$session_id]);
$attendance = $stmt->fetchAll();
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
                <h1 class="h2">Session Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="edit.php?id=<?php echo $session_id; ?>" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Session Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3><?php echo htmlspecialchars($session['title']); ?></h3>
                                    <?php if ($session['course_title']): ?>
                                        <p class="text-muted">
                                            <i class="fas fa-book"></i> <?php echo htmlspecialchars($session['course_title']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-<?php 
                                    echo $session['status'] === 'ongoing' ? 'success' : 
                                        ($session['status'] === 'scheduled' ? 'primary' : 
                                        ($session['status'] === 'completed' ? 'secondary' : 'danger')); 
                                ?> fs-6">
                                    <?php echo ucfirst($session['status']); ?>
                                </span>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Date & Time</small>
                                        <p class="mb-0">
                                            <i class="fas fa-calendar text-primary"></i> 
                                            <?php echo formatDateTime($session['scheduled_at']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Duration</small>
                                        <p class="mb-0">
                                            <i class="fas fa-clock text-primary"></i> 
                                            <?php echo $session['duration']; ?> minutes
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Participants</small>
                                        <p class="mb-0">
                                            <i class="fas fa-users text-primary"></i> 
                                            <?php echo $session['attended_count']; ?> / <?php echo $session['max_participants']; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($session['description']): ?>
                            <div class="mt-3">
                                <h6>Description</h6>
                                <p><?php echo nl2br(htmlspecialchars($session['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($session['meeting_link']): ?>
                            <div class="mt-3">
                                <h6>Meeting Link</h6>
                                <a href="<?php echo $session['meeting_link']; ?>" target="_blank" class="btn btn-sm btn-success">
                                    <i class="fas fa-video"></i> Join Session
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Bookings -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-check"></i> Bookings 
                                <span class="badge bg-secondary"><?php echo count($bookings); ?> total</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($bookings)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Status</th>
                                            <th>Booked</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo getAvatar($booking); ?>" 
                                                         class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                                    <?php echo htmlspecialchars($booking['full_name']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $booking['status'] === 'pending' ? 'warning' : 
                                                        ($booking['status'] === 'approved' ? 'success' : 
                                                        ($booking['status'] === 'completed' ? 'primary' : 'danger')); 
                                                ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($booking['booking_date']); ?></td>
                                            <td>
                                                <a href="../booking/view.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No bookings yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Attendance -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">
                                <i class="fas fa-user-check"></i> Attendance 
                                <span class="badge bg-secondary"><?php echo count($attendance); ?> attended</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($attendance)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Joined</th>
                                            <th>Left</th>
                                            <th>Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attendance as $attend): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo getAvatar($attend); ?>" 
                                                         class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                                    <?php echo htmlspecialchars($attend['full_name']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo $attend['joined_at'] ? formatDateTime($attend['joined_at']) : '-'; ?></td>
                                            <td><?php echo $attend['left_at'] ? formatDateTime($attend['left_at']) : 'Still in session'; ?></td>
                                            <td>
                                                <?php 
                                                if ($attend['joined_at'] && $attend['left_at']) {
                                                    $diff = strtotime($attend['left_at']) - strtotime($attend['joined_at']);
                                                    echo floor($diff / 60) . ' min';
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No attendance records yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h6>Session Statistics</h6>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Total Bookings</span>
                                <strong><?php echo count($bookings); ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Approved</span>
                                <strong><?php echo $session['approved_count']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Pending</span>
                                <strong><?php echo $session['pending_count']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Completed</span>
                                <strong><?php echo $session['completed_count']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Cancelled</span>
                                <strong><?php echo $session['cancelled_count']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2">
                                <span class="text-muted">Attended</span>
                                <strong><?php echo $session['attended_count']; ?></strong>
                            </div>
                            
                            <hr>
                            
                            <div class="d-grid gap-2">
                                <?php if ($session['meeting_link']): ?>
                                    <a href="<?php echo $session['meeting_link']; ?>" target="_blank" class="btn btn-success">
                                        <i class="fas fa-video"></i> Join Session
                                    </a>
                                <?php endif; ?>
                                <a href="../booking/index.php?session=<?php echo $session_id; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-users"></i> Manage Bookings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>