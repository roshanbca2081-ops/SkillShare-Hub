<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('fresher');

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
      <p class="text-light-emphasis mb-0">Open assigned tasks and submit your work.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($assignments as $assignment): ?>
        <div class="col-md-6">
          <div class="card p-4">
            <h5><?php echo e($assignment['title']); ?></h5>
            <p class="text-light-emphasis"><?php echo e($assignment['description']); ?></p>
            <p class="small mb-2">Deadline: <?php echo e($assignment['deadline'] ?: 'No deadline'); ?></p>
            <p class="small mb-3">Posted by: <?php echo e($assignment['created_by_name'] ?: 'Admin'); ?></p>
            <a class="btn btn-primary" href="submit.php?assignment_id=<?php echo (int)$assignment['id']; ?>">Submit work</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
