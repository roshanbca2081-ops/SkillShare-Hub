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

if ($action === 'list') {
    $stmt = $pdo->prepare("SELECT id, title, message, type, link, icon, color, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 30");
    $stmt->execute([$userId]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'unread_count') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    echo json_encode(['success' => true, 'data' => ['count' => (int)$stmt->fetchColumn()]]);
    exit;
}

if ($action === 'mark_read' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $pdo->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND user_id = ?")->execute([$id, $userId]);
    echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
    exit;
}

if ($action === 'mark_all_read') {
    $pdo->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ?")->execute([$userId]);
    echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
