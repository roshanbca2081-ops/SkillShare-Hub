<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('admin');

$id = (int) ($_GET['id'] ?? 0);
$course = get_course($id);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if (update_course($id, $title, $description)) {
        redirect('admin/courses/index.php');
    }
    $message = 'Unable to update course.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Course</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4">
      <h2>Edit Course</h2>
      <p class="text-light-emphasis">Update course details.</p>
      <?php if ($message): ?><div class="alert alert-danger"><?php echo e($message); ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" value="<?php echo e($course['title'] ?? ''); ?>" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4"><?php echo e($course['description'] ?? ''); ?></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Update course</button>
        <a class="btn btn-outline-light ms-2" href="index.php">Back</a>
      </form>
    </div>
  </div>
</body>
</html>
