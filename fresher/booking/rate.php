<?php
$page_title = 'Rate Session';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$booking_id = isset($_GET['booking']) ? (int)$_GET['booking'] : 0;
$rating_value = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;

if ($rating_value < 1 || $rating_value > 5) {
    redirect('index.php');
}

$user_id = getUserId();

// Verify booking exists and is completed
$stmt = $pdo->prepare("SELECT b.*, s.mentor_id, s.id as session_id 
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       WHERE b.id = ? AND b.fresher_id = ? AND (b.status = 'completed' OR s.scheduled_at < NOW())");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect('index.php');
}

// Check if already rated
$stmt = $pdo->prepare("SELECT id FROM ratings WHERE fresher_id = ? AND session_id = ?");
$stmt->execute([$user_id, $booking['session_id']]);
if ($stmt->fetch()) {
    $_SESSION['alert'] = [
        'type' => 'info',
        'icon' => 'info-circle',
        'message' => 'You have already rated this session.'
    ];
    redirect('view.php?id=' . $booking_id);
}

// Save rating
$stmt = $pdo->prepare("INSERT INTO ratings (mentor_id, fresher_id, session_id, rating) 
                       VALUES (?, ?, ?, ?)");
$stmt->execute([$booking['mentor_id'], $user_id, $booking['session_id'], $rating_value]);

$_SESSION['alert'] = [
    'type' => 'success',
    'icon' => 'check-circle',
    'message' => 'Thank you for rating the session!'
];
redirect('view.php?id=' . $booking_id);
?>