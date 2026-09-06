<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();
$booking_id = (int) ($_GET['id'] ?? 0);
$user_id = getUserId();

$stmt = $pdo->prepare("SELECT b.id, b.fresher_id, s.title FROM bookings b JOIN sessions s ON s.id = b.session_id WHERE b.id = ? AND s.mentor_id = ?");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();

if ($booking) {
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ?");
    $stmt->execute([$booking_id]);
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) VALUES (?, 'booking_rejected', 'Booking Rejected', ?, 'fresher/booking/index.php')");
    $stmt->execute([$booking['fresher_id'], 'Your session "' . $booking['title'] . '" was rejected.']);
    $_SESSION['alert'] = ['type' => 'warning', 'icon' => 'times-circle', 'message' => 'Booking rejected.'];
}

redirect('index.php');
?>
