<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
ensure_database_schema();
require_login('fresher');

$assignmentId = (int)($GET['assignment_id'] ?? 0);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notes = trim($_POST['notes'] ?? '');
    $filePath = '';
    if (!empty($_FILES['submission_file']['name'])) {
        $uploadDir = '../../assets/uploads/submissions/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
        $filePath = 'assets/uploads/submissions/' . time() . '_' . basename($_FILES['submission_file']['name']);
        move_uploaded_file($_FILES['submission_file']['tmp_name'], '../../' . $filePath);
    }
    if (create_assignment_submission($assignmentId, $_SESSION['user_id'], $notes, $filePath)) {
        add_notification($_SESSION['user_id'], 'Assignment submitted successfully.');
        $message = 'Assignment submitted successfully!';
    } else {
        $message = 'Submission failed. Please try again.';
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
          <h1>Submit Assignment</h1>
          <p>Upload your work and add notes for the mentor.</p>
        </div>
      </div>
      <?php if ($message): ?><div class="alert <?php echo strpos($message, 'successfully') !== false ? '' : 'alert-danger'; ?>"><?php echo e($message); ?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Assignment ID</label>
          <input class="form-control" name="assignment_id" value="<?php echo e($assignmentId); ?>" readonly />
        </div>
        <div class="mb-3">
          <label class="form-label">Notes for Mentor</label>
          <textarea name="notes" class="form-control" rows="4" placeholder="Describe what you've done..."></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Upload File</label>
          <input type="file" name="submission_file" class="form-control" />
        </div>
        <button class="btn btn--primary" type="submit">Submit Assignment</button>
        <a class="btn btn--outline" href="index.php">Back</a>
      </form>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
