<?php include 'config/config.php'; include 'includes/functions.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<div class="container py-5">
  <div class="card form-card animate">
    <h2 class="text-center mb-3"><i class="fa-solid fa-key me-2"></i>Forgot Password</h2>
    <p class="text-center text-light-emphasis mb-4">Enter your email and we will guide you through the reset steps.</p>
    <form>
      <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" class="form-control" placeholder="you@example.com" />
      </div>
      <button class="btn btn-primary w-100">Send Reset Link</button>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
