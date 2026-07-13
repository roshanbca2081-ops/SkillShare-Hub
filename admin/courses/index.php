<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('admin');

$action = $_POST['action'] ?? '';
if ($action === 'delete' && !empty($_POST['course_id'])) {
    delete_course((int) $_POST['course_id']);
    redirect('admin/courses/index.php');
}

$courses = get_courses();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Courses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4 mb-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2 class="mb-1">Courses</h2>
          <p class="text-light-emphasis mb-0">Manage platform courses and content.</p>
        </div>
        <a class="btn btn-primary" href="add.php">Add course</a>
      </div>
    </div>
    <div class="card p-4">
      <table class="table table-dark table-striped align-middle">
        <thead>
          <tr><th>Title</th><th>Description</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($courses as $course): ?>
            <tr>
              <td><?php echo e($course['title']); ?></td>
              <td><?php echo e($course['description']); ?></td>
              <td><?php echo e($course['created_at']); ?></td>
              <td>
                <a class="btn btn-sm btn-outline-light me-2" href="edit.php?id=<?php echo (int)$course['id']; ?>">Edit</a>
                <form method="post" class="d-inline">
                  <input type="hidden" name="action" value="delete" />
                  <input type="hidden" name="course_id" value="<?php echo (int)$course['id']; ?>" />
                  <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
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
