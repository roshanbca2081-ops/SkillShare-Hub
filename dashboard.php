<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

ensure_database_schema();
require_login();

$role = current_user_role();
$counts = get_dashboard_counts();
$target = $role === 'admin' ? 'admin/dashboard.php' : ($role === 'graduate' ? 'graduate/dashboard.php' : 'fresher/dashboard.php');
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container">
    <div class="app-layout">
      <aside class="sidebar-nav">
        <div class="sidebar-brand">SkillShare Hub</div>
        <a class="active" href="dashboard.php">Dashboard</a>
        <a href="course.php">My Courses</a>
        <a href="fresher/assignments/index.php">Assignments</a>
        <a href="notifications.php">Notifications</a>
        <a href="mentor.php">Mentorship</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
      </aside>
      <div>
        <div class="page-title">
          <div>
            <h1>Welcome, <?php echo e($_SESSION['user_name'] ?? 'Learner'); ?></h1>
            <p>Here is what's happening today in your <?php echo e($role); ?> workspace.</p>
          </div>
          <a class="btn btn-primary" href="<?php echo $target; ?>">Open <?php echo e(ucfirst($role)); ?> Panel</a>
        </div>
        <div class="stat-grid">
          <div class="stat-card"><div class="stat-icon">C</div><div><strong><?php echo e($counts['courses']); ?></strong><span>Enrolled Courses</span></div></div>
          <div class="stat-card"><div class="stat-icon">S</div><div><strong><?php echo e($counts['sessions']); ?></strong><span>Upcoming Sessions</span></div></div>
          <div class="stat-card"><div class="stat-icon">A</div><div><strong><?php echo e($counts['assignments']); ?></strong><span>Assignments</span></div></div>
          <div class="stat-card"><div class="stat-icon">P</div><div><strong>78%</strong><span>Progress</span></div></div>
        </div>
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="card">
              <h3>My Courses</h3>
              <div class="course-grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
                <article class="soft-card"><strong>Full Stack Web Development</strong><p class="small text-light-emphasis">60% complete</p><span class="tag">60%</span></article>
                <article class="soft-card"><strong>Mobile App Development</strong><p class="small text-light-emphasis">30% complete</p><span class="tag">30%</span></article>
                <article class="soft-card"><strong>Database Management</strong><p class="small text-light-emphasis">80% complete</p><span class="tag">80%</span></article>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card">
              <h3>Upcoming Sessions</h3>
              <div class="meta-list">
                <span><strong>Full Stack Web Dev</strong> - 07:00 PM with Anany Sharma</span>
                <span><strong>Mobile App Dev</strong> - 06:00 PM with Ram Thapa</span>
              </div>
              <a class="btn btn--outline btn-sm" href="fresher/mentorship/book.php">View All Sessions</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
