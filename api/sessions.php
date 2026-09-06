<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $stmt = $pdo->prepare("SELECT s.*, u.full_name as mentor_name 
                               FROM sessions s 
                               JOIN users u ON s.mentor_id = u.id 
                               WHERE s.scheduled_at > NOW() AND s.status = 'scheduled'
                               ORDER BY s.scheduled_at LIMIT 20");
        $stmt->execute();
        $sessions = $stmt->fetchAll();
        echo json_encode($sessions);
        break;
        
    case 'book':
        if (!isLoggedIn() || !isFresher()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        $session_id = isset($_POST['session_id']) ? (int)$_POST['session_id'] : 0;
        $user_id = getUserId();
        
        $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id) VALUES (?, ?)");
        $success = $stmt->execute([$user_id, $session_id]);
        
        echo json_encode(['success' => $success]);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>