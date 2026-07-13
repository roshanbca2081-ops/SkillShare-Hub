<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('fresher');

$placements = get_placements();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Placement Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4 mb-4">
      <h2>Placement Hub</h2>
      <p class="text-light-emphasis">Prepare for interviews and discover placement opportunities.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($placements as $placement): ?>
        <div class="col-md-6">
          <div class="card p-4">
            <h5><?php echo e($placement['title']); ?></h5>
            <p class="text-light-emphasis mb-1"><strong><?php echo e($placement['company']); ?></strong></p>
            <p><?php echo e($placement['description']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
