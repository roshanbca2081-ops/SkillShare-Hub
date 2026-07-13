<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';

ensure_database_schema();
require_login('graduate');

$users = get_users('fresher');
$selectedUserId = (int) ($_GET['user_id'] ?? 0);
$message = trim($_POST['message'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $message !== '') {
    send_message($_SESSION['user_id'], $selectedUserId, $message);
    redirect('graduate/messages/index.php?user_id=' . $selectedUserId);
}

$conversation = [];
if ($selectedUserId) {
    $conversation = get_messages_between($_SESSION['user_id'], $selectedUserId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Graduate Messages</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card p-4">
      <h2>Graduate Messages</h2>
      <p class="text-light-emphasis">Chat privately with freshers and other mentors.</p>
      <div class="row mt-3">
        <div class="col-md-4">
          <h5>Contacts</h5>
          <ul class="list-group">
            <?php foreach ($users as $user): ?>
              <li class="list-group-item bg-transparent"><a href="index.php?user_id=<?php echo (int)$user['id']; ?>"><?php echo e($user['full_name']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="col-md-8">
          <?php if ($selectedUserId): ?>
            <h5>Conversation</h5>
            <div class="border rounded p-3 mb-3" style="min-height:180px;">
              <?php foreach ($conversation as $item): ?>
                <div class="mb-2"><strong><?php echo e($item['sender_id'] == $_SESSION['user_id'] ? 'You' : 'Student'); ?></strong>: <?php echo e($item['message']); ?></div>
              <?php endforeach; ?>
            </div>
            <form method="post">
              <input type="hidden" name="user_id" value="<?php echo (int)$selectedUserId; ?>" />
              <textarea name="message" class="form-control" rows="3" placeholder="Type your message"></textarea>
              <button class="btn btn-primary mt-2" type="submit">Send</button>
            </form>
          <?php else: ?>
            <p class="text-light-emphasis">Select a contact to start messaging.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
