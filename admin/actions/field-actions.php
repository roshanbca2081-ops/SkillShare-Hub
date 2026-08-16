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
            'slug' => sanitize($_POST['slug'] ?? ''),
            'color' => sanitize($_POST['color'] ?? '#3b82f6'),
            'icon' => sanitize($_POST['icon'] ?? 'fa-book'),
            'status' => sanitize($_POST['status'] ?? 'active'),
            'description' => sanitize($_POST['description'] ?? ''),
        ];
        
        if ($id) {
            $stmt = $pdo->prepare("UPDATE academic_fields SET name = ?, slug = ?, color = ?, icon = ?, status = ?, description = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$data['name'], $data['slug'], $data['color'], $data['icon'], $data['status'], $data['description'], $id]);
            echo json_encode(['success' => true, 'message' => 'Field updated']);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("INSERT INTO academic_fields (name, slug, color, icon, status, description, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$data['name'], $data['slug'], $data['color'], $data['icon'], $data['status'], $data['description'], $data['created_at']]);
            $newId = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Field created', 'id' => $newId]);
        }
        break;

    case 'delete':
        $pdo->prepare("DELETE FROM academic_fields WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Field deleted']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}
