<?php
$page_title = 'Payment Success';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h2>Payment Successful!</h2>
                    <p class="text-muted">Thank you for your purchase. Your payment has been processed successfully.</p>
                    
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle"></i> 
                        <?php if ($session_id): ?>
                            You can now join the session.
                        <?php elseif ($course_id): ?>
                            You can now access the course.
                        <?php else: ?>
                            You can now access your purchased content.
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <?php if ($session_id): ?>
                            <a href="../sessions/my-sessions.php" class="btn btn-primary">View My Sessions</a>
                        <?php elseif ($course_id): ?>
                            <a href="../learning/my-courses.php" class="btn btn-primary">Go to My Courses</a>
                        <?php else: ?>
                            <a href="../dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                        <?php endif; ?>
                        <a href="index.php" class="btn btn-outline-secondary">View Payment History</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>