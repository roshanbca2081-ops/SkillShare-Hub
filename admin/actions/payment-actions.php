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
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch ($action) {
    case 'update_status':
        $status = sanitize($_POST['status'] ?? 'pending');
        $allowed = ['pending', 'paid', 'failed', 'refunded'];
        if (!in_array($status, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            break;
        }
        $pdo->prepare("UPDATE payments SET status = ? WHERE id = ?")->execute([$status, $id]);
        echo json_encode(['success' => true, 'message' => 'Payment updated']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}
