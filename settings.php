<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
ensure_database_schema();
require_login();
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container page-section">
    <div class="card card--padded animate" style="max-width:780px;margin:auto;">
      <div class="page-title">
        <div>
          <h1>Account Settings</h1>
          <p>Update your profile, notifications, and privacy preferences.</p>
        </div>
      </div>
      <div class="row g-4">
        <div class="col-md-6">
          <label class="form-label">Full Name</label>
          <input type="text" class="form-control" value="<?php echo e($_SESSION['user_name'] ?? ''); ?>" disabled />
        </div>
        <div class="col-md-6">
          <label class="form-label">Email Address</label>
          <input type="email" class="form-control" value="<?php echo e($_SESSION['user_email'] ?? ''); ?>" disabled />
        </div>
        <div class="col-12">
          <label class="form-label">Notifications</label>
          <div class="soft-card">Email alerts for assignments, session reminders, and important updates.</div>
        </div>
      </div>
      <div class="mt-4 d-flex gap-3 flex-wrap">
        <a href="dashboard.php" class="btn btn--outline">Back to Dashboard</a>
        <a href="logout.php" class="btn btn--primary">Sign Out</a>
      </div>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
