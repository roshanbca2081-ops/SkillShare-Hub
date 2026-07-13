<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('fresher');

$bookings = get_mentorship_bookings($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mentorship History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4">
      <h2>Mentorship History</h2>
      <p class="text-light-emphasis">Track past bookings and upcoming sessions.</p>
      <table class="table table-dark table-striped align-middle mt-3">
        <thead><tr><th>Topic</th><th>Mentor</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
          <?php foreach ($bookings as $booking): ?>
            <tr>
              <td><?php echo e($booking['topic']); ?></td>
              <td><?php echo e($booking['mentor_name'] ?: 'Pending'); ?></td>
              <td><?php echo e($booking['status']); ?></td>
              <td><?php echo e($booking['created_at']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
