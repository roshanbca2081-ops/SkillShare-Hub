<?php
$page_title = 'Messages';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$chat_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Get conversations (students who have messaged or enrolled)
$stmt = $pdo->prepare("SELECT DISTINCT 
                       u.id as user_id, u.full_name, u.avatar, u.email,
                       (SELECT message FROM messages 
                        WHERE (sender_id = ? AND receiver_id = u.id) 
                           OR (sender_id = u.id AND receiver_id = ?) 
                        ORDER BY created_at DESC LIMIT 1) as last_message,
                       (SELECT created_at FROM messages 
                        WHERE (sender_id = ? AND receiver_id = u.id) 
                           OR (sender_id = u.id AND receiver_id = ?) 
                        ORDER BY created_at DESC LIMIT 1) as last_time,
                       (SELECT COUNT(*) FROM messages 
                        WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count
                       FROM messages m
                       JOIN users u ON (u.id = m.sender_id OR u.id = m.receiver_id)
                       WHERE (m.sender_id = ? OR m.receiver_id = ?)
                       AND u.id != ?
                       AND u.role = 'fresher'
                       UNION
                       SELECT DISTINCT 
                       u.id as user_id, u.full_name, u.avatar, u.email,
                       NULL as last_message, NULL as last_time, 0 as unread_count
                       FROM enrollments e
                       JOIN users u ON e.fresher_id = u.id
                       JOIN courses c ON e.course_id = c.id
                       WHERE c.mentor_id = ?
                       AND u.id NOT IN (SELECT DISTINCT 
                           CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END 
                           FROM messages 
                           WHERE sender_id = ? OR receiver_id = ?
                       )
                       ORDER BY last_time DESC");
$stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
$conversations = $stmt->fetchAll();

// Get messages for chat
$messages = [];
$chat_user = null;

if ($chat_user_id) {
    $stmt = $pdo->prepare("SELECT id, full_name, avatar, email FROM users WHERE id = ?");
    $stmt->execute([$chat_user_id]);
    $chat_user = $stmt->fetch();
    
    if ($chat_user) {
        $stmt = $pdo->prepare("SELECT m.*, u.full_name, u.avatar 
                               FROM messages m 
                               JOIN users u ON m.sender_id = u.id 
                               WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                                  OR (m.sender_id = ? AND m.receiver_id = ?) 
                               ORDER BY m.created_at ASC");
        $stmt->execute([$user_id, $chat_user_id, $chat_user_id, $user_id]);
        $messages = $stmt->fetchAll();
        
        // Mark as read
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1, read_at = NOW() 
                               WHERE sender_id = ? AND receiver_id = ?");
        $stmt->execute([$chat_user_id, $user_id]);
    }
}

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = (int)$_POST['receiver_id'];
    $message = sanitize($_POST['message']);
    
    if (!empty($message) && $receiver_id) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $receiver_id, $message]);
        
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link)
                       VALUES (?, 'message', 'New Message',
                           CONCAT(?, ' sent you a message'),
                           CONCAT('fresher/message/index.php?user_id=', ?))");
        $stmt->execute([$receiver_id, getUserName(), $user_id]);
        
        redirect('index.php?user_id=' . $receiver_id);
    }
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Messages</h1>
            </div>
            
            <div class="row g-4">
                <!-- Conversations -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">Conversations</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="conversations-list" style="max-height: 500px; overflow-y: auto;">
                                <?php foreach ($conversations as $conv): ?>
                                <a href="?user_id=<?php echo $conv['user_id']; ?>" 
                                   class="text-decoration-none d-block">
                                    <div class="conversation-item p-3 border-bottom <?php echo $chat_user_id == $conv['user_id'] ? 'bg-light' : ''; ?>">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo getAvatar($conv); ?>" 
                                                 class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($conv['full_name']); ?></h6>
                                                    <?php if ($conv['unread_count'] > 0): ?>
                                                        <span class="badge bg-danger"><?php echo $conv['unread_count']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo $conv['last_message'] ? htmlspecialchars(substr($conv['last_message'], 0, 40)) : 'No messages'; ?>
                                                </small>
                                                <?php if ($conv['last_time']): ?>
                                                    <br><small class="text-muted"><?php echo getTimeAgo($conv['last_time']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                                <?php if (empty($conversations)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No conversations</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chat Area -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <?php if ($chat_user): ?>
                            <div class="card-header bg-transparent border-bottom">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo getAvatar($chat_user); ?>" 
                                         class="rounded-circle me-2" style="width: 36px; height: 36px;">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($chat_user['full_name']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($chat_user['email']); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="height: 400px; overflow-y: auto;" id="chatMessages">
                                <?php foreach ($messages as $msg): ?>
                                <div class="d-flex <?php echo $msg['sender_id'] == $user_id ? 'justify-content-end' : 'justify-content-start'; ?> mb-3">
                                    <?php if ($msg['sender_id'] != $user_id): ?>
                                        <img src="<?php echo getAvatar($msg); ?>" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                    <?php endif; ?>
                                    <div class="message-bubble p-2 rounded <?php echo $msg['sender_id'] == $user_id ? 'bg-primary text-white' : 'bg-light'; ?>" 
                                         style="max-width: 70%;">
                                        <p class="mb-0 small"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                        <small class="<?php echo $msg['sender_id'] == $user_id ? 'text-white-50' : 'text-muted'; ?>" style="font-size: 0.65rem;">
                                            <?php echo getTimeAgo($msg['created_at']); ?>
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="card-footer bg-transparent border-top">
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="receiver_id" value="<?php echo $chat_user_id; ?>">
                                    <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                                    <button type="submit" name="send_message" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="card-body text-center py-5">
                                <i class="fas fa-comment-dots fa-3x text-muted mb-3"></i>
                                <h5>Select a conversation</h5>
                                <p class="text-muted">Choose a student from the list to start messaging.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const chatMessages = document.getElementById('chatMessages');
if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
</script>

<?php include '../../includes/footer.php'; ?>