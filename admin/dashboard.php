<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

ensure_database_schema();
require_login('admin');

$counts = get_dashboard_counts();
$users = get_users();
$recentAssignments = get_assignments();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4 mb-4">
      <h2>Admin Dashboard</h2>
      <p class="text-light-emphasis">Manage users, graduates, courses, assignments, payments, and reports.</p>
    </div>
    <div class="row g-4 mb-4">
      <div class="col-md-3"><div class="card p-4"><h5>Users</h5><h2><?php echo e($counts['users']); ?></h2></div></div>
      <div class="col-md-3"><div class="card p-4"><h5>Courses</h5><h2><?php echo e($counts['courses']); ?></h2></div></div>
      <div class="col-md-3"><div class="card p-4"><h5>Assignments</h5><h2><?php echo e($counts['assignments']); ?></h2></div></div>
      <div class="col-md-3"><div class="card p-4"><h5>Sessions</h5><h2><?php echo e($counts['sessions']); ?></h2></div></div>
    </div>
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card p-4">
          <h5 class="mb-3">Recent assignments</h5>
          <ul class="list-group list-group-flush">
            <?php foreach (array_slice($recentAssignments, 0, 5) as $assignment): ?>
              <li class="list-group-item bg-transparent"><strong><?php echo e($assignment['title']); ?></strong> — <?php echo e($assignment['deadline'] ?: 'No deadline'); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card p-4">
          <h5 class="mb-3">Quick actions</h5>
          <div class="d-grid gap-2">
            <a class="btn btn-outline-light" href="users/index.php">Manage users</a>
            <a class="btn btn-outline-light" href="courses/index.php">Manage courses</a>
            <a class="btn btn-outline-light" href="assignments/index.php">Manage assignments</a>
            <a class="btn btn-outline-light" href="payments/index.php">Payments</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
