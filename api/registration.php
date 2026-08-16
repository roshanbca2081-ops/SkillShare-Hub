<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

$pdo = getDB();
$action = $_GET['action'] ?? '';

if ($action === 'fields') {
    $stmt = $pdo->query("SELECT id, name, slug, icon, color, description FROM academic_fields WHERE status = 'active' ORDER BY sort_order, name ASC");
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'courses' && isset($_GET['field_id'])) {
    $fieldId = (int)$_GET['field_id'];
    $stmt = $pdo->prepare("SELECT id, name, slug, description, duration, level, icon FROM courses WHERE academic_field_id = ? AND status = 'active' ORDER BY name ASC");
    $stmt->execute([$fieldId]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'course-detail' && isset($_GET['course_id'])) {
    $courseId = (int)$_GET['course_id'];
    $stmt = $pdo->prepare("SELECT c.*, f.name as field_name FROM courses c JOIN academic_fields f ON f.id = c.academic_field_id WHERE c.id = ? AND c.status = 'active'");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch();
    if ($course) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM skills WHERE course_id = ? AND status = 'active'");
        $stmt->execute([$courseId]);
        $course['skill_count'] = (int)$stmt->fetchColumn();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'mentor' AND status = 'active' AND course_id = ?");
        $stmt->execute([$courseId]);
        $course['mentor_count'] = (int)$stmt->fetchColumn();
    }
    echo json_encode(['success' => true, 'data' => $course]);
    exit;
}

if ($action === 'skills' && isset($_GET['course_id'])) {
    $courseId = (int)$_GET['course_id'];
    $stmt = $pdo->prepare("SELECT id, name, slug, description, category, difficulty FROM skills WHERE course_id = ? AND status = 'active' ORDER BY name ASC");
    $stmt->execute([$courseId]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
