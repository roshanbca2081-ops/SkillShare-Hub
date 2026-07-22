<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('admin');

$id = (int)($_GET['id'] ?? 0);
$field = get_field_by_id($id);

if (!$field) {
    redirect('admin/fields/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-graduation-cap');
    $description = trim($_POST['description'] ?? '');

    if (empty($slug)) {
        $slug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $name)));
    }

    if (empty($name)) {
        $error = 'Field name is required.';
    } else {
        $logo_path = $field['logo_path']; // keep existing

        // Handle logo upload
        $target_dir = __DIR__ . '/../../assets/images/fields/';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            // Delete old logo
            if (!empty($field['logo_path']) && file_exists($target_dir . $field['logo_path'])) {
                unlink($target_dir . $field['logo_path']);
            }
            $new_logo = upload_image($_FILES['logo'], $target_dir);
            if ($new_logo) {
                $logo_path = $new_logo;
            } else {
                $error = 'Failed to upload logo.';
            }
        }

        // Remove logo if checkbox is set
        if (isset($_POST['remove_logo']) && $_POST['remove_logo'] === '1') {
            if (!empty($field['logo_path']) && file_exists($target_dir . $field['logo_path'])) {
                unlink($target_dir . $field['logo_path']);
            }
            $logo_path = null;
        }

        if (empty($error) && update_field($id, $name, $slug, $icon, $description, $logo_path)) {
            redirect('admin/fields/index.php');
        } elseif (empty($error)) {
            $error = 'Failed to update field.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Field - Admin</title>
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
              <h1>Edit Field: <?php echo e($field['name']); ?></h1>
              <p>Update field details and logo.</p>
            </div>
            <a class="btn btn--outline" href="index.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
          </div>

          <div class="card p-4" style="max-width:720px;">
            <?php if ($error): ?>
              <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label">Field Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?php echo e($field['name']); ?>" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" value="<?php echo e($field['slug'] ?? ''); ?>" />
                <div class="small text-light-emphasis mt-1">Unique URL identifier. Example: <code>information-technology</code></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Font Awesome Icon</label>
                <div class="input-icon">
                  <i class="fa-solid fa-icons" style="left:14px;right:auto;"></i>
                  <input type="text" name="icon" class="form-control" value="<?php echo e($field['icon'] ?? 'fa-graduation-cap'); ?>" style="padding-left:42px;" />
                </div>
                <div class="small text-light-emphasis mt-1">Current icon: <i class="fa-solid <?php echo e($field['icon'] ?? 'fa-graduation-cap'); ?>"></i> <code><?php echo e($field['icon'] ?? 'fa-graduation-cap'); ?></code></div>
              </div>

              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?php echo e($field['description'] ?? ''); ?></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Field Logo</label>
                <?php if (!empty($field['logo_path'])): ?>
                  <div class="d-flex align-items-center gap-3 mb-2">
                    <img src="../../assets/images/fields/<?php echo e($field['logo_path']); ?>" alt="Current logo" style="width:80px;height:80px;border-radius:12px;object-fit:cover;border:1px solid var(--border);" />
                    <div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="remove_logo" />
                        <label class="form-check-label small" for="remove_logo">Remove current logo</label>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
                <input type="file" name="logo" class="form-control" accept="image/jpeg,image/png,image/gif,image/svg+xml,image/webp" />
                <div class="small text-light-emphasis mt-1">Leave empty to keep current logo. Allowed: jpg, png, gif, svg, webp</div>
              </div>

              <div class="d-flex gap-2 mt-4">
                <button class="btn btn--primary" type="submit"><i class="fa-solid fa-save"></i> Update Field</button>
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

