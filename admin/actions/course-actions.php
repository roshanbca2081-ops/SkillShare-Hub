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
     case 'save':
         $data = [
             'name' => sanitize($_POST['name'] ?? ''),
             'academic_field_id' => (int)($_POST['academic_field_id'] ?? 0),
             'level' => sanitize($_POST['level'] ?? 'beginner'),
             'status' => sanitize($_POST['status'] ?? 'active'),
         ];
         if (!empty($_POST['description'])) {
             $data['description'] = sanitize($_POST['description']);
         }
         
         if ($id) {
             $pdo->prepare("UPDATE courses SET name = ?, academic_field_id = ?, level = ?, status = ?, updated_at = NOW() " . (!empty($data['description']) ? ", description = ?" : "") . " WHERE id = ?")
                 ->execute(!empty($data['description']) ? array_merge([$data['name'], $data['academic_field_id'], $data['level'], $data['status'], $data['description'], $id]) : [$data['name'], $data['academic_field_id'], $data['level'], $data['status'], $id]);
             echo json_encode(['success' => true, 'message' => 'Course updated']);
         } else {
             $data['created_at'] = date('Y-m-d H:i:s');
             $stmt = $pdo->prepare("INSERT INTO courses (name, academic_field_id, level, status, created_at" . (!empty($data['description']) ? ", description" : "") . ") VALUES (?, ?, ?, ?, ?" . (!empty($data['description']) ? ", ?" : "") . ")");
             $params = !empty($data['description']) ? [$data['name'], $data['academic_field_id'], $data['level'], $data['status'], $data['created_at'], $data['description']] : [$data['name'], $data['academic_field_id'], $data['level'], $data['status'], $data['created_at']];
             $stmt->execute($params);
             $newId = $pdo->lastInsertId();
             echo json_encode(['success' => true, 'message' => 'Course created', 'id' => $newId]);
         }
         break;

    case 'delete':
        $pdo->prepare("DELETE FROM courses WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Course deleted']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}
