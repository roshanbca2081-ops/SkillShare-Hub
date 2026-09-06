<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        $user_id = getUserId();
        $role = getUserRole();
        
        if ($role === 'fresher') {
            $stmt = $pdo->prepare("SELECT b.*, s.title, s.scheduled_at, u.full_name as mentor_name 
                                   FROM bookings b 
                                   JOIN sessions s ON b.session_id = s.id 
                                   JOIN users u ON s.mentor_id = u.id 
                                   WHERE b.fresher_id = ? 
                                   ORDER BY b.booking_date DESC");
            $stmt->execute([$user_id]);
        } else {
            $stmt = $pdo->prepare("SELECT b.*, s.title, s.scheduled_at, u.full_name as fresher_name 
                                   FROM bookings b 
                                   JOIN sessions s ON b.session_id = s.id 
                                   JOIN users u ON b.fresher_id = u.id 
                                   WHERE s.mentor_id = ? 
                                   ORDER BY b.booking_date DESC");
            $stmt->execute([$user_id]);
        }
        
        $bookings = $stmt->fetchAll();
        echo json_encode($bookings);
        break;
        
    case 'cancel':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
        $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
        
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $success = $stmt->execute([$booking_id]);
        
        echo json_encode(['success' => $success]);
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>