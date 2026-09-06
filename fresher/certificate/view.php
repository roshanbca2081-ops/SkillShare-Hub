<?php
$page_title = 'Certificate';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$certificate_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT c.*, cr.title as course_title, cr.thumbnail, u.full_name as mentor_name
                       FROM certificates c 
                       JOIN courses cr ON c.course_id = cr.id 
                       JOIN users u ON cr.mentor_id = u.id 
                       WHERE c.id = ? AND c.fresher_id = ? AND c.is_valid = 1");
$stmt->execute([$certificate_id, $user_id]);
$certificate = $stmt->fetch();

if (!$certificate) {
    redirect('index.php');
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-0">
                    <!-- Certificate Design -->
                    <div class="certificate-wrapper p-5 text-center" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px;">
                        <div class="certificate-border p-4" style="border: 3px double #6C63FF; border-radius: 8px; background: white;">
                            <div class="certificate-header mb-4">
                                <img src="../../assets/images/logo.png" alt="SkillShare Hub" style="height: 60px; margin-bottom: 16px;">
                                <h1 class="display-4 fw-bold text-primary">Certificate of Completion</h1>
                                <p class="text-muted">This certifies that</p>
                            </div>
                            
                            <div class="certificate-body py-3">
                                <h2 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h2>
                                <p class="lead">has successfully completed the course</p>
                                <h3 class="display-6 fw-bold text-primary mb-3"><?php echo htmlspecialchars($certificate['course_title']); ?></h3>
                                
                                <div class="row justify-content-center mt-4">
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between border-top pt-3">
                                            <div>
                                                <small class="text-muted">Date Issued</small>
                                                <p class="mb-0"><?php echo formatDate($certificate['issued_at']); ?></p>
                                            </div>
                                            <div>
                                                <small class="text-muted">Certificate Code</small>
                                                <p class="mb-0"><?php echo $certificate['certificate_code']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="certificate-footer mt-4 pt-3 border-top">
                                <div class="row">
                                    <div class="col-6 text-start">
                                        <div class="signature-area">
                                            <div class="signature-line" style="width: 150px; height: 2px; background: #333; margin-bottom: 4px;"></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($certificate['mentor_name']); ?></small>
                                            <br><small class="text-muted">Course Instructor</small>
                                        </div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <div class="seal-area">
                                            <div style="width: 60px; height: 60px; border: 3px solid #6C63FF; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #6C63FF;">
                                                <i class="fas fa-certificate fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 text-center">
                        <div class="d-flex justify-content-center gap-3">
                            <a href="download.php?id=<?php echo $certificate['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button class="btn btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                Verify this certificate at: 
                                <a href="verify.php?code=<?php echo $certificate['certificate_code']; ?>" target="_blank">
                                    <?php echo $_SERVER['HTTP_HOST']; ?>/verify.php?code=<?php echo $certificate['certificate_code']; ?>
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, .sidebar, .btn, .no-print, footer {
        display: none !important;
    }
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
    }
    .certificate-wrapper {
        border-radius: 0 !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<?php include '../../includes/footer.php'; ?>