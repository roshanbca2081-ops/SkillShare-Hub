<?php
$page_title = 'Resource Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

$resource_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$resource_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid resource ID']);
    exit;
}

$stmt = $pdo->prepare("SELECT r.*, u.full_name as mentor_name, c.title as course_title 
                       FROM resources r 
                       JOIN users u ON r.mentor_id = u.id 
                       JOIN courses c ON r.course_id = c.id 
                       WHERE r.id = ? AND r.is_public = 1");
$stmt->execute([$resource_id]);
$resource = $stmt->fetch();

if (!$resource) {
    echo json_encode(['success' => false, 'message' => 'Resource not found']);
    exit;
}

echo json_encode([
    'success' => true,
    'resource' => [
        'id' => $resource['id'],
        'title' => $resource['title'],
        'description' => $resource['description'],
        'file_url' => $resource['file_url'],
        'file_type' => $resource['file_type'],
        'mentor_name' => $resource['mentor_name'],
        'course_title' => $resource['course_title'],
        'download_count' => $resource['download_count'],
        'created_at' => formatDate($resource['created_at'])
    ]
]);
