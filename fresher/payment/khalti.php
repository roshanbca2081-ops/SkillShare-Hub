<?php
$page_title = 'Khalti Payment';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
$paymentConfig = require_once __DIR__ . '/../../config/payment-gateways.php';
requireFresher();
$userId = (int)getUserId();
$message = '';
$messageType = 'warning';

$pidx = trim((string)($_GET['pidx'] ?? ''));
$orderReference = trim((string)($_GET['purchase_order_id'] ?? ''));
if ($pidx !== '' || $orderReference !== '') {
    try {
        $order = parsePaymentOrderReference($orderReference);
        if (!$order || !preg_match('/\A[a-zA-Z0-9_-]{8,100}\z/', $pidx)) {
            throw new RuntimeException('Invalid payment callback.');
        }
        $stmt = $pdo->prepare("SELECT p.id, p.fresher_id, p.amount, p.status, p.payment_method, p.transaction_id, p.metadata, s.id AS session_id, s.price AS session_price FROM payments p JOIN bookings b ON b.id = ? AND b.fresher_id = p.fresher_id JOIN sessions s ON s.id = b.session_id WHERE p.id = ? AND JSON_UNQUOTE(JSON_EXTRACT(p.metadata, '$.booking_id')) = ? AND p.fresher_id = ? AND p.payment_method = 'khalti' LIMIT 1");
        $stmt->execute([$order['booking_id'], $order['payment_id'], (string)$order['booking_id'], $userId]);
        $payment = $stmt->fetch();
        $metadata = $payment ? json_decode((string)$payment['metadata'], true) : null;
        if (!$payment || $payment['transaction_id'] !== $pidx || ($metadata['order_reference'] ?? '') !== $orderReference) {
            throw new RuntimeException('Payment order does not match the callback.');
        }
        $lookup = khaltiLookupPayment($pidx);
        $expectedPaisa = paymentNprForUsd($payment['session_price']) * 100;
        if (($lookup['pidx'] ?? '') !== $pidx
            || (int)($lookup['total_amount'] ?? -1) !== $expectedPaisa
            || (float)$payment['amount'] !== (float)$payment['session_price']) {
            throw new RuntimeException('Khalti transaction details did not match this order.');
        }
        if (($lookup['status'] ?? '') === 'Completed' && empty($lookup['refunded'])) {
            if (!finalizeSessionPayment($pdo, $order['payment_id'], $order['booking_id'], 'khalti', $pidx, true)) {
                throw new RuntimeException('Payment could not be completed for this booking.');
            }
            redirect('fresher/payment/sucess.php?session_id=' . (int)$payment['session_id']);
        }
        $providerStatus = (string)($lookup['status'] ?? 'Unknown');
        if (in_array($providerStatus, ['Expired', 'User canceled', 'Refunded', 'Partially Refunded', 'Canceled', 'Failed'], true)) {
            finalizeSessionPayment($pdo, $order['payment_id'], $order['booking_id'], 'khalti', $pidx, false);
            $message = 'Khalti did not complete this payment. You have not been charged by SkillShare Hub.';
            $messageType = 'danger';
        } else {
            $message = 'Khalti has not confirmed this payment yet. Your booking remains pending; refresh this page later to check again.';
        }
    } catch (Throwable $error) {
        error_log('Khalti callback verification failed: ' . $error->getMessage());
        $message = 'We could not verify the Khalti payment. It has not been marked paid. If your wallet was charged, contact support with your Khalti reference.';
        $messageType = 'danger';
    }
}
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>
<div class="container-fluid"><div class="row">
    <div class="col-md-3 col-lg-2 d-md-block sidebar p-0"><?php include __DIR__ . '/../../includes/sidebar.php'; ?></div>
    <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
        <div class="row justify-content-center"><div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-body p-4 p-md-5 text-center">
            <i class="fas fa-mobile-alt fa-3x text-primary" aria-hidden="true"></i><h1 class="h3 mt-3">Khalti payment verification</h1>
            <?php if ($message): ?><div class="alert alert-<?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?> mt-4" role="status"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div><a class="btn btn-outline-primary" href="index.php">Payment history</a>
            <?php else: ?><p class="text-muted mt-3">Khalti checkout will return here after payment. Transaction status is verified directly with Khalti before booking approval.</p><a class="btn btn-primary" href="../sessions/index.php">Browse sessions</a><?php endif; ?>
        </div></div></div></div>
    </main>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
