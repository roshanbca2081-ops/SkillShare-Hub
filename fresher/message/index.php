<?php
$page_title = 'Messages';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$chat_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$chat_user_id = isset($_GET['chat']) ? (int)$_GET['chat'] : $chat_user_id;

// Get all conversations
$stmt = $pdo->prepare("SELECT DISTINCT 
                       CASE 
                           WHEN sender_id = ? THEN receiver_id 
                           ELSE sender_id 
                       END as other_user_id,
                       u.full_name, u.avatar, u.role, u.is_active,
                       (SELECT message FROM messages 
                        WHERE (sender_id = ? AND receiver_id = u.id) 
                           OR (sender_id = u.id AND receiver_id = ?) 
                        ORDER BY created_at DESC LIMIT 1) as last_message,
                       (SELECT created_at FROM messages 
                        WHERE (sender_id = ? AND receiver_id = u.id) 
                           OR (sender_id = u.id AND receiver_id = ?) 
                        ORDER BY created_at DESC LIMIT 1) as last_time,
                       (SELECT COUNT(*) FROM messages 
                        WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count,
                       (SELECT COUNT(*) FROM messages 
                        WHERE (sender_id = ? AND receiver_id = u.id) 
                           OR (sender_id = u.id AND receiver_id = ?)) as total_messages
                       FROM messages m
                       JOIN users u ON (u.id = m.sender_id OR u.id = m.receiver_id)
                       WHERE (m.sender_id = ? OR m.receiver_id = ?)
                       AND u.id != ?
                       AND u.is_active = 1
                       GROUP BY other_user_id, u.full_name, u.avatar, u.role, u.is_active
                       ORDER BY last_time DESC");
$stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
$conversations = $stmt->fetchAll();

// Get messages for chat
$messages = [];
$chat_user = null;

if ($chat_user_id) {
    // Get chat user info
    $stmt = $pdo->prepare("SELECT id, full_name, avatar, role, is_active, last_login FROM users WHERE id = ?");
    $stmt->execute([$chat_user_id]);
    $chat_user = $stmt->fetch();
    
    if ($chat_user) {
        // Get messages
        $stmt = $pdo->prepare("SELECT m.*, u.full_name, u.avatar 
                               FROM messages m 
                               JOIN users u ON m.sender_id = u.id 
                               WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                                  OR (m.sender_id = ? AND m.receiver_id = ?) 
                               ORDER BY m.created_at ASC");
        $stmt->execute([$user_id, $chat_user_id, $chat_user_id, $user_id]);
        $messages = $stmt->fetchAll();
        
        // Mark messages as read
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1, read_at = NOW() 
                               WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
        $stmt->execute([$chat_user_id, $user_id]);
    } else {
        $chat_user_id = 0;
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'User not found.'
        ];
    }
}

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = (int)$_POST['receiver_id'];
    $message = sanitize($_POST['message']);
    
    if (empty($message)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Please enter a message.'
        ];
    } elseif ($receiver_id) {
        // Check if receiver exists
        $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$receiver_id]);
        $receiver = $stmt->fetch();
        
        if ($receiver) {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $receiver_id, $message]);
            
            // Create notification for receiver
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) 
                                   VALUES (?, 'message', 'New Message', 
                                           CONCAT(?, ' sent you a message'), 
                                            'fresher/message/index.php?user_id=' || ?)");
            $stmt->execute([$receiver_id, getUserName(), $user_id]);
            
            // Redirect to refresh chat
            redirect('index.php?user_id=' . $receiver_id);
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'message' => 'User not found or inactive.'
            ];
        }
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_file'])) {
    $receiver_id = (int)$_POST['receiver_id'];
    
    if (isset($_FILES['file_attachment']) && $_FILES['file_attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/messages/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 10 * 1024 * 1024; // 10MB
        
        if (!in_array($_FILES['file_attachment']['type'], $allowed_types)) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'message' => 'File type not allowed. Please upload images, PDFs, or Word documents.'
            ];
        } elseif ($_FILES['file_attachment']['size'] > $max_size) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'message' => 'File size must be less than 10MB.'
            ];
        } else {
            $file_extension = pathinfo($_FILES['file_attachment']['name'], PATHINFO_EXTENSION);
            $file_name = 'msg_' . $user_id . '_' . $receiver_id . '_' . time() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['file_attachment']['tmp_name'], $file_path)) {
                $message = "[File: " . $_FILES['file_attachment']['name'] . "] - " . $file_path;
                $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $receiver_id, $message]);
                
                // Create notification for receiver
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) 
                                       VALUES (?, 'message', 'New File', 
                                               CONCAT(?, ' sent you a file'), 
                                               'fresher/message/index.php?user_id=' || ?)");
                $stmt->execute([$receiver_id, getUserName(), $user_id]);
                
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'icon' => 'check-circle',
                    'message' => 'File sent successfully!'
                ];
                redirect('index.php?user_id=' . $receiver_id);
            }
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Please select a file to upload.'
        ];
    }
}

// Get unread count for sidebar
$unread_count = getUnreadMessages($pdo, $user_id);

// Handle delete conversation
if (isset($_GET['delete']) && isset($_GET['user_id'])) {
    $delete_user_id = (int)$_GET['user_id'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
    $stmt->execute([$user_id, $delete_user_id, $delete_user_id, $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Conversation deleted successfully.'
    ];
    redirect('index.php');
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Messages</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-primary me-2">
                        <i class="fas fa-envelope"></i> <?php echo $unread_count; ?> unread
                    </span>
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Conversations List -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Conversations</h6>
                                <span class="badge bg-primary"><?php echo count($conversations); ?></span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="conversations-list" style="max-height: 600px; overflow-y: auto;">
                                <?php if (!empty($conversations)): ?>
                                    <?php foreach ($conversations as $conv): ?>
                                        <?php
                                        $is_active = $chat_user_id == $conv['other_user_id'];
                                        $is_online = $conv['is_active'] && $conv['last_login'] && strtotime($conv['last_login']) > strtotime('-5 minutes');
                                        ?>
                                        <a href="?user_id=<?php echo $conv['other_user_id']; ?>" 
                                           class="text-decoration-none d-block conversation-link <?php echo $is_active ? 'active' : ''; ?>">
                                            <div class="conversation-item p-3 border-bottom <?php echo $is_active ? 'bg-light' : ''; ?>">
                                                <div class="d-flex align-items-center">
                                                    <div class="position-relative">
                                                        <img src="<?php echo getAvatar($conv); ?>" 
                                                             class="rounded-circle" 
                                                             style="width: 48px; height: 48px; object-fit: cover;">
                                                        <?php if ($is_online): ?>
                                                            <span class="position-absolute bottom-0 end-0 bg-success rounded-circle" 
                                                                  style="width: 12px; height: 12px; border: 2px solid white;"></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="ms-3 flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0"><?php echo htmlspecialchars($conv['full_name']); ?></h6>
                                                            <?php if ($conv['unread_count'] > 0): ?>
                                                                <span class="badge bg-danger rounded-pill"><?php echo $conv['unread_count']; ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <p class="mb-0 text-muted small text-truncate" style="max-width: 150px;">
                                                                <?php 
                                                                $last_msg = $conv['last_message'] ?? '';
                                                                if (strpos($last_msg, '[File:') !== false) {
                                                                    echo '<i class="fas fa-paperclip"></i> File';
                                                                } else {
                                                                    echo htmlspecialchars(substr($last_msg, 0, 40));
                                                                }
                                                                ?>
                                                            </p>
                                                            <small class="text-muted ms-2 flex-shrink-0">
                                                                <?php echo $conv['last_time'] ? getTimeAgo($conv['last_time']) : ''; ?>
                                                            </small>
                                                        </div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-<?php echo $conv['role'] === 'mentor' ? 'chalkboard-teacher' : 'user'; ?>"></i>
                                                            <?php echo ucfirst($conv['role']); ?>
                                                            • <?php echo $conv['total_messages']; ?> messages
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h6>No conversations</h6>
                                        <p class="text-muted small">Start messaging with mentors or other students.</p>
                                         <a href="<?php echo appUrl('public/mentor.php'); ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-user-plus"></i> Find Mentors
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chat Area -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <?php if ($chat_user && $chat_user_id): ?>
                            <?php
                            $is_online = $chat_user['is_active'] && $chat_user['last_login'] && strtotime($chat_user['last_login']) > strtotime('-5 minutes');
                            ?>
                            <!-- Chat Header -->
                            <div class="card-header bg-transparent border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative">
                                            <img src="<?php echo getAvatar($chat_user); ?>" 
                                                 class="rounded-circle" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php if ($is_online): ?>
                                                <span class="position-absolute bottom-0 end-0 bg-success rounded-circle" 
                                                      style="width: 12px; height: 12px; border: 2px solid white;"></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($chat_user['full_name']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo $is_online ? '<span class="text-success">Online</span>' : 'Last seen ' . getTimeAgo($chat_user['last_login'] ?? $chat_user['created_at']); ?>
                                                • <?php echo ucfirst($chat_user['role']); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                  <a class="dropdown-item" href="../mentor/details.php?id=<?php echo $chat_user_id; ?>">
                                                    <i class="fas fa-user"></i> View Profile
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="?delete=1&user_id=<?php echo $chat_user_id; ?>" 
                                                   onclick="return confirm('Delete this conversation?')">
                                                    <i class="fas fa-trash text-danger"></i> Delete Conversation
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Messages -->
                            <div class="card-body" id="chatMessages" style="height: 450px; overflow-y: auto; background: #f8f9fa;">
                                <?php if (!empty($messages)): ?>
                                    <?php 
                                    $last_date = '';
                                    foreach ($messages as $msg): 
                                        $msg_date = date('Y-m-d', strtotime($msg['created_at']));
                                        if ($msg_date != $last_date):
                                            $last_date = $msg_date;
                                    ?>
                                        <div class="text-center my-3">
                                            <span class="badge bg-secondary"><?php echo date('F d, Y', strtotime($msg['created_at'])); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex <?php echo $msg['sender_id'] == $user_id ? 'justify-content-end' : 'justify-content-start'; ?> mb-3">
                                        <?php if ($msg['sender_id'] != $user_id): ?>
                                            <img src="<?php echo getAvatar($msg); ?>" 
                                                 class="rounded-circle me-2 align-self-end" 
                                                 style="width: 32px; height: 32px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div class="message-wrapper" style="max-width: 70%;">
                                            <div class="message-bubble p-2 rounded <?php echo $msg['sender_id'] == $user_id ? 'bg-primary text-white' : 'bg-white shadow-sm'; ?>" 
                                                 style="word-wrap: break-word;">
                                                <?php 
                                                $message_text = htmlspecialchars($msg['message']);
                                                if (strpos($msg['message'], '[File:') !== false && strpos($msg['message'], 'uploads/messages/') !== false) {
                                                    preg_match('/\[File:(.*?)\]\s*-\s*([^\s]+)/', $msg['message'], $matches);
                                                    if (isset($matches[1]) && isset($matches[2])) {
                                                        $file_name = trim($matches[1]);
                                                        $file_path = trim($matches[2]);
                                                        $file_icon = 'file';
                                                        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                                                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                            $file_icon = 'file-image';
                                                        } elseif (in_array($ext, ['pdf'])) {
                                                            $file_icon = 'file-pdf';
                                                        } elseif (in_array($ext, ['doc', 'docx'])) {
                                                            $file_icon = 'file-word';
                                                        }
                                                        echo '<i class="fas fa-' . $file_icon . ' me-1"></i>';
                                                        echo '<a href="../../' . $file_path . '" target="_blank" class="' . ($msg['sender_id'] == $user_id ? 'text-white' : 'text-primary') . '">';
                                                        echo htmlspecialchars($file_name);
                                                        echo '</a>';
                                                    }
                                                } else {
                                                    echo nl2br($message_text);
                                                }
                                                ?>
                                            </div>
                                            <small class="text-muted d-block mt-1 <?php echo $msg['sender_id'] == $user_id ? 'text-end' : ''; ?>" style="font-size: 0.65rem;">
                                                <?php echo getTimeAgo($msg['created_at']); ?>
                                                <?php if ($msg['is_read'] && $msg['sender_id'] == $user_id): ?>
                                                    <i class="fas fa-check-double text-primary ms-1" title="Read"></i>
                                                <?php elseif ($msg['sender_id'] == $user_id): ?>
                                                    <i class="fas fa-check text-muted ms-1" title="Sent"></i>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-comment-dots fa-3x text-muted mb-3"></i>
                                        <h6>No messages yet</h6>
                                        <p class="text-muted">Start the conversation by sending a message below.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Typing Indicator -->
                            <div id="typingIndicator" class="px-3 py-1 d-none">
                                <small class="text-muted"><span id="typingUser"></span> is typing...</small>
                            </div>
                            
                            <!-- Message Input -->
                            <div class="card-footer bg-transparent border-top">
                                <form method="POST" enctype="multipart/form-data" id="messageForm">
                                    <input type="hidden" name="receiver_id" value="<?php echo $chat_user_id; ?>">
                                    <div class="d-flex gap-2">
                                        <div class="flex-grow-1">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('fileInput').click()">
                                                    <i class="fas fa-paperclip"></i>
                                                </button>
                                                <input type="file" name="file_attachment" id="fileInput" class="d-none">
                                                <input type="text" name="message" class="form-control" 
                                                       placeholder="Type a message..." 
                                                       id="messageInput"
                                                       autocomplete="off">
                                                <button type="submit" name="send_message" class="btn btn-primary">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div id="filePreview" class="mt-2 d-none">
                                    <span class="badge bg-light text-dark border p-2">
                                        <i class="fas fa-file"></i> <span id="fileName"></span>
                                        <button type="button" class="btn-close btn-sm ms-2" onclick="clearFile()"></button>
                                    </span>
                                </div>
                            </div>
                            
                        <?php else: ?>
                            <!-- No Chat Selected -->
                            <div class="card-body text-center py-5">
                                <i class="fas fa-comments fa-4x text-muted mb-4"></i>
                                <h5>Select a conversation</h5>
                                <p class="text-muted">Choose a conversation from the list to start messaging.</p>
                                <?php if (empty($conversations)): ?>
                                      <a href="<?php echo appUrl('public/mentor.php'); ?>" class="btn btn-primary mt-3">
                                        <i class="fas fa-user-plus"></i> Find People to Chat With
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to bottom of chat
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // File input handler
    document.getElementById('fileInput').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('filePreview').classList.remove('d-none');
        }
    });
    
    // Enter key to send message
    document.getElementById('messageInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('messageForm').submit();
        }
    });
    
    // Poll for new messages
    <?php if ($chat_user_id): ?>
    setInterval(function() {
        fetch('get-messages.php?user_id=<?php echo $chat_user_id; ?>&last_id=<?php echo !empty($messages) ? end($messages)['id'] : 0; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    // Append new messages
                    const container = document.getElementById('chatMessages');
                    data.forEach(msg => {
                        const messageHtml = createMessageHtml(msg);
                        container.insertAdjacentHTML('beforeend', messageHtml);
                    });
                    container.scrollTop = container.scrollHeight;
                    
                    // Update last message ID
                    lastId = data[data.length - 1].id;
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
    }, 3000);
    <?php endif; ?>
});

function createMessageHtml(msg) {
    const isMine = msg.sender_id == <?php echo $user_id; ?>;
    const messageText = msg.message;
    const isFile = messageText.indexOf('[File:') !== -1;
    
    let content = '';
    if (isFile) {
        const parts = messageText.match(/\[File:(.*?)\]\s*-\s*([^\s]+)/);
        if (parts) {
            const fileName = parts[1];
            const filePath = parts[2];
            content = `<i class="fas fa-file me-1"></i><a href="../../${filePath}" target="_blank" class="${isMine ? 'text-white' : 'text-primary'}">${fileName}</a>`;
        }
    } else {
        content = messageText.replace(/\n/g, '<br>');
    }
    
    return `
        <div class="d-flex ${isMine ? 'justify-content-end' : 'justify-content-start'} mb-3">
            ${!isMine ? `<img src="${msg.avatar || '../../assets/images/default-avatar.png'}" class="rounded-circle me-2 align-self-end" style="width: 32px; height: 32px; object-fit: cover;">` : ''}
            <div class="message-wrapper" style="max-width: 70%;">
                <div class="message-bubble p-2 rounded ${isMine ? 'bg-primary text-white' : 'bg-white shadow-sm'}" style="word-wrap: break-word;">
                    ${content}
                </div>
                <small class="text-muted d-block mt-1 ${isMine ? 'text-end' : ''}" style="font-size: 0.65rem;">
                    ${getTimeAgo(msg.created_at)}
                </small>
            </div>
        </div>
    `;
}

function getTimeAgo(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return diff + 's ago';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
    return date.toLocaleDateString();
}

function clearFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.add('d-none');
}
</script>

<style>
.conversation-link {
    transition: all 0.2s ease;
}

.conversation-link:hover {
    background: #f8f9fa;
}

.conversation-link.active .conversation-item {
    background: #e9ecef;
    border-left: 3px solid #6C63FF;
}

.message-bubble {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Scrollbar styling */
.conversations-list::-webkit-scrollbar,
#chatMessages::-webkit-scrollbar {
    width: 6px;
}

.conversations-list::-webkit-scrollbar-track,
#chatMessages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.conversations-list::-webkit-scrollbar-thumb,
#chatMessages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.conversations-list::-webkit-scrollbar-thumb:hover,
#chatMessages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<?php include '../../includes/footer.php'; ?>