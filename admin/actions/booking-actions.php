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
    case 'update_status':
        $status = sanitize($_POST['status'] ?? 'pending');
        $allowed = ['pending', 'accepted', 'rejected', 'cancelled', 'completed'];
        if (!in_array($status, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            break;
        }
        $pdo->prepare("UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?")->execute([$status, $id]);
        
        if ($status === 'accepted') {
            $pdo->prepare("INSERT IGNORE INTO sessions (booking_id, mentor_id, user_id, course_id, status, created_at) SELECT id, mentor_id, user_id, course_id, 'scheduled', NOW() FROM bookings WHERE id = ?")->execute([$id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Booking updated']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}
