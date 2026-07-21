<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

ensure_database_schema();
require_login('admin');

$counts = get_dashboard_counts();
$recentAssignments = get_assignments();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<main class="page-shell dashboard-shell">
  <div class="dashboard-bg-logo" aria-hidden="true"></div>
  <section class="container">
    <div class="app-layout">
      <aside class="sidebar-nav">
        <div class="sidebar-brand"><span class="site-logo site-logo--sidebar" aria-hidden="true"></span></div>
        <a class="active" href="dashboard.php">Dashboard</a>
        <a href="users/index.php">Users</a>
        <a href="graduates/index.php">Mentors</a>
        <a href="courses/index.php">Courses</a>
        <a href="fields/index.php">Fields</a>
        <a href="assignments/index.php">Assignments</a>
        <a href="payments/index.php">Payments</a>
        <a href="reports/index.php">Reports</a>
        <a href="settings/index.php">Settings</a>
      </aside>
      <div>
        <div class="page-title">
          <div>
            <h1>Admin Dashboard</h1>
            <p>Manage users, mentors, courses, assignments, payments and reports.</p>
          </div>
        </div>
        <div class="stat-grid">
          <div class="stat-card"><div class="stat-icon">U</div><div><strong><?php echo e($counts['users']); ?></strong><span>Users</span></div></div>
          <div class="stat-card"><div class="stat-icon">C</div><div><strong><?php echo e($counts['courses']); ?></strong><span>Courses</span></div></div>
          <div class="stat-card"><div class="stat-icon">A</div><div><strong><?php echo e($counts['assignments']); ?></strong><span>Assignments</span></div></div>
          <div class="stat-card"><div class="stat-icon">S</div><div><strong><?php echo e($counts['sessions']); ?></strong><span>Sessions</span></div></div>
        </div>
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="card">
              <h3>Recent Assignments</h3>
              <div class="list-group">
                <?php foreach (array_slice($recentAssignments, 0, 5) as $assignment): ?>
                  <div class="list-group-item"><strong><?php echo e($assignment['title']); ?></strong><div class="small text-light-emphasis"><?php echo e($assignment['deadline'] ?: 'No deadline'); ?></div></div>
                <?php endforeach; ?>
                <?php if (!$recentAssignments): ?>
                  <div class="list-group-item">No assignments yet.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card">
              <h3>Quick Actions</h3>
              <div class="d-grid gap-2">
                <a class="btn btn--outline" href="users/index.php">Manage Users</a>
                <a class="btn btn--outline" href="courses/index.php">Manage Courses</a>
                <a class="btn btn--outline" href="assignments/index.php">Manage Assignments</a>
                <a class="btn btn--outline" href="payments/index.php">Payments</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../includes/footer.php'; ?>
