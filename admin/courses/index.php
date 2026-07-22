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
  <title>Courses - Admin</title>
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
              <h1>Courses</h1>
              <p>Manage platform courses with field assignments and images.</p>
            </div>
            <a class="btn btn--primary" href="add.php"><i class="fa-solid fa-plus"></i> Add Course</a>
          </div>

          <?php if (empty($courses)): ?>
            <div class="card p-5 text-center">
              <i class="fa-solid fa-book-open-reader" style="font-size:3rem;color:var(--muted);margin-bottom:16px;"></i>
              <h3>No Courses Yet</h3>
              <p class="text-light-emphasis">Add your first course to get started.</p>
              <a class="btn btn--primary mt-3" href="add.php"><i class="fa-solid fa-plus"></i> Add Course</a>
            </div>
          <?php else: ?>
            <div class="card p-4">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Image</th>
                      <th>Title</th>
                      <th>Field</th>
                      <th>Difficulty</th>
                      <th>Duration</th>
                      <th>Rating</th>
                      <th>Enrolled</th>
                      <th>Mentor</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($courses as $course): ?>
                      <tr>
                        <td>
                          <?php if (!empty($course['image_path'])): ?>
                            <img src="../../assets/images/courses/<?php echo e($course['image_path']); ?>" alt="<?php echo e($course['title']); ?>" style="width:50px;height:35px;border-radius:6px;object-fit:cover;" />
                          <?php else: ?>
                            <div style="width:50px;height:35px;border-radius:6px;background:rgba(26,91,191,.08);display:grid;place-items:center;color:var(--muted);">
                              <i class="fa-solid fa-image" style="font-size:.85rem;"></i>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td><strong><?php echo e($course['title']); ?></strong></td>
                        <td>
                          <?php if (!empty($course['field_name'])): ?>
                            <span class="badge bg-info"><i class="fa-solid <?php echo e($course['field_icon'] ?? 'fa-graduation-cap'); ?>"></i> <?php echo e($course['field_name']); ?></span>
                          <?php else: ?>
                            <span class="text-light-emphasis">—</span>
                          <?php endif; ?>
                        </td>
                        <td><span class="badge bg-secondary"><?php echo e($course['difficulty_level'] ?? 'Beginner'); ?></span></td>
                        <td><?php echo e($course['duration'] ?? '—'); ?></td>
                        <td>
                          <?php if (!empty($course['rating']) && $course['rating'] > 0): ?>
                            <span class="text-warning"><?php echo str_repeat('★', round($course['rating'])); ?></span> <?php echo e($course['rating']); ?>
                          <?php else: ?>
                            —
                          <?php endif; ?>
                        </td>
                        <td><?php echo e($course['enrolled_students'] ?? 0); ?></td>
                        <td><?php echo e($course['mentor_name'] ?? '—'); ?></td>
                        <td>
                          <div class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?php echo (int)$course['id']; ?>"><i class="fa-solid fa-pen"></i></a>
                            <form method="post" class="d-inline">
                              <input type="hidden" name="action" value="delete" />
                              <input type="hidden" name="course_id" value="<?php echo (int)$course['id']; ?>" />
                              <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this course?')"><i class="fa-solid fa-trash"></i></button>
                            </form>
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

