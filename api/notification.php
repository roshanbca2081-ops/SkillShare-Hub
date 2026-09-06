<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$action = $_GET['action'] ?? 'list';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = getUserId();

switch ($action) {
    case 'list':
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
        $stmt->execute([$user_id]);
        $notifications = $stmt->fetchAll();
        echo json_encode($notifications);
        break;
        
    case 'unread_count':
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$user_id]);
        $count = $stmt->fetchColumn();
        echo json_encode(['count' => (int)$count]);
        break;
        
    case 'mark_read':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $success = $stmt->execute([$id, $user_id]);
        echo json_encode(['success' => $success]);
        break;
        
    case 'mark_all_read':
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $success = $stmt->execute([$user_id]);
        echo json_encode(['success' => $success]);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>