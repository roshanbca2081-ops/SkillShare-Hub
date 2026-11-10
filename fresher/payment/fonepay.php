<?php
$page_title = 'Fonepay Unavailable';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireFresher();
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>
<div class="container py-5"><div class="row justify-content-center"><div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-body p-5 text-center">
    <i class="fas fa-circle-exclamation fa-3x text-warning" aria-hidden="true"></i>
    <h1 class="h3 mt-3">Fonepay checkout is unavailable</h1>
    <p class="text-muted">Fonepay verification has not been integrated. No payment will be recorded from a manually entered reference. Use eSewa or Khalti from checkout instead.</p>
    <a class="btn btn-primary" href="../sessions/index.php">Browse sessions</a>
</div></div></div></div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
