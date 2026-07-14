<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
ensure_database_schema();
require_login('fresher');
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container">
    <div class="app-layout">
      <aside class="sidebar-nav">
        <div class="sidebar-brand">SkillShare Hub</div>
        <a class="active" href="dashboard.php">Dashboard</a>
        <a href="enrolled-courses/index.php">My Courses</a>
        <a href="assignments/index.php">Assignments</a>
        <a href="../notifications.php">Notifications</a>
        <a href="mentorship/book.php">Mentorship</a>
        <a href="research/index.php">Research</a>
        <a href="interview/index.php">Interview Prep</a>
        <a href="placement/index.php">Placement Prep</a>
      </aside>
      <div>
        <div class="page-title"><div><h1>Welcome, <?php echo e($_SESSION['user_name'] ?? 'Fresher'); ?></h1><p>Keep learning and grow your skills.</p></div></div>
        <div class="stat-grid">
          <div class="stat-card"><div class="stat-icon">3</div><div><strong>3</strong><span>Enrolled Courses</span></div></div>
          <div class="stat-card"><div class="stat-icon">2</div><div><strong>2</strong><span>Upcoming Sessions</span></div></div>
          <div class="stat-card"><div class="stat-icon">4</div><div><strong>4</strong><span>Pending Assignments</span></div></div>
          <div class="stat-card"><div class="stat-icon">78</div><div><strong>78%</strong><span>Overall Progress</span></div></div>
        </div>
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="card">
              <h3>My Courses</h3>
              <div class="course-grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
                <article class="soft-card"><strong>Full Stack Web Development</strong><p class="small text-light-emphasis">60%</p><span class="tag">Continue</span></article>
                <article class="soft-card"><strong>Mobile App Development</strong><p class="small text-light-emphasis">30%</p><span class="tag">Continue</span></article>
                <article class="soft-card"><strong>Database Management</strong><p class="small text-light-emphasis">80%</p><span class="tag">Continue</span></article>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card">
              <h3>Upcoming Sessions</h3>
              <div class="meta-list">
                <span><strong>24 May</strong> Full Stack Web Dev with Anany Sharma</span>
                <span><strong>25 May</strong> Mobile App Dev with Ram Thapa</span>
              </div>
              <a class="btn btn-primary btn-sm" href="mentorship/book.php">Book New Session</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../includes/footer.php'; ?>
