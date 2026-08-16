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
    case 'save':
        $settings = $_POST['settings'] ?? [];
        $group = sanitize($_POST['group'] ?? 'general');
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (key_name, value, group_name, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE value = ?, updated_at = NOW()");
            $stmt->execute([$key, $value, $group, $value]);
        }
        echo json_encode(['success' => true, 'message' => 'Settings saved']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}
