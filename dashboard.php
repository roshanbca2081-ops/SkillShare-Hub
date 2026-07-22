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
<main class="page-shell dashboard-shell">
  <div class="dashboard-bg-logo" aria-hidden="true"></div>
  <section class="container">
    <div class="app-layout">
      <aside class="sidebar-nav">
        <div class="sidebar-brand"><span class="site-logo site-logo--sidebar" aria-hidden="true"></span></div>
        <a class="active" href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <?php if ($role === 'fresher'): ?>
        <a href="fresher/fields.php"><i class="fa-solid fa-layer-group"></i> Fields</a>
        <a href="course.php"><i class="fa-solid fa-book-open"></i> Courses</a>
        <a href="fresher/assignments/index.php"><i class="fa-solid fa-file-pen"></i> Assignments</a>
        <a href="fresher/mentorship/book.php"><i class="fa-solid fa-video"></i> Book Session</a>
        <a href="fresher/research/index.php"><i class="fa-solid fa-microscope"></i> Research</a>
        <a href="fresher/placement/index.php"><i class="fa-solid fa-briefcase"></i> Placement</a>
        <a href="fresher/certificates/index.php"><i class="fa-solid fa-award"></i> Certificates</a>
        <?php elseif ($role === 'graduate'): ?>
        <a href="graduate/video-sessions/index.php"><i class="fa-solid fa-video"></i> Sessions</a>
        <a href="graduate/assignments/index.php"><i class="fa-solid fa-file-pen"></i> Assignments</a>
        <a href="graduate/earnings/index.php"><i class="fa-solid fa-wallet"></i> Earnings</a>
        <a href="graduate/messages/index.php"><i class="fa-solid fa-message"></i> Messages</a>
        <a href="graduate/research/index.php"><i class="fa-solid fa-microscope"></i> Research</a>
        <?php else: ?>
        <a href="admin/users/index.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="admin/courses/index.php"><i class="fa-solid fa-book"></i> Courses</a>
        <a href="admin/assignments/index.php"><i class="fa-solid fa-file"></i> Assignments</a>
        <a href="admin/payments/index.php"><i class="fa-solid fa-credit-card"></i> Payments</a>
        <a href="admin/reports/index.php"><i class="fa-solid fa-chart-bar"></i> Reports</a>
        <?php endif; ?>
        <a href="notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
        <hr style="border-color:rgba(255,255,255,.08);margin:10px 0;">
        <a href="logout.php" style="color:var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </aside>

      <div>
        <div class="dashboard-hero card">
          <div class="dashboard-hero-grid">
            <div>
              <p class="auth-eyebrow"><?php echo ucfirst($role); ?> Dashboard</p>
              <h2>Welcome, <?php echo e($_SESSION['user_name'] ?? 'User'); ?>!</h2>
              <p class="text-light-emphasis">Track your progress, manage sessions, and stay on top of your learning journey.</p>
            </div>
            <div class="dashboard-hero-actions">
              <span class="pill"><i class="fa-regular fa-user"></i> <?php echo ucfirst($role); ?></span>
              <span class="pill"><i class="fa-regular fa-book-open"></i> <?php echo e($counts['courses']); ?> Courses</span>
              <a class="btn btn--primary btn-sm" href="<?php echo $target; ?>">Full Dashboard</a>
            </div>
          </div>
        </div>

        <div class="stat-grid">
          <div class="stat-card">
            <div class="stat-icon"><i class="fa-regular fa-book-open"></i></div>
            <div><strong><?php echo e($counts['courses']); ?></strong><span>Courses</span></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
            <div><strong><?php echo e($counts['sessions']); ?></strong><span>Sessions</span></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon"><i class="fa-regular fa-file-lines"></i></div>
            <div><strong><?php echo e($counts['assignments']); ?></strong><span>Assignments</span></div>
          </div>
          <div class="stat-card">
            <div class="stat-icon"><i class="fa-regular fa-rectangle-list"></i></div>
            <div><strong><?php echo e($counts['users']); ?></strong><span><?php echo $role === 'admin' ? 'Users' : 'Active'; ?></span></div>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-lg-7">
            <div class="card">
              <div class="section-heading" style="margin-top:0;">
                <h3>Recent Activity</h3>
                <a href="notifications.php">View all</a>
              </div>
              <div class="list-group">
                <div class="list-group-item">
                  <div style="display:flex;justify-content:space-between;">
                    <span><strong>Welcome to ShareSkill Hub</strong></span>
                    <span class="tag">New</span>
                  </div>
                  <p class="small text-light-emphasis" style="margin:4px 0 0;">Explore courses and book your first mentorship session.</p>
                </div>
                <div class="list-group-item">
                  <div style="display:flex;justify-content:space-between;">
                    <span><strong>Complete Your Profile</strong></span>
                    <span class="tag tag--orange">Pending</span>
                  </div>
                  <p class="small text-light-emphasis" style="margin:4px 0 0;">Add your academic field and skills to get personalized recommendations.</p>
                </div>
                <div class="list-group-item">
                  <div style="display:flex;justify-content:space-between;">
                    <span><strong>Browse Available Mentors</strong></span>
                    <span class="tag tag--green">Active</span>
                  </div>
                  <p class="small text-light-emphasis" style="margin:4px 0 0;">Find mentors in your field and book a session.</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card">
              <div class="section-heading" style="margin-top:0;">
                <h3>Quick Actions</h3>
              </div>
              <div class="d-grid gap-2">
                <?php if ($role === 'fresher'): ?>
                  <a class="btn btn--outline" href="fresher/mentorship/book.php"><i class="fa-solid fa-video"></i> Book a Session</a>
                  <a class="btn btn--outline" href="fresher/assignments/index.php"><i class="fa-solid fa-file-arrow-up"></i> Submit Assignment</a>
                  <a class="btn btn--outline" href="fresher/placement/index.php"><i class="fa-solid fa-briefcase"></i> Placement Prep</a>
                <?php elseif ($role === 'graduate'): ?>
                  <a class="btn btn--outline" href="graduate/assignments/create.php"><i class="fa-solid fa-plus"></i> Create Assignment</a>
                  <a class="btn btn--outline" href="graduate/availability/index.php"><i class="fa-solid fa-calendar"></i> Set Availability</a>
                  <a class="btn btn--outline" href="graduate/earnings/index.php"><i class="fa-solid fa-wallet"></i> View Earnings</a>
                <?php else: ?>
                  <a class="btn btn--outline" href="admin/users/index.php"><i class="fa-solid fa-users-gear"></i> Manage Users</a>
                  <a class="btn btn--outline" href="admin/courses/index.php"><i class="fa-solid fa-book"></i> Manage Courses</a>
                  <a class="btn btn--outline" href="admin/reports/index.php"><i class="fa-solid fa-chart-line"></i> View Reports</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
