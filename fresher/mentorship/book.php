<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('fresher');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mentorId = (int) ($_POST['mentor_id'] ?? 0);
    $topic = trim($_POST['topic'] ?? '');
    if (create_mentorship_booking($mentorId, $_SESSION['user_id'], $topic)) {
        add_notification($mentorId, 'New mentorship request received from a fresher.');
        redirect('fresher/mentorship/history.php');
    }
    $message = 'Unable to create mentorship request.';
}

$mentors = get_users('graduate');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Book Mentorship</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4">
      <h2>Book Mentorship</h2>
      <p class="text-light-emphasis">Reserve a session with a graduate mentor.</p>
      <?php if ($message): ?><div class="alert alert-danger"><?php echo e($message); ?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Mentor</label>
          <select name="mentor_id" class="form-select" required>
            <?php foreach ($mentors as $mentor): ?>
              <option value="<?php echo (int)$mentor['id']; ?>"><?php echo e($mentor['full_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Topic</label>
          <input type="text" name="topic" class="form-control" required />
        </div>
        <button class="btn btn-primary" type="submit">Send request</button>
        <a class="btn btn-outline-light ms-2" href="history.php">View history</a>
      </form>
    </div>
  </div>
</body>
</html>
