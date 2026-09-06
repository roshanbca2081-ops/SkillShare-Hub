<?php
// Delete message endpoint
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$message_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($message_id) {
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ? AND sender_id = ?");
    $stmt->execute([$message_id, $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Message deleted successfully.'
    ];
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'icon' => 'exclamation-circle',
        'message' => 'Message not found.'
    ];
}

redirect('index.php');
?>