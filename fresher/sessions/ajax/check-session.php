<?php
// AJAX endpoint for checking session status
require_once '../../../config/database.php';
require_once '../../../config/session.php';
require_once '../../../config/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$session_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$session_id) {
    echo json_encode(['error' => 'Invalid session ID']);
    exit();
}

$stmt = $pdo->prepare("SELECT status, scheduled_at, duration FROM sessions WHERE id = ?");
$stmt->execute([$session_id]);
$session = $stmt->fetch();

if (!$session) {
    echo json_encode(['error' => 'Session not found']);
    exit();
}

$current_time = time();
$session_time = strtotime($session['scheduled_at']);
$session_end = $session_time + ($session['duration'] * 60);

$status = $session['status'];
if ($status === 'scheduled') {
    if ($current_time >= $session_time && $current_time <= $session_end) {
        $status = 'ongoing';
    } elseif ($current_time > $session_end) {
        $status = 'completed';
    }
}

// Get participant count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM session_attendance WHERE session_id = ?");
$stmt->execute([$session_id]);
$participants = $stmt->fetchColumn();

echo json_encode([
    'status' => $status,
    'is_active' => $status === 'ongoing' || ($status === 'scheduled' && $current_time >= $session_time && $current_time <= $session_end),
    'is_ended' => $status === 'completed' || $current_time > $session_end,
    'participants' => (int)$participants,
    'time_remaining' => $session_end - $current_time,
    'can_join' => ($status === 'ongoing' || ($status === 'scheduled' && $current_time >= $session_time))
]);
?>