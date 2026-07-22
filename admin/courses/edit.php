<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('admin');

$id = (int)($_GET['id'] ?? 0);
$course = get_course($id);

if (!$course) {
    redirect('admin/courses/index.php');
}

$fields = get_fields();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $difficulty_level = trim($_POST['difficulty_level'] ?? 'Beginner');
    $rating = (float)($_POST['rating'] ?? 0);
    $enrolled_students = (int)($_POST['enrolled_students'] ?? 0);
    $mentor_name = trim($_POST['mentor_name'] ?? '');
    $field_id = !empty($_POST['field_id']) ? (int)$_POST['field_id'] : null;

    if (empty($slug)) {
        $slug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9 ]/', '', $title)));
    }

    if (empty($title)) {
        $error = 'Course title is required.';
    } else {
        $target_dir = __DIR__ . '/../../assets/images/courses/';
        $image_path = $course['image_path']; // keep existing

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if (!empty($course['image_path']) && file_exists($target_dir . $course['image_path'])) {
                unlink($target_dir . $course['image_path']);
            }
            $new_image = upload_image($_FILES['image'], $target_dir);
            if ($new_image) {
                $image_path = $new_image;
            } else {
                $error = 'Failed to upload image.';
            }
        }

        // Remove image if checkbox is set
        if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
            if (!empty($course['image_path']) && file_exists($target_dir . $course['image_path'])) {
                unlink($target_dir . $course['image_path']);
            }
            $image_path = null;
        }

        if (empty($error)) {
            if (update_course_full($id, $title, $slug, $description, $image_path, $duration, $difficulty_level, $rating, $enrolled_students, $mentor_name, null, $field_id)) {
                redirect('admin/courses/index.php');
            } else {
                $error = 'Failed to update course.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Course - Admin</title>
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
          <a class="active" href="index.php">Courses</a>
          <a href="../fields/index.php">Fields</a>
          <a href="../assignments/index.php">Assignments</a>
          <a href="../payments/index.php">Payments</a>
          <a href="../reports/index.php">Reports</a>
          <a href="../settings/index.php">Settings</a>
        </aside>
        <div>
          <div class="page-title">
            <div>
              <h1>Edit Course</h1>
              <p>Update course: <?php echo e($course['title']); ?></p>
            </div>
            <a class="btn btn--outline" href="index.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
          </div>

          <div class="card p-4" style="max-width:720px;">
            <?php if ($error): ?>
              <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label">Course Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?php echo e($course['title']); ?>" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" class="form-control" value="<?php echo e($course['slug'] ?? ''); ?>" />
              </div>

              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?php echo e($course['description'] ?? ''); ?></textarea>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Academic Field</label>
                    <select name="field_id" class="form-select">
                      <option value="">-- Select Field --</option>
                      <?php foreach ($fields as $f): ?>
                        <option value="<?php echo (int)$f['id']; ?>" <?php echo ((int)$course['field_id'] === (int)$f['id']) ? 'selected' : ''; ?>>
                          <?php echo e($f['name']); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Difficulty Level</label>
                    <select name="difficulty_level" class="form-select">
                      <option value="Beginner" <?php echo ($course['difficulty_level'] ?? '') === 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                      <option value="Intermediate" <?php echo ($course['difficulty_level'] ?? '') === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                      <option value="Advanced" <?php echo ($course['difficulty_level'] ?? '') === 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Duration</label>
                    <input type="text" name="duration" class="form-control" value="<?php echo e($course['duration'] ?? ''); ?>" placeholder="e.g. 10 weeks" />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="mb-3">
                    <label class="form-label">Rating (0-5)</label>
                    <input type="number" name="rating" class="form-control" value="<?php echo e($course['rating'] ?? '0'); ?>" step="0.1" min="0" max="5" />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="mb-3">
                    <label class="form-label">Enrolled</label>
                    <input type="number" name="enrolled_students" class="form-control" value="<?php echo e($course['enrolled_students'] ?? '0'); ?>" min="0" />
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Mentor Name</label>
                <input type="text" name="mentor_name" class="form-control" value="<?php echo e($course['mentor_name'] ?? ''); ?>" placeholder="e.g. Prof. Nirmal Bhatia" />
              </div>

              <div class="mb-3">
                <label class="form-label">Course Image</label>
                <?php if (!empty($course['image_path'])): ?>
                  <div class="d-flex align-items-center gap-3 mb-2">
                    <img src="../../assets/images/courses/<?php echo e($course['image_path']); ?>" alt="Current" style="width:120px;height:70px;border-radius:8px;object-fit:cover;border:1px solid var(--border);" />
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image" />
                      <label class="form-check-label small" for="remove_image">Remove image</label>
                    </div>
                  </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" />
              </div>

              <div class="d-flex gap-2 mt-4">
                <button class="btn btn--primary" type="submit"><i class="fa-solid fa-save"></i> Update Course</button>
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

