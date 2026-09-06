<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = getUserId();

// Get booking details
$stmt = $pdo->prepare("
    SELECT 
        b.*, 
        s.mentor_id, 
        s.title, 
        s.scheduled_at,
        u.full_name AS student_name, 
        u.id AS student_id
    FROM bookings b
    JOIN sessions s ON b.session_id = s.id
    JOIN users u ON b.fresher_id = u.id
    WHERE b.id = ? 
      AND s.mentor_id = ?
");

$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect('index.php');
}

// Approve booking
$stmt = $pdo->prepare("
    UPDATE bookings 
    SET status = 'approved' 
    WHERE id = ?
");

$stmt->execute([$booking_id]);

// Send notification to student
$stmt = $pdo->prepare("
    INSERT INTO notifications 
        (user_id, type, title, message, link)
    VALUES 
        (
            ?,
            'booking_approved',
            'Booking Approved',
            CONCAT('Your session \"', ?, '\" has been approved.'),
            CONCAT('fresher/booking/view.php?id=', ?)
        )
");

$stmt->execute([
    $booking['student_id'],
    $booking['title'],
    $booking_id
]);

// Success message
$_SESSION['alert'] = [
    'type' => 'success',
    'icon' => 'check-circle',
    'message' => 'Booking approved successfully!'
];

redirect('index.php');
?>