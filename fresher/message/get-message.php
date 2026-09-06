<?php
// AJAX endpoint for getting new messages
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode([]);
    exit();
}

$user_id = getUserId();
$chat_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

if (!$chat_user_id) {
    echo json_encode([]);
    exit();
}

$stmt = $pdo->prepare("SELECT m.*, u.full_name, u.avatar 
                       FROM messages m 
                       JOIN users u ON m.sender_id = u.id 
                       WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
                          OR (m.sender_id = ? AND m.receiver_id = ?)) 
                       AND m.id > ?
                       ORDER BY m.created_at ASC");
$stmt->execute([$user_id, $chat_user_id, $chat_user_id, $user_id, $last_id]);
$messages = $stmt->fetchAll();

// Mark messages as read
if (!empty($messages)) {
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1, read_at = NOW() 
                           WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
    $stmt->execute([$chat_user_id, $user_id]);
}

echo json_encode($messages);
?>