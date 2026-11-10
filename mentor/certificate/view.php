<?php
$page_title = 'Certificate Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireMentor();

$certificateId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT c.*, cr.title AS course_title, u.full_name AS mentor_name, s.full_name AS recipient_name
                       FROM certificates c
                       JOIN courses cr ON c.course_id = cr.id
                       JOIN users u ON cr.mentor_id = u.id
                       JOIN users s ON c.fresher_id = s.id
                       WHERE c.id = ? AND cr.mentor_id = ?
                       LIMIT 1");
$stmt->execute([$certificateId, getUserId()]);
$certificate = $stmt->fetch();
if (!$certificate) {
    $_SESSION['alert'] = ['type' => 'warning', 'icon' => 'exclamation-circle', 'message' => 'Certificate not found for your courses.'];
    redirect('index.php');
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>
<div class="container-fluid certificate-page">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0"><?php include '../../includes/mentor-sidebar.php'; ?></div>
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="certificate-actions d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div><h1 class="h2 mb-1">Certificate Details</h1><p class="text-muted mb-0">Issued to <?php echo htmlspecialchars($certificate['recipient_name'], ENT_QUOTES, 'UTF-8'); ?></p></div>
                <div class="d-flex gap-2">
                    <?php if ((int)$certificate['is_valid'] === 1): ?>
                        <a href="download.php?id=<?php echo (int)$certificate['id']; ?>" class="btn btn-primary"><i class="fas fa-download me-1"></i>Download PDF</a>
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()"><i class="fas fa-print me-1"></i>Print</button>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
            <?php if (!(int)$certificate['is_valid']): ?><div class="certificate-actions alert alert-danger">This certificate has been revoked and is not valid.</div><?php endif; ?>
            <?php if ((int)$certificate['is_valid'] === 1): ?>
                <?php include '../../includes/certificate-document.php'; ?>
            <?php endif; ?>
        </main>
    </div>
</div>
<style>
@media print {
    .navbar, .sidebar, .certificate-actions, footer, .role-dashboard-topbar { display:none !important; }
    .certificate-page, .certificate-page .row, .certificate-page main { display:block !important; width:100% !important; max-width:100% !important; margin:0 !important; padding:0 !important; }
    body { background:#fff !important; }
}
</style>
<?php include '../../includes/footer.php'; ?>
