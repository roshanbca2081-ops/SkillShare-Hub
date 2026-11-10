<?php
$page_title = 'eSewa Payment';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
$paymentConfig = require_once __DIR__ . '/../../config/payment-gateways.php';
requireFresher();
$userId = (int)getUserId();
$message = '';
$messageType = 'danger';
$checkout = null;
$gatewayForm = [];

if (isset($_GET['data'])) {
    try {
        $encoded = (string)$_GET['data'];
        $decoded = base64_decode($encoded, true);
        $response = is_string($decoded) ? json_decode($decoded, true) : null;
        if (!is_array($response)) {
            throw new RuntimeException('Invalid payment response.');
        }
        $statusCheck = verifyEsewaResponse($response);
        $order = parsePaymentOrderReference($response['transaction_uuid'] ?? null);
        if (!$order || ($response['status'] ?? '') !== 'COMPLETE' || ($response['product_code'] ?? '') !== $paymentConfig['esewa_product_code']) {
            throw new RuntimeException('Payment was not confirmed.');
        }
        $stmt = $pdo->prepare('SELECT p.id, p.fresher_id, p.amount, p.status, p.payment_method, p.transaction_id, s.id AS session_id, s.price AS session_price FROM payments p JOIN bookings b ON b.id = ? AND b.fresher_id = p.fresher_id JOIN sessions s ON s.id = b.session_id WHERE p.id = ? AND JSON_UNQUOTE(JSON_EXTRACT(p.metadata, \'$.booking_id\')) = ? AND p.transaction_id = ? AND p.payment_method = \'esewa\' LIMIT 1');
        $stmt->execute([$order['booking_id'], $order['payment_id'], (string)$order['booking_id'], $response['transaction_uuid']]);
        $payment = $stmt->fetch();
        $expectedNpr = $payment ? paymentNprForUsd($payment['session_price']) : 0;
        $reportedAmount = isset($response['total_amount']) ? (float)str_replace(',', '', (string)$response['total_amount']) : -1;
        if (!$payment || (int)$payment['fresher_id'] !== $userId || (float)$payment['amount'] !== (float)$payment['session_price'] || abs($reportedAmount - $expectedNpr) > 0.001) {
            throw new RuntimeException('The payment details did not match this order.');
        }
        if (($statusCheck['status'] ?? '') !== 'COMPLETE'
            || ($statusCheck['transaction_uuid'] ?? '') !== $response['transaction_uuid']
            || ($statusCheck['product_code'] ?? '') !== $paymentConfig['esewa_product_code']
            || abs((float)($statusCheck['total_amount'] ?? -1) - $expectedNpr) > 0.001) {
            throw new RuntimeException('eSewa has not confirmed this payment.');
        }
        if (!finalizeSessionPayment($pdo, $order['payment_id'], $order['booking_id'], 'esewa', $response['transaction_uuid'], true)) {
            throw new RuntimeException('The payment is no longer available for confirmation.');
        }
        redirect('fresher/payment/sucess.php?session_id=' . (int)$payment['session_id']);
    } catch (Throwable $error) {
        error_log('eSewa callback verification failed: ' . $error->getMessage());
        $message = 'We could not verify the eSewa payment. It has not been marked paid. If your wallet was charged, contact support with your eSewa reference.';
    }
}

if (!$message && isset($_GET['failed'])) {
    try {
        $failedPaymentId = (int)($_GET['payment_id'] ?? 0);
        $failedStmt = $pdo->prepare("SELECT p.id, p.fresher_id, p.amount, p.status, p.transaction_id, p.metadata, s.id AS session_id, s.price FROM payments p JOIN bookings b ON b.fresher_id = p.fresher_id JOIN sessions s ON s.id = b.session_id WHERE p.id = ? AND p.fresher_id = ? AND p.payment_method = 'esewa' AND JSON_UNQUOTE(JSON_EXTRACT(p.metadata, '$.booking_id')) = b.id LIMIT 1");
        $failedStmt->execute([$failedPaymentId, $userId]);
        $failedPayment = $failedStmt->fetch();
        $metadata = $failedPayment ? json_decode((string)$failedPayment['metadata'], true) : null;
        $order = $failedPayment ? parsePaymentOrderReference($failedPayment['transaction_id']) : null;
        if (!$failedPayment || !$order || $order['payment_id'] !== $failedPaymentId || ($metadata['booking_id'] ?? 0) !== $order['booking_id']) {
            throw new RuntimeException('Invalid eSewa return order.');
        }
        $expectedAmount = number_format(paymentNprForUsd($failedPayment['price']), 2, '.', '');
        $statusCheck = lookupEsewaTransaction($expectedAmount, $failedPayment['transaction_id']);
        if (($statusCheck['transaction_uuid'] ?? '') !== $failedPayment['transaction_id']
            || ($statusCheck['product_code'] ?? '') !== $paymentConfig['esewa_product_code']
            || abs((float)($statusCheck['total_amount'] ?? -1) - (float)$expectedAmount) > 0.001) {
            throw new RuntimeException('eSewa return did not match the order.');
        }
        if (($statusCheck['status'] ?? '') === 'COMPLETE' && (float)$failedPayment['amount'] === (float)$failedPayment['price']) {
            if (!finalizeSessionPayment($pdo, $failedPaymentId, $order['booking_id'], 'esewa', $failedPayment['transaction_id'], true)) {
                throw new RuntimeException('Payment could not be completed for this booking.');
            }
            redirect('fresher/payment/sucess.php?session_id=' . (int)$failedPayment['session_id']);
        }
        if (in_array($statusCheck['status'] ?? '', ['CANCELED', 'NOT_FOUND'], true)) {
            finalizeSessionPayment($pdo, $failedPaymentId, $order['booking_id'], 'esewa', $failedPayment['transaction_id'], false);
            $message = 'eSewa did not complete this payment. You can try checkout again.';
            $messageType = 'warning';
        } else {
            $message = 'eSewa has not confirmed a final payment status yet. Your payment remains pending; contact support if your wallet was charged.';
            $messageType = 'warning';
        }
    } catch (Throwable $error) {
        error_log('eSewa failure return verification failed: ' . $error->getMessage());
        $message = 'We could not confirm the eSewa payment status. It has not been marked paid.';
    }
}

$paymentId = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : 0;
if (!$message && $paymentId > 0) {
    $stmt = $pdo->prepare("SELECT p.id, p.amount, p.status, p.transaction_id, s.id AS session_id, s.title, s.price, s.course_id FROM payments p JOIN bookings b ON b.id = ? AND b.fresher_id = p.fresher_id JOIN sessions s ON s.id = b.session_id WHERE p.id = ? AND JSON_UNQUOTE(JSON_EXTRACT(p.metadata, '$.booking_id')) = ? AND p.fresher_id = ? AND p.payment_method = 'esewa' LIMIT 1");
    $order = null;
    $candidate = $pdo->prepare("SELECT p.transaction_id FROM payments p WHERE p.id = ? AND p.fresher_id = ? AND p.payment_method = 'esewa' LIMIT 1");
    $candidate->execute([$paymentId, $userId]);
    $order = parsePaymentOrderReference($candidate->fetchColumn());
    if ($order) {
        $stmt->execute([$order['booking_id'], $paymentId, (string)$order['booking_id'], $userId]);
        $checkout = $stmt->fetch();
    }
    if (!$checkout || $checkout['status'] !== 'pending' || !paymentGatewayReady('esewa')) {
        $message = 'This eSewa checkout is unavailable. Check gateway configuration or start a new checkout.';
    } else {
        $amountNpr = paymentNprForUsd($checkout['price']);
        $totalAmount = number_format($amountNpr, 2, '.', '');
        $signedFieldNames = 'total_amount,transaction_uuid,product_code';
        $signatureMessage = 'total_amount=' . $totalAmount . ',transaction_uuid=' . $checkout['transaction_id'] . ',product_code=' . $paymentConfig['esewa_product_code'];
        $signature = base64_encode(hash_hmac('sha256', $signatureMessage, $paymentConfig['esewa_secret_key'], true));
        $gatewayForm = [
            'amount' => $totalAmount,
            'tax_amount' => '0',
            'total_amount' => $totalAmount,
            'transaction_uuid' => $checkout['transaction_id'],
            'product_code' => $paymentConfig['esewa_product_code'],
            'product_service_charge' => '0',
            'product_delivery_charge' => '0',
            'success_url' => paymentAppUrl('fresher/payment/esewa.php'),
            'failure_url' => paymentAppUrl('fresher/payment/esewa.php?failed=1&payment_id=' . (int)$checkout['id']),
            'signed_field_names' => $signedFieldNames,
            'signature' => $signature,
        ];
    }
}
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>
<div class="container-fluid"><div class="row">
    <div class="col-md-3 col-lg-2 d-md-block sidebar p-0"><?php include __DIR__ . '/../../includes/sidebar.php'; ?></div>
    <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
        <div class="row justify-content-center"><div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-body p-4 p-md-5 text-center">
            <i class="fas fa-wallet fa-3x text-success" aria-hidden="true"></i><h1 class="h3 mt-3">Pay securely with eSewa</h1>
            <?php if ($message): ?><div class="alert alert-<?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?> mt-4" role="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div><a class="btn btn-outline-primary" href="index.php">Payment history</a>
            <?php elseif ($checkout && $gatewayForm): ?>
                <p class="text-muted">You will complete payment on the official eSewa checkout page. We verify the transaction with eSewa before approving your booking.</p>
                <div class="alert alert-info"><strong>Session:</strong> <?php echo htmlspecialchars($checkout['title'], ENT_QUOTES, 'UTF-8'); ?><br><strong>Amount:</strong> NPR <?php echo number_format(paymentNprForUsd($checkout['price']), 0); ?></div>
                <form method="POST" action="<?php echo $paymentConfig['environment'] === 'production' ? 'https://epay.esewa.com.np/api/epay/main/v2/form' : 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'; ?>">
                    <?php foreach ($gatewayForm as $name => $value): ?><input type="hidden" name="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"><?php endforeach; ?>
                    <button class="btn btn-success btn-lg" type="submit"><i class="fas fa-lock me-2"></i>Continue to eSewa</button>
                </form>
            <?php else: ?>
                <p class="text-muted mt-3">Select eSewa from session checkout to begin a verified payment.</p><a class="btn btn-primary" href="../sessions/index.php">Browse sessions</a>
            <?php endif; ?>
        </div></div></div></div>
    </main>
</div></div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
