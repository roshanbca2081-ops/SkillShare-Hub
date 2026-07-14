<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
ensure_database_schema();
require_login('graduate');
$bookings = get_mentorship_bookings(null, $_SESSION['user_id']);
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container">
    <div class="app-layout">
      <aside class="sidebar-nav">
        <div class="sidebar-brand">SkillShare Hub</div>
        <a class="active" href="dashboard.php">Dashboard</a>
        <a href="video-sessions/index.php">My Sessions</a>
        <a href="assignments/index.php">Assignments</a>
        <a href="messages/index.php">Messages</a>
        <a href="../notifications.php">Notifications</a>
        <a href="availability/index.php">Calendar</a>
        <a href="earnings/index.php">Earnings</a>
        <a href="profile.php">Profile</a>
      </aside>
      <div>
        <div class="page-title"><div><h1>Welcome, <?php echo e($_SESSION['user_name'] ?? 'Mentor'); ?></h1><p>Here is what's happening today.</p></div></div>
        <div class="stat-grid">
          <div class="stat-card"><div class="stat-icon">12</div><div><strong>12</strong><span>Upcoming Sessions</span></div></div>
          <div class="stat-card"><div class="stat-icon">45</div><div><strong>45</strong><span>Total Students</span></div></div>
          <div class="stat-card"><div class="stat-icon">8</div><div><strong>8</strong><span>Assignments</span></div></div>
          <div class="stat-card"><div class="stat-icon">Rs</div><div><strong>24,000</strong><span>Total Earnings</span></div></div>
        </div>
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="card">
              <h3>Upcoming Sessions</h3>
              <div class="meta-list">
                <?php foreach (array_slice($bookings, 0, 4) as $booking): ?>
                  <span><strong><?php echo e($booking['topic']); ?></strong> with <?php echo e($booking['student_name']); ?> <a class="btn btn--outline btn-sm" href="video-sessions/index.php">Join</a></span>
                <?php endforeach; ?>
                <?php if (!$bookings): ?>
                  <span><strong>Full Stack Web Development</strong> Today, 07:00 PM <a class="btn btn--outline btn-sm" href="video-sessions/index.php">Join</a></span>
                  <span><strong>API Development</strong> Tomorrow, 01:00 PM <a class="btn btn--outline btn-sm" href="video-sessions/index.php">Join</a></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card">
              <h3>Recent Notifications</h3>
              <div class="meta-list">
                <span>New booking by Rohan Sharma</span>
                <span>Session reminder today at 07:00 PM</span>
                <span>New assignment uploaded</span>
                <span>Payment received from Sita Magar</span>
              </div>
              <a class="btn btn--outline btn-sm" href="../notifications.php">View All Notifications</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../includes/footer.php'; ?>
