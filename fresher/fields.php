<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
ensure_database_schema();
require_login('fresher');

$fields = get_fields();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fields</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include '../includes/header.php'; ?>
  <?php include '../includes/navbar.php'; ?>
  <main class="page-shell">
    <section class="container page-section">
      <div class="card card--padded animate" style="max-width:900px;margin:auto;">
        <div class="page-title">
          <div>
            <h1>Academic Fields</h1>
            <p>Choose a field to explore related courses and assignments.</p>
          </div>
        </div>

        <?php
          // Phase 3: if DB table is empty, fall back to the premium catalog.
          if (!$fields) {
            require_once '../includes/field-data.php';
            // after requiring field-data.php, $academicFields becomes available
            $fields = array_values($academicFields ?? []);
          }
        ?>

        <?php if (!$fields): ?>
          <div class="alert alert-info">No fields available yet.</div>
        <?php else: ?>
          <div class="row g-3">
            <?php foreach ($fields as $f): ?>
              <div class="col-md-6">
                <a href="../field.php?field=<?php echo urlencode($f['slug'] ?? strtolower(str_replace(' ','-',$f['name'] ?? ''))); ?>" style="text-decoration:none;">
                  <div class="soft-card">
                    <div class="d-flex align-items-center gap-3">
                      <div class="field-icon" style="width:46px;height:46px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.03);border-radius:12px;">
                        <i class="fa-solid <?php echo e($f['icon'] ?? 'fa-graduation-cap'); ?>"></i>
                      </div>
                      <div>
                        <strong><?php echo e($f['name'] ?? 'Field'); ?></strong>
                        <div class="small text-light-emphasis" style="margin-top:3px; font-weight:800;">Explore field details</div>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="mt-4">
          <a class="btn btn--outline" href="../course.php">Back to Courses</a>
        </div>
      </div>
    </section>
  </main>
</body>
</html>

