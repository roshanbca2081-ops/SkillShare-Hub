<?php
$page_title = 'Payment Failed';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();
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
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-times-circle fa-5x text-danger"></i>
                    </div>
                    <h2>Payment Failed</h2>
                    <p class="text-muted">We couldn't process your payment. Please try again or use a different payment method.</p>
                    
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> 
                        <?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'An error occurred during payment processing.'; ?>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?php echo $_SERVER['HTTP_REFERER'] ?? '../dashboard.php'; ?>" class="btn btn-primary">Try Again</a>
                        <a href="index.php" class="btn btn-outline-secondary">View Payment History</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<?php include '../../includes/footer.php'; ?>