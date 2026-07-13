<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('admin');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    if (create_assignment($title, $description, $_SESSION['user_id'], $deadline)) {
        redirect('admin/assignments/index.php');
    }
    $message = 'Unable to create assignment.';
}

$assignments = get_assignments();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Assignments</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4 mb-4">
      <h2 class="mb-1">Assignments</h2>
      <p class="text-light-emphasis mb-0">Review assignment submissions and progress.</p>
    </div>
    <div class="card p-4 mb-4">
      <h5>Create assignment</h5>
      <?php if ($message): ?><div class="alert alert-danger"><?php echo e($message); ?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-md-4"><input type="text" name="title" class="form-control" placeholder="Assignment title" required /></div>
        <div class="col-md-4"><input type="text" name="description" class="form-control" placeholder="Short description" /></div>
        <div class="col-md-2"><input type="date" name="deadline" class="form-control" /></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Save</button></div>
      </form>
    </div>
    <div class="card p-4">
      <table class="table table-dark table-striped align-middle">
        <thead><tr><th>Title</th><th>Description</th><th>Deadline</th><th>Posted by</th></tr></thead>
        <tbody>
          <?php foreach ($assignments as $assignment): ?>
            <tr>
              <td><?php echo e($assignment['title']); ?></td>
              <td><?php echo e($assignment['description']); ?></td>
              <td><?php echo e($assignment['deadline'] ?: '—'); ?></td>
              <td><?php echo e($assignment['created_by_name'] ?: 'Admin'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
