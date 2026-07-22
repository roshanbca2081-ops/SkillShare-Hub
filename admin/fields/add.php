<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('admin');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-graduation-cap');
    $description = trim($_POST['description'] ?? '');

    // Auto-generate slug if empty
    if (empty($slug)) {
        $slug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $name)));
    }

    if (empty($name)) {
        $error = 'Field name is required.';
    } else {
        // Handle logo upload
        $logo_path = null;
        $target_dir = __DIR__ . '/../../assets/images/fields/';

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logo_path = upload_image($_FILES['logo'], $target_dir);
            if (!$logo_path) {
                $error = 'Failed to upload logo. Allowed: jpg, jpeg, png, gif, svg, webp';
            }
        }

        if (empty($error) && create_field($name, $slug, $icon, $description, $logo_path)) {
            redirect('admin/fields/index.php');
        } elseif (empty($error)) {
            $error = 'Failed to create field. The slug may already exist.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Field - Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <?php include '../../includes/header.php'; ?>
  <?php include '../../includes/navbar.php'; ?>
  <main class="page-shell">
    <div class="container">
      <div class="app-layout">
        <aside class="sidebar-nav">
          <div class="sidebar-brand"><span class="site-logo site-logo--sidebar" aria-hidden="true"></span></div>
          <a href="../dashboard.php">Dashboard</a>
          <a href="../users/index.php">Users</a>
          <a href="../graduates/index.php">Mentors</a>
          <a href="../courses/index.php">Courses</a>
          <a class="active" href="index.php">Fields</a>
          <a href="../assignments/index.php">Assignments</a>
          <a href="../payments/index.php">Payments</a>
          <a href="../reports/index.php">Reports</a>
          <a href="../settings/index.php">Settings</a>
        </aside>
        <div>
          <div class="page-title">
            <div>
              <h1>Add Academic Field</h1>
              <p>Create a new field of study with icon and logo.</p>
            </div>
            <a class="btn btn--outline" href="index.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
          </div>

          <div class="card p-4" style="max-width:720px;">
            <?php if ($error): ?>
              <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>
            <?php if ($message): ?>
              <div class="alert alert-success"><?php echo e($message); ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label">Field Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?php echo e($_POST['name'] ?? ''); ?>" required placeholder="e.g. Information Technology" />
              </div>

              <div class="mb-3">
                <label class="form-label">Slug (URL path)</label>
                <input type="text" name="slug" class="form-control" value="<?php echo e($_POST['slug'] ?? ''); ?>" placeholder="Auto-generated if empty" />
                <div class="small text-light-emphasis mt-1">Leave empty to auto-generate from name. Example: <code>information-technology</code></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Font Awesome Icon</label>
                <div class="input-icon">
                  <i class="fa-solid fa-icons" style="left:14px;right:auto;"></i>
                  <input type="text" name="icon" class="form-control" value="<?php echo e($_POST['icon'] ?? 'fa-graduation-cap'); ?>" style="padding-left:42px;" placeholder="e.g. fa-laptop-code" />
                </div>
                <div class="small text-light-emphasis mt-1">
                  Choose from: <code>fa-laptop-code</code> (IT), <code>fa-gears</code> (Engineering), <code>fa-flask</code> (Science), <code>fa-briefcase</code> (Management), <code>fa-seedling</code> (Agriculture), <code>fa-heart-pulse</code> (Health), <code>fa-graduation-cap</code> (Education), <code>fa-scale-balanced</code> (Law), <code>fa-palette</code> (Arts), etc.
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe this field of study..."><?php echo e($_POST['description'] ?? ''); ?></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Field Logo</label>
                <input type="file" name="logo" class="form-control" accept="image/jpeg,image/png,image/gif,image/svg+xml,image/webp" />
                <div class="small text-light-emphasis mt-1">Recommended: 200x200px. Allowed: jpg, png, gif, svg, webp</div>
              </div>

              <div class="d-flex gap-2 mt-4">
                <button class="btn btn--primary" type="submit"><i class="fa-solid fa-save"></i> Create Field</button>
                <a class="btn btn--outline" href="index.php">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
  <?php include '../../includes/footer.php'; ?>
</body>
</html>

