<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

ensure_database_schema();
require_login();

$role = current_user_role();
$counts = get_dashboard_counts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | ShareSkill Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand fw-bold" href="dashboard.php"><i class="fa-solid fa-graduation-cap me-2"></i>ShareSkill Hub</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto gap-2">
          <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <section class="section">
    <div class="container">
      <div class="card p-4 mb-4">
        <h2 class="mb-2">Welcome back, <?php echo e($_SESSION['user_name'] ?? 'there'); ?>!</h2>
        <p class="text-light-emphasis mb-0">You’re signed in as <strong><?php echo e($role); ?></strong> and can access the tools tailored to your account.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-4"><div class="card p-4 animate"><h5><i class="fa-solid fa-book-open me-2"></i>Courses</h5><p class="text-light-emphasis">Keep learning with curated practical content.</p><span class="badge bg-primary"><?php echo e($counts['courses']); ?> available</span></div></div>
        <div class="col-md-4"><div class="card p-4 animate"><h5><i class="fa-solid fa-calendar-check me-2"></i>Sessions</h5><p class="text-light-emphasis">Schedule and manage mentorship sessions.</p><span class="badge bg-success"><?php echo e($counts['sessions']); ?> active</span></div></div>
        <div class="col-md-4"><div class="card p-4 animate"><h5><i class="fa-solid fa-trophy me-2"></i>Achievements</h5><p class="text-light-emphasis">Track your growth and progress.</p><span class="badge bg-info"><?php echo e($counts['assignments']); ?> assignments</span></div></div>
      </div>
      <?php if ($role === 'admin'): ?>
        <div class="mt-4"><a class="btn btn-outline-light" href="admin/dashboard.php">Open admin panel</a></div>
      <?php elseif ($role === 'fresher'): ?>
        <div class="mt-4"><a class="btn btn-outline-light" href="fresher/dashboard.php">Open fresher panel</a></div>
      <?php else: ?>
        <div class="mt-4"><a class="btn btn-outline-light" href="graduate/dashboard.php">Open graduate panel</a></div>
      <?php endif; ?>
    </div>
  </section>
</body>
</html>
