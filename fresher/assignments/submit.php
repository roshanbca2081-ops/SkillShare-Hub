<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('fresher');

$assignmentId = (int) ($_GET['assignment_id'] ?? 0);
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignmentId = (int) ($_POST['assignment_id'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    $filePath = '';
    if (!empty($_FILES['file']['name'])) {
        $targetDir = '../../assets/uploads/submissions/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['file']['name']);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);
        $filePath = 'assets/uploads/submissions/' . $fileName;
    }

    if (create_assignment_submission($assignmentId, $_SESSION['user_id'], $notes, $filePath)) {
        redirect('fresher/assignments/index.php');
    }
    $message = 'Unable to submit assignment.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Submit Assignment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4">
      <h2>Submit Assignment</h2>
      <p class="text-light-emphasis">Submit your completed work with notes and files.</p>
      <?php if ($message): ?><div class="alert alert-danger"><?php echo e($message); ?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="assignment_id" value="<?php echo (int)$assignmentId; ?>" />
        <div class="mb-3">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="4"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Upload file</label>
          <input type="file" name="file" class="form-control" />
        </div>
        <button class="btn btn-primary" type="submit">Submit assignment</button>
        <a class="btn btn-outline-light ms-2" href="index.php">Back</a>
      </form>
    </div>
  </div>
</body>
</html>
