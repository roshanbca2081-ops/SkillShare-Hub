<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=UTF-8');

$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$paymentId = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : 0;

if (!$bookingId || !$paymentId) {
    header('Location: ' . BASE_URL . 'dashboard/fresher/bookings.php?status=error');
    exit();
}

$pdo = getDB();

$booking = $pdo->prepare("SELECT b.*, p.id as payment_id, p.transaction_ref FROM bookings b JOIN payments p ON p.booking_id = b.id WHERE b.id = ? AND p.id = ?");
$booking->execute([$bookingId, $paymentId]);
$record = $booking->fetch();

if (!$record) {
    header('Location: ' . BASE_URL . 'dashboard/fresher/bookings.php?status=error');
    exit();
}

$transactionUuid = $record['transaction_ref'];
$amount = (float)$record['total_amount'];
$paymentConfig = require __DIR__ . '/config.php';
$mode = $paymentConfig['mode'] ?? 'uat';
$esewa = $paymentConfig['esewa'][$mode] ?? [];

$statusUrl = rtrim($esewa['status_url'], '/') . '/' . rawurlencode($transactionUuid);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $statusUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$verified = false;
$status = 'failed';

if ($httpCode === 200 && $response) {
    $xml = simplexml_load_string($response);
    if ($xml && isset($xml->status)) {
        $esewaStatus = (string)$xml->status;
        if ($esewaStatus === 'COMPLETE') {
            $verified = true;
            $status = 'paid';
        } elseif ($esewaStatus === 'PENDING') {
            $status = 'pending';
        } else {
            $status = 'failed';
        }
    }
}

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("UPDATE payments SET status = ?, payment_date = NOW() WHERE id = ?");
    $stmt->execute([$status, $paymentId]);

    if ($verified) {
        $pdo->prepare("UPDATE bookings SET payment_status = 'paid', status = 'confirmed', updated_at = NOW() WHERE id = ?")->execute([$bookingId]);

        $stmt = $pdo->prepare("SELECT user_id, mentor_id FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);
        $b = $stmt->fetch();

        $startAt = date('Y-m-d H:i:s', strtotime($booking['session_date'] . ' ' . $booking['session_time']));
        $endAt = date('Y-m-d H:i:s', strtotime($startAt . ' + ' . $booking['duration'] . ' minutes'));

        $stmt = $pdo->prepare("INSERT INTO sessions (booking_id, mentor_id, user_id, session_title, session_date, start_at, end_at, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'scheduled', NOW())");
        $stmt->execute([$bookingId, $b['mentor_id'], $b['user_id'], $booking['session_title'], $booking['session_date'], $startAt, $endAt]);

        $payload = json_encode(['booking_id' => $bookingId, 'payment_id' => $paymentId]);
        $pdo->prepare("INSERT INTO notifications (user_id, type, payload, is_read, created_at) VALUES (?, 'payment_verified', ?, 0, NOW())")->execute([$b['user_id'], $payload]);
        $pdo->prepare("INSERT INTO notifications (user_id, type, payload, is_read, created_at) VALUES (?, 'booking_confirmed', ?, 0, NOW())")->execute([$b['mentor_id'], $payload]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}

if ($verified) {
    header('Location: ' . BASE_URL . 'dashboard/fresher/bookings.php?status=paid');
} else {
    header('Location: ' . BASE_URL . 'dashboard/fresher/bookings.php?status=failed');
}
exit();
