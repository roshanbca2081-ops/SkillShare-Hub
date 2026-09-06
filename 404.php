<?php
$page_title = 'Page Not Found';
require_once 'config/session.php';
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center text-center">
        <div class="col-md-6">
            <div class="display-1 text-muted">404</div>
            <h1 class="display-4 fw-bold mb-3">Page Not Found</h1>
            <p class="lead text-muted mb-4">Oops! The page you're looking for doesn't exist.</p>
            <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Go Home</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>