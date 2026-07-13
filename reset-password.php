<?php include 'config/config.php'; include 'includes/functions.php'; ?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<div class="container py-5">
  <div class="card form-card animate">
    <h2 class="text-center mb-3"><i class="fa-solid fa-lock me-2"></i>Reset Password</h2>
    <p class="text-center text-light-emphasis mb-4">Create a new secure password for your account.</p>
    <form>
      <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" class="form-control" placeholder="New password" />
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" class="form-control" placeholder="Confirm password" />
      </div>
      <button class="btn btn-primary w-100">Update Password</button>
    </form>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
