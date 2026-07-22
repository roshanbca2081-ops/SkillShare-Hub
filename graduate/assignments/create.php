<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
ensure_database_schema();
require_login('graduate');
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $deadline = $_POST['deadline'] ?? '';
    if (create_assignment($title, $description, $_SESSION['user_id'], $deadline)) {
        add_notification($_SESSION['user_id'], 'New assignment created: ' . $title);
        $message = 'Assignment created successfully!';
    } else {
        $message = 'Failed to create assignment.';
    }
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container py-5">
    <div class="card p-4" style="max-width:700px;margin:0 auto;">
      <div class="page-title">
        <div>
          <h1>Create Assignment</h1>
          <p>Create a new assignment for freshers.</p>
        </div>
      </div>
      <?php if ($message): ?><div class="alert <?php echo strpos($message, 'successfully') !== false ? '' : 'alert-danger'; ?>"><?php echo e($message); ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Title</label>
          <input name="title" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Deadline</label>
          <input name="deadline" type="date" class="form-control" />
        </div>
        <button class="btn btn--primary" type="submit">Create Assignment</button>
        <a class="btn btn--outline" href="index.php">Cancel</a>
      </form>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
