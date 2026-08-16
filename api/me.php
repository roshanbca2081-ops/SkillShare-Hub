<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'authenticated' => false, 'message' => 'Not logged in']);
    exit;
}

$user = getCurrentUser();
if (!$user) {
    echo json_encode(['success' => false, 'authenticated' => false, 'message' => 'User not found']);
    exit;
}

$pdo = getDB();

$user['field_name'] = null;
if ($user['academic_field_id']) {
    $stmt = $pdo->prepare("SELECT name FROM academic_fields WHERE id = ?");
    $stmt->execute([$user['academic_field_id']]);
    $user['field_name'] = $stmt->fetchColumn();
}
$user['course_name'] = null;
if ($user['course_id']) {
    $stmt = $pdo->prepare("SELECT name FROM courses WHERE id = ?");
    $stmt->execute([$user['course_id']]);
    $user['course_name'] = $stmt->fetchColumn();
}

echo json_encode(['success' => true, 'authenticated' => true, 'data' => $user]);
