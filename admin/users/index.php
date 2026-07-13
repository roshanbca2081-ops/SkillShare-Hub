<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('admin');

$action = $_POST['action'] ?? '';
if ($action === 'status' && !empty($_POST['user_id']) && !empty($_POST['status'])) {
    update_user_status((int) $_POST['user_id'], $_POST['status']);
    redirect('admin/users/index.php');
}

$users = get_users();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4">
      <h2>Manage Users</h2>
      <p class="text-light-emphasis">Manage platform users and account status.</p>
      <table class="table table-dark table-striped align-middle mt-3">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo e($user['full_name']); ?></td>
              <td><?php echo e($user['email']); ?></td>
              <td><?php echo e($user['role']); ?></td>
              <td><?php echo e($user['status']); ?></td>
              <td>
                <form method="post" class="d-inline">
                  <input type="hidden" name="action" value="status" />
                  <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>" />
                  <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                    <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="suspended" <?php echo $user['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                  </select>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
