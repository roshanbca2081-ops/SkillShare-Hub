<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('admin');

$submissions = get_assignment_submissions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Review Assignment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4">
      <h2>Review Assignment</h2>
      <p class="text-light-emphasis">Grade and provide feedback on submissions.</p>
      <table class="table table-dark table-striped align-middle mt-3">
        <thead><tr><th>Student</th><th>Assignment</th><th>Status</th><th>Notes</th><th>File</th></tr></thead>
        <tbody>
          <?php foreach ($submissions as $submission): ?>
            <tr>
              <td><?php echo e($submission['student_name'] ?: 'Unknown'); ?></td>
              <td><?php echo e($submission['assignment_title'] ?: '—'); ?></td>
              <td><?php echo e($submission['status']); ?></td>
              <td><?php echo e($submission['notes']); ?></td>
              <td><?php if ($submission['file_path']): ?><a href="../../<?php echo e($submission['file_path']); ?>" target="_blank">Open file</a><?php else: ?>—<?php endif; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
