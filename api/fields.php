<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

$pdo = getDB();

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $stmt = $pdo->query("SELECT id, name, slug, icon, description, color, total_courses, status FROM academic_fields WHERE status = 'active' ORDER BY sort_order, name");
    $fields = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $fields]);
}

elseif ($action === 'show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM academic_fields WHERE id = ? AND status = 'active'");
    $stmt->execute([$id]);
    $field = $stmt->fetch();

    if (!$field) {
        echo json_encode(['success' => false, 'message' => 'Field not found']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, name, slug, description, duration, level, rating, total_mentors, total_students, status FROM courses WHERE academic_field_id = ? AND status = 'active' ORDER BY name");
    $stmt->execute([$id]);
    $field['courses'] = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $field]);
}
