<?php
$page_title = 'My Certificates';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT c.*, cr.title as course_title, cr.thumbnail, u.full_name as mentor_name
                       FROM certificates c 
                       JOIN courses cr ON c.course_id = cr.id 
                       JOIN users u ON cr.mentor_id = u.id 
                       WHERE c.fresher_id = ? AND c.is_valid = 1
                       ORDER BY c.issued_at DESC");
$stmt->execute([$user_id]);
$certificates = $stmt->fetchAll();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Certificates</h1>
            </div>
            
            <?php if (!empty($certificates)): ?>
                <div class="row g-4">
                    <?php foreach ($certificates as $cert): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-certificate fa-4x text-primary"></i>
                                </div>
                                <h5><?php echo htmlspecialchars($cert['course_title']); ?></h5>
                                <p class="text-muted small">Issued to <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                                <p class="text-muted small">
                                    <i class="fas fa-calendar"></i> <?php echo formatDate($cert['issued_at']); ?>
                                </p>
                                <div class="mb-2">
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Valid</span>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="view.php?id=<?php echo $cert['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> View Certificate
                                    </a>
                                    <a href="#" class="btn btn-outline-primary">
                                        <i class="fas fa-download"></i> Download PDF
                                    </a>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    Code: <?php echo $cert['certificate_code']; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                    <h5>No certificates yet</h5>
                    <p class="text-muted">Complete courses to earn certificates.</p>
                    <a href="../../public/courses.php" class="btn btn-primary">Browse Courses</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>