<div class="card p-3 mb-4">
  <h5 class="mb-3">Quick Links</h5>
  <ul class="list-unstyled">
    <li><a href="dashboard.php"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
    <li><a href="notifications.php"><i class="fa-solid fa-bell me-2"></i>Notifications</a></li>
    <li><a href="settings.php"><i class="fa-solid fa-gear me-2"></i>Settings</a></li>
    <?php if (current_user_role() === 'fresher'): ?>
      <li><a href="fresher/placement/index.php"><i class="fa-solid fa-briefcase me-2"></i>Placement Hub</a></li>
      <li><a href="fresher/messages/index.php"><i class="fa-solid fa-envelope me-2"></i>Messages</a></li>
    <?php elseif (current_user_role() === 'graduate'): ?>
      <li><a href="graduate/messages/index.php"><i class="fa-solid fa-envelope me-2"></i>Messages</a></li>
    <?php elseif (current_user_role() === 'admin'): ?>
      <li><a href="admin/payments/index.php"><i class="fa-solid fa-money-bill me-2"></i>Payments</a></li>
    <?php endif; ?>
  </ul>
</div>
