<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$session_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$session_id) {
    redirect('index.php');
}

// Verify session belongs to this mentor
$stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = ? AND mentor_id = ?");
$stmt->execute([$session_id, $user_id]);
$session = $stmt->fetch();

if (!$session) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'icon' => 'exclamation-circle',
        'message' => 'Session not found or access denied.'
    ];
    redirect('index.php');
}

if ($session['status'] === 'cancelled') {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-triangle',
        'message' => 'Session is already cancelled.'
    ];
    redirect('index.php');
}

// Cancel the session
$stmt = $pdo->prepare("UPDATE sessions SET status = 'cancelled' WHERE id = ? AND mentor_id = ?");
$stmt->execute([$session_id, $user_id]);

// Also cancel all pending/approved bookings for this session
$stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE session_id = ? AND status IN ('pending','approved')");
$stmt->execute([$session_id]);

// Notify booked students
$stmt = $pdo->prepare("SELECT DISTINCT b.fresher_id FROM bookings b WHERE b.session_id = ?");
$stmt->execute([$session_id]);
$students = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($students as $student_id) {
    createNotification(
        $pdo,
        $student_id,
        'session_cancelled',
        'Session Cancelled',
        'The session "' . $session['title'] . '" has been cancelled by the mentor.',
        null,
        'calendar-times'
    );
}

$_SESSION['alert'] = [
    'type' => 'success',
    'icon' => 'check-circle',
    'message' => 'Session "' . htmlspecialchars($session['title']) . '" has been cancelled. All bookings have been notified.'
];
redirect('index.php');
