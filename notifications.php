<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

ensure_database_schema();
require_login();

$notifications = get_notifications($_SESSION['user_id']);
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-lg-3">
      <?php include 'includes/sidebar.php'; ?>
    </div>
    <div class="col-lg-9">
      <div class="card p-4 animate">
        <h3 class="mb-3">Notifications</h3>
        <p class="text-light-emphasis">Stay updated with session reminders, assignment deadlines, and account activity.</p>
        <ul class="list-group list-group-flush">
          <?php if ($notifications): ?>
            <?php foreach ($notifications as $notification): ?>
              <li class="list-group-item bg-transparent">
                <strong><?php echo e($notification['message']); ?></strong>
                <div class="small text-light-emphasis"><?php echo e($notification['created_at']); ?></div>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="list-group-item bg-transparent">No notifications yet.</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
