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
    case 'status':
        $status = sanitize($_POST['status'] ?? 'active');
        $pdo->prepare("UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?")->execute([$status, $id]);
        echo json_encode(['success' => true, 'message' => 'User status updated']);
        break;

     case 'save':
         $data = [
             'full_name' => sanitize($_POST['full_name'] ?? ''),
             'email' => sanitize($_POST['email'] ?? ''),
             'phone' => sanitize($_POST['phone'] ?? ''),
             'role' => sanitize($_POST['role'] ?? 'fresher'),
             'status' => sanitize($_POST['status'] ?? 'active'),
         ];
         
         if ($id) {
             if (!empty($_POST['password'])) {
                 $data['password_hash'] = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]);
             }
             $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, role = ?, status = ?, updated_at = NOW() " . (!empty($_POST['password']) ? ", password_hash = ?" : "") . " WHERE id = ?")
                 ->execute(array_merge(array_values($data), !empty($_POST['password']) ? [password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]), $id] : [$id]));
             echo json_encode(['success' => true, 'message' => 'User updated']);
         } else {
             if (empty($_POST['password'])) {
                 echo json_encode(['success' => false, 'message' => 'Password required for new user']);
                 break;
             }
             $data['password_hash'] = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]);
             $data['is_verified'] = 1;
             $data['created_at'] = date('Y-m-d H:i:s');
             $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, status, is_verified, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
             $stmt->execute([$data['full_name'], $data['email'], $data['phone'], $data['password_hash'], $data['role'], $data['status'], $data['is_verified'], $data['created_at']]);
             $newId = $pdo->lastInsertId();
             if ($newId) {
                 if ($data['role'] === 'mentor') {
                     $pdo->prepare("INSERT IGNORE INTO mentors (user_id) VALUES (?)")->execute([$newId]);
                 } elseif ($data['role'] === 'fresher') {
                     $pdo->prepare("INSERT IGNORE INTO freshers (user_id) VALUES (?)")->execute([$newId]);
                 }
             }
             echo json_encode(['success' => true, 'message' => 'User created', 'id' => $newId]);
         }
         break;

    case 'delete':
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'User deleted']);
        break;

    case 'delete_field':
        $pdo->prepare("DELETE FROM academic_fields WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Field deleted']);
        break;

    case 'delete_notification':
        $pdo->prepare("DELETE FROM notifications WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Notification deleted']);
        break;

    case 'delete_review':
        $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Review deleted']);
        break;

    case 'delete_question':
        $pdo->prepare("DELETE FROM interview_questions WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Question deleted']);
        break;

    case 'delete_research':
        $pdo->prepare("DELETE FROM research WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Research deleted']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}
