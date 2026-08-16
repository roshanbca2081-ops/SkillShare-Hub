<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

admin_csrf_check();

$action = $_GET['action'] ?? '';
$pdo = getDB();

switch ($action) {
    case 'send':
        $target = sanitize($_POST['target'] ?? 'all');
        $type = sanitize($_POST['type'] ?? 'announcement');
        $message = sanitize($_POST['message'] ?? '');
        
        if (empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Message is required']);
            break;
        }
        
        $users = [];
        if ($target === 'all') {
            $users = $pdo->query("SELECT id FROM users WHERE status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
        } elseif ($target === 'mentors') {
            $users = $pdo->query("SELECT id FROM users WHERE role = 'mentor' AND status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
        } elseif ($target === 'freshers') {
            $users = $pdo->query("SELECT id FROM users WHERE role = 'fresher' AND status = 'active'")->fetchAll(PDO::FETCH_COLUMN);
        }
        
        $payload = json_encode(['message' => $message, 'type' => $type]);
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, payload, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        
        foreach ($users as $userId) {
            $stmt->execute([$userId, $type, $payload]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Notification sent to ' . count($users) . ' users']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}
