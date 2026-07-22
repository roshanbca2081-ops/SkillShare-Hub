<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
ensure_database_schema();
require_login('graduate');
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container py-5">
    <div class="card p-4" style="max-width:700px;margin:0 auto;">
      <div class="page-title">
        <div>
          <h1>Graduate Settings</h1>
          <p>Manage notifications, password, and account preferences.</p>
        </div>
      </div>
      <div class="row g-4">
        <div class="col-md-6">
          <label class="form-label">Full Name</label>
          <input class="form-control" value="<?php echo e($_SESSION['user_name'] ?? ''); ?>" disabled />
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input class="form-control" value="<?php echo e($_SESSION['user_email'] ?? ''); ?>" disabled />
        </div>
        <div class="col-12">
          <label class="form-label">Notifications</label>
          <div class="soft-card">Email alerts for session requests, assignments, and earnings.</div>
        </div>
      </div>
      <div class="mt-4 d-flex gap-3 flex-wrap">
        <a class="btn btn--outline" href="../dashboard.php">Back to Dashboard</a>
        <a class="btn btn--primary" href="../logout.php">Sign Out</a>
      </div>
    </div>
  </section>
</main>
<?php include '../includes/footer.php'; ?>
