<?php
$page_title = 'Booking Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = getUserId();

$stmt = $pdo->prepare("SELECT b.*, s.title as session_title, s.description, s.scheduled_at, s.duration, s.meeting_link,
                       u.id as student_id, u.full_name as student_name, u.email, u.avatar, u.phone, u.bio as student_bio,
                       c.id as course_id, c.title as course_title
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON b.fresher_id = u.id 
                       LEFT JOIN courses c ON s.course_id = c.id 
                       WHERE b.id = ? AND s.mentor_id = ?");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect('index.php');
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
                <h1 class="h2">Booking Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3><?php echo htmlspecialchars($booking['session_title']); ?></h3>
                                    <?php if ($booking['course_title']): ?>
                                        <p class="text-muted">
                                            <i class="fas fa-book"></i> 
                                            <?php echo htmlspecialchars($booking['course_title']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-<?php 
                                    echo $booking['status'] === 'pending' ? 'warning' : 
                                        ($booking['status'] === 'approved' ? 'success' : 
                                        ($booking['status'] === 'completed' ? 'primary' : 'danger')); 
                                ?> fs-6">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Date & Time</small>
                                        <p class="mb-0">
                                            <i class="fas fa-calendar text-primary"></i> 
                                            <?php echo formatDateTime($booking['scheduled_at']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Duration</small>
                                        <p class="mb-0">
                                            <i class="fas fa-clock text-primary"></i> 
                                            <?php echo $booking['duration']; ?> minutes
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($booking['description']): ?>
                            <div class="mt-3">
                                <h6>Session Description</h6>
                                <p><?php echo nl2br(htmlspecialchars($booking['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($booking['notes']): ?>
                            <div class="mt-3">
                                <h6>Student's Notes</h6>
                                <p class="bg-light p-3 rounded"><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Student Info -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>Student Information</h5>
                            <div class="d-flex align-items-start">
                                <img src="<?php echo getAvatar($booking); ?>" 
                                     class="rounded-circle me-3" style="width: 60px; height: 60px;">
                                <div>
                                    <h6><?php echo htmlspecialchars($booking['student_name']); ?></h6>
                                    <p class="text-muted small">
                                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($booking['email']); ?>
                                        <?php if ($booking['phone']): ?>
                                            <br><i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking['phone']); ?>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($booking['student_bio']): ?>
                                        <p class="small"><?php echo nl2br(htmlspecialchars($booking['student_bio'])); ?></p>
                                    <?php endif; ?>
                                    <div>
                                        <a href="../message/index.php?user_id=<?php echo $booking['student_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-envelope"></i> Send Message
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h5>Actions</h5>
                            
                            <?php if ($booking['status'] === 'pending'): ?>
                                <div class="d-grid gap-2">
                                    <a href="approve.php?id=<?php echo $booking['id']; ?>" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approve Booking
                                    </a>
                                    <a href="reject.php?id=<?php echo $booking['id']; ?>" class="btn btn-danger" 
                                       onclick="return confirm('Reject this booking?')">
                                        <i class="fas fa-times"></i> Reject Booking
                                    </a>
                                </div>
                            <?php elseif ($booking['status'] === 'approved'): ?>
                                <?php if ($booking['meeting_link']): ?>
                                    <a href="<?php echo $booking['meeting_link']; ?>" target="_blank" class="btn btn-success w-100 mb-2">
                                        <i class="fas fa-video"></i> Join Session
                                    </a>
                                <?php endif; ?>
                                <button class="btn btn-outline-secondary w-100" onclick="markCompleted(<?php echo $booking['id']; ?>)">
                                    <i class="fas fa-check"></i> Mark as Completed
                                </button>
                            <?php endif; ?>
                            
                            <hr>
                            
                            <h6>Booking Info</h6>
                            <ul class="list-unstyled small">
                                <li><strong>Booking ID:</strong> #<?php echo $booking['id']; ?></li>
                                <li><strong>Booked On:</strong> <?php echo formatDateTime($booking['booking_date']); ?></li>
                                <li><strong>Status:</strong> <?php echo ucfirst($booking['status']); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markCompleted(id) {
    if (confirm('Mark this booking as completed?')) {
        window.location.href = 'complete.php?id=' + id;
    }
}
</script>

<?php include '../../includes/footer.php'; ?>