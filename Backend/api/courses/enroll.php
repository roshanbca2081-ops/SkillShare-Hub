<?php
/**
 * API - Enroll user in a course
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;
$courseId = $data['course_id'] ?? null;

if (!$userId || !$courseId) {
    sendError('Missing user_id or course_id', 400);
}

try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO user_courses (user_id, course_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE created_at = created_at");
    $stmt->execute([(int)$userId, (int)$courseId]);
    sendSuccess(['enrolled' => true], 'Enrolled successfully');
} catch (Exception $e) {
    sendError('Enrollment failed: ' . $e->getMessage(), 500);
}
