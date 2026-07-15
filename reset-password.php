<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<link rel="stylesheet" href="assets/css/auth-premium.css" />
<main class="page-shell">
  <section class="page-section centered" style="min-height:calc(100vh - 120px);padding:50px 0;">
    <div class="form-card card animate" style="max-width:520px;margin:auto;">
      <div class="text-center mb-4">
        <div class="auth-logo" style="margin-inline:auto;margin-bottom:18px;width:64px;height:64px;">SH</div>
        <h2 class="mb-1">Password Reset</h2>
        <p class="text-light-emphasis">Please use the Forgot Password page to securely reset your account password.</p>
      </div>
      <div class="mb-4">
        <p class="text-light-emphasis">To continue, request a one-time OTP and update your password on the reset flow.</p>
      </div>
      <div class="d-grid gap-3">
        <a href="forgot-password.php" class="btn btn--primary">Go to Forgot Password</a>
        <a href="login.php" class="btn btn--outline">Back to Login</a>
      </div>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
