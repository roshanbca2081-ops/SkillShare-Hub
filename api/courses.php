<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $stmt = $pdo->prepare("SELECT c.*, u.full_name as mentor_name, u.avatar 
                               FROM courses c 
                               JOIN users u ON c.mentor_id = u.id 
                               WHERE c.status = 'active' 
                               ORDER BY c.created_at DESC LIMIT 20");
        $stmt->execute();
        $courses = $stmt->fetchAll();
        echo json_encode($courses);
        break;
        
    case 'detail':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $stmt = $pdo->prepare("SELECT c.*, u.full_name as mentor_name, u.avatar, u.bio as mentor_bio,
                               (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as student_count
                               FROM courses c 
                               JOIN users u ON c.mentor_id = u.id 
                               WHERE c.id = ?");
        $stmt->execute([$id]);
        $course = $stmt->fetch();
        echo json_encode($course);
        break;
        
    case 'enroll':
        if (!isLoggedIn() || !isFresher()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
        $user_id = getUserId();
        
        $stmt = $pdo->prepare("INSERT INTO enrollments (fresher_id, course_id) VALUES (?, ?)");
        $success = $stmt->execute([$user_id, $course_id]);
        
        echo json_encode(['success' => $success]);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>