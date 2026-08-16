<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'authenticated' => false]);
    exit;
}

$pdo = getDB();
$userId = getUserId();
$action = $_GET['action'] ?? 'list';

if ($action === 'conversations') {
    $sql = "SELECT m.*, u.full_name as other_name, u.profile_picture as other_avatar, u.role as other_role FROM messages m JOIN users u ON (m.from_user = u.id OR m.to_user = u.id) WHERE (m.from_user = ? OR m.to_user = ?) AND u.id != ? AND m.is_deleted_sender = 0 ORDER BY m.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $userId, $userId]);
    $messages = $stmt->fetchAll();

    $conversations = [];
    foreach ($messages as $msg) {
        $otherId = $msg['from_user'] == $userId ? $msg['to_user'] : $msg['from_user'];
        $key = $otherId;
        if (!isset($conversations[$key])) {
            $conversations[$key] = [
                'user_id' => $otherId,
                'name' => $msg['other_name'],
                'avatar' => $msg['other_avatar'],
                'role' => $msg['other_role'],
                'last_message' => $msg['message'],
                'last_time' => $msg['created_at'],
                'unread' => 0,
            ];
        }
        if ($msg['to_user'] == $userId && $msg['is_read'] == 0) {
            $conversations[$key]['unread']++;
        }
    }

    echo json_encode(['success' => true, 'data' => array_values($conversations)]);
    exit;
}

if ($action === 'conversation' && isset($_GET['id'])) {
    $otherId = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT m.*, u.full_name as from_name, u.profile_picture as from_avatar FROM messages m JOIN users u ON m.from_user = u.id WHERE ((m.from_user = ? AND m.to_user = ?) OR (m.from_user = ? AND m.to_user = ?)) AND m.is_deleted_sender = 0 AND m.is_deleted_receiver = 0 ORDER BY m.created_at ASC");
    $stmt->execute([$userId, $otherId, $otherId, $userId]);
    $messages = $stmt->fetchAll();

    $pdo->prepare("UPDATE messages SET is_read = 1, read_at = NOW() WHERE from_user = ? AND to_user = ? AND is_read = 0")->execute([$otherId, $userId]);

    echo json_encode(['success' => true, 'data' => $messages]);
    exit;
}

if ($action === 'send' && $_POST) {
    $toId = (int)($_POST['to_user'] ?? 0);
    $content = sanitize($_POST['message'] ?? '');

    if (!$toId || !$content) {
        echo json_encode(['success' => false, 'message' => 'Recipient and message required']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, $toId, $content]);

    $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, payload, is_read, created_at) VALUES (?, 'new_message', 'New Message', ?, ?, 0, NOW())")->execute([$toId, $content, json_encode(['from_user' => $userId, 'preview' => substr($content, 0, 100)])]);

    echo json_encode(['success' => true, 'message' => 'Message sent']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
