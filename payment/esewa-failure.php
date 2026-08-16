<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: text/html; charset=UTF-8');

$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$paymentId = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : 0;

if ($bookingId && $paymentId) {
    $pdo = getDB();
    $pdo->prepare("UPDATE payments SET status = 'failed' WHERE id = ?")->execute([$paymentId]);
    $pdo->prepare("UPDATE bookings SET payment_status = 'failed' WHERE id = ?")->execute([$bookingId]);
}

header('Location: ' . BASE_URL . 'dashboard/fresher/bookings.php?status=failed');
exit();
