<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'authenticated' => false]);
    exit;
}

$pdo = getDB();
$userId = getUserId();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT key_name, value, type, group_name FROM settings WHERE is_public = 1 OR (group_name = 'general')");
    $stmt->execute();
    $settings = [];
    foreach ($stmt->fetchAll() as $row) {
        $settings[$row['key_name']] = $row['value'];
    }
    echo json_encode(['success' => true, 'data' => $settings]);
    exit;
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $user = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?")->execute([$userId]);
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!password_verify($current, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit;
        }

        if (strlen($new) < 6) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
            exit;
        }

        if ($new !== $confirm) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            exit;
        }

        $pdo->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?")->execute([password_hash($new, PASSWORD_BCRYPT), $userId]);
        echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        exit;
    }

    if ($action === 'update_settings') {
        $allowedKeys = ['site_name', 'site_tagline', 'currency_symbol', 'tax_rate', 'maintenance_mode', 'registration_enabled'];
        foreach ($allowedKeys as $key) {
            if (isset($_POST[$key])) {
                $value = sanitize($_POST[$key]);
                $pdo->prepare("UPDATE settings SET value = ?, updated_at = NOW() WHERE key_name = ?")->execute([$value, $key]);
            }
        }
        echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
