<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode(['results' => []]);
    exit();
}

$results = [];

// Search courses
$stmt = $pdo->prepare("SELECT id, title, description, 'course' as type, thumbnail, mentor_id, price 
                       FROM courses WHERE status = 'active' AND (title LIKE ? OR description LIKE ?) 
                       LIMIT 10");
$stmt->execute(["%$query%", "%$query%"]);
$courses = $stmt->fetchAll();
foreach ($courses as $course) {
    $results[] = $course;
}

// Search mentors
$stmt = $pdo->prepare("SELECT id, full_name, bio, 'mentor' as type, avatar, skills 
                       FROM users WHERE role = 'mentor' AND is_active = 1 AND (full_name LIKE ? OR bio LIKE ? OR skills LIKE ?) 
                       LIMIT 10");
$stmt->execute(["%$query%", "%$query%", "%$query%"]);
$mentors = $stmt->fetchAll();
foreach ($mentors as $mentor) {
    $results[] = $mentor;
}

echo json_encode(['results' => $results]);
?>