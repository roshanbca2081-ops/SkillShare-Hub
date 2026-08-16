<?php
require_once __DIR__ . '/../config.php';
$paymentConfig = require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if (!$bookingId) {
    header('Location: ' . BASE_URL . 'dashboard/fresher/bookings.php');
    exit();
}

$pdo = getDB();
$userId = getUserId();
$role = getUserRole();

$stmt = $pdo->prepare("SELECT b.*, u.full_name as mentor_name FROM bookings b JOIN users u ON b.mentor_id = u.id WHERE b.id = ? AND b.user_id = ? AND b.payment_status = 'pending'");
$stmt->execute([$bookingId, $userId]);
$booking = $stmt->fetch();

if (!$booking) {
    header('Location: ' . BASE_URL . 'dashboard/fresher/bookings.php');
    exit();
}

$amount = (float)$booking['total_amount'];
$taxAmount = 0;
$totalAmount = $amount + $taxAmount;
$transactionUuid = 'TXN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(8)));
$mode = $paymentConfig['mode'] ?? 'uat';
$esewa = $paymentConfig['esewa'][$mode] ?? [];

$pdo->prepare("INSERT INTO payments (user_id, booking_id, amount, tax_amount, total_amount, payment_method, transaction_ref, status, created_at) VALUES (?, ?, ?, ?, ?, 'esewa', ?, 'pending', NOW())")->execute([$userId, $bookingId, $amount, $taxAmount, $totalAmount, $transactionUuid]);

$paymentId = (int)$pdo->lastInsertId();

$signedFields = "total_amount,transaction_uuid,product_code";
$signature = base64_encode(hash_hmac('sha256', $signedFields, $esewa['secret_key'], true));

$params = [
    'amount' => number_format($amount, 2, '.', ''),
    'tax_amount' => number_format($taxAmount, 2, '.', ''),
    'total_amount' => number_format($totalAmount, 2, '.', ''),
    'transaction_uuid' => $transactionUuid,
    'product_code' => $esewa['merchant_code'],
    'product_service_charge' => '0',
    'product_delivery_charge' => '0',
    'success_url' => $esewa['success_url'] . '?booking_id=' . $bookingId . '&payment_id=' . $paymentId,
    'failure_url' => $esewa['failure_url'] . '?booking_id=' . $bookingId . '&payment_id=' . $paymentId,
    'signed_field_names' => $signedFields,
    'signature' => $signature,
];

$actionUrl = rtrim($esewa['base_url'], '/') . '/main';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to eSewa...</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0a0a1a; color: #fff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .box { text-align: center; padding: 40px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; }
        .spinner { width: 40px; height: 40px; border: 4px solid rgba(255,255,255,0.1); border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="box">
        <div class="spinner"></div>
        <h2>Redirecting to eSewa</h2>
        <p style="color:rgba(255,255,255,0.6);">Please wait while we redirect you to complete the payment...</p>
    </div>
    <form id="esewaForm" method="POST" action="<?php echo htmlspecialchars($actionUrl); ?>">
        <?php foreach ($params as $k => $v): ?>
            <input type="hidden" name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>">
        <?php endforeach; ?>
    </form>
    <script>document.getElementById('esewaForm').submit();</script>
</body>
</html>
