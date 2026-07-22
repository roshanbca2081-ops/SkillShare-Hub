<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('admin');

$action = $_GET['action'] ?? '';
$id = (int)($_GET['id'] ?? 0);

// Handle delete
if ($action === 'delete' && $id > 0) {
    $field = get_field_by_id($id);
    if ($field) {
        // Delete logo file if exists
        if (!empty($field['logo_path'])) {
            $logoFile = __DIR__ . '/../../assets/images/fields/' . $field['logo_path'];
            if (file_exists($logoFile)) {
                unlink($logoFile);
            }
        }
        delete_field($id);
    }
    redirect('admin/fields/index.php');
}

$fields = get_fields();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Academic Fields - Admin</title>
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
              <h1>Academic Fields</h1>
              <p>Manage academic fields of study, upload logos, and organize courses.</p>
            </div>
            <a class="btn btn--primary" href="add.php"><i class="fa-solid fa-plus"></i> Add Field</a>
          </div>

          <?php if (empty($fields)): ?>
            <div class="card p-5 text-center">
              <i class="fa-solid fa-layer-group" style="font-size:3rem;color:var(--muted);margin-bottom:16px;"></i>
              <h3>No Fields Yet</h3>
              <p class="text-light-emphasis">Get started by adding your first academic field.</p>
              <a class="btn btn--primary mt-3" href="add.php"><i class="fa-solid fa-plus"></i> Add Field</a>
            </div>
          <?php else: ?>
            <div class="card p-4">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Logo</th>
                      <th>Name</th>
                      <th>Slug</th>
                      <th>Icon</th>
                      <th>Courses</th>
                      <th>Created</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($fields as $field): ?>
                      <tr>
                        <td>
                          <?php if (!empty($field['logo_path'])): ?>
                            <img src="../../assets/images/fields/<?php echo e($field['logo_path']); ?>" alt="<?php echo e($field['name']); ?>" style="width:40px;height:40px;border-radius:8px;object-fit:cover;" />
                          <?php else: ?>
                            <div style="width:40px;height:40px;border-radius:8px;background:rgba(26,91,191,.1);display:grid;place-items:center;color:var(--primary);">
                              <i class="fa-solid <?php echo e($field['icon'] ?? 'fa-graduation-cap'); ?>"></i>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td><strong><?php echo e($field['name']); ?></strong></td>
                        <td><code><?php echo e($field['slug'] ?? ''); ?></code></td>
                        <td><i class="fa-solid <?php echo e($field['icon'] ?? 'fa-graduation-cap'); ?>"></i> <?php echo e($field['icon'] ?? 'fa-graduation-cap'); ?></td>
                        <td><span class="badge bg-primary"><?php echo e((int)($field['course_count'] ?? 0)); ?></span></td>
                        <td class="small text-light-emphasis"><?php echo e(date('M d, Y', strtotime($field['created_at'] ?? 'now'))); ?></td>
                        <td>
                          <div class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?php echo (int)$field['id']; ?>"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a class="btn btn-sm btn-outline-danger" href="index.php?action=delete&id=<?php echo (int)$field['id']; ?>" onclick="return confirm('Delete this field? Courses in this field will be unassigned.')"><i class="fa-solid fa-trash"></i></a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>
  <?php include '../../includes/footer.php'; ?>
</body>
</html>

