<?php
$page_title = 'Payment Checkout';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
requireFresher();
$paymentConfig = require '../../config/payment-gateways.php';

$userId = (int)getUserId();
$sessionId = isset($_GET['session']) ? (int)$_GET['session'] : 0;
$bookingId = isset($_GET['booking']) ? (int)$_GET['booking'] : 0;
if (!$sessionId && $bookingId) {
    $bookingStmt = $pdo->prepare('SELECT session_id FROM bookings WHERE id = ? AND fresher_id = ?');
    $bookingStmt->execute([$bookingId, $userId]);
    $bookingRow = $bookingStmt->fetch();
    $sessionId = $bookingRow ? (int)$bookingRow['session_id'] : 0;
}
if (!$sessionId) {
    redirect('fresher/sessions/index.php');
}

$sessionStmt = $pdo->prepare("SELECT s.*, u.full_name AS mentor_name, u.avatar AS mentor_avatar FROM sessions s JOIN users u ON s.mentor_id = u.id WHERE s.id = ? AND s.status = 'scheduled'");
$sessionStmt->execute([$sessionId]);
$session = $sessionStmt->fetch();
if (!$session) {
    redirect('fresher/sessions/index.php');
}

$checkoutError = '';
$attempt = null;
$attemptProvider = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $checkoutError = 'Your checkout request expired. Please reload and try again.';
    } elseif (!in_array($_POST['payment_method'] ?? '', ['esewa', 'khalti'], true)) {
        $checkoutError = 'Choose a supported payment provider.';
    } elseif ((float)$session['price'] <= 0) {
        $checkoutError = 'This payment checkout requires a session with a positive price.';
    } else {
        $provider = $_POST['payment_method'];
        $attemptProvider = $provider;
        if (!paymentGatewayReady($provider)) {
            $checkoutError = 'This payment provider is not configured yet. Contact the site administrator.';
        } else {
            try {
                $attempt = createSessionPaymentAttempt($pdo, $userId, $session, $provider);
                if ($provider === 'esewa') {
                    redirect('fresher/payment/esewa.php?payment_id=' . $attempt['payment_id']);
                }

                $base = $paymentConfig['environment'] === 'production' ? 'https://khalti.com/api/v2' : 'https://dev.khalti.com/api/v2';
                $amountPaisa = paymentNprForUsd($session['price']) * 100;
                if ($amountPaisa < 1000) {
                    throw new RuntimeException('The payment amount is below Khalti’s minimum supported amount.');
                }
                $customerStmt = $pdo->prepare('SELECT full_name, email, phone FROM users WHERE id = ? LIMIT 1');
                $customerStmt->execute([$userId]);
                $customer = $customerStmt->fetch() ?: [];
                $customerInfo = [];
                foreach (['name' => 'full_name', 'email' => 'email', 'phone' => 'phone'] as $field => $column) {
                    $value = trim((string)($customer[$column] ?? ''));
                    if ($value !== '') {
                        $customerInfo[$field] = $value;
                    }
                }
                $init = paymentProviderJsonRequest($base . '/epayment/initiate/', $paymentConfig['khalti_secret_key'], [
                    'return_url' => paymentAppUrl('fresher/payment/khalti.php'),
                    'website_url' => paymentAppUrl(''),
                    'amount' => $amountPaisa,
                    'purchase_order_id' => $attempt['order_reference'],
                    'purchase_order_name' => mb_substr($session['title'], 0, 100),
                    'customer_info' => $customerInfo,
                ]);
                $paymentUrl = $init['payment_url'] ?? '';
                $pidx = $init['pidx'] ?? '';
                $paymentUrlParts = is_string($paymentUrl) ? parse_url($paymentUrl) : false;
                $expectedHost = $paymentConfig['environment'] === 'production' ? 'pay.khalti.com' : 'test-pay.khalti.com';
                if (!is_string($pidx) || $pidx === '' || !is_array($paymentUrlParts) || strtolower($paymentUrlParts['host'] ?? '') !== $expectedHost || strtolower($paymentUrlParts['scheme'] ?? '') !== 'https') {
                    throw new RuntimeException('Khalti returned an invalid checkout response.');
                }
                $savePidx = $pdo->prepare("UPDATE payments SET transaction_id = ? WHERE id = ? AND status = 'pending' AND transaction_id = ?");
                $savePidx->execute([$pidx, $attempt['payment_id'], $attempt['order_reference']]);
                if ($savePidx->rowCount() !== 1) {
                    throw new RuntimeException('Could not store the Khalti payment reference.');
                }
                header('Location: ' . $paymentUrl, true, 303);
                exit;
            } catch (Throwable $error) {
                if ($attempt && $attemptProvider === 'khalti') {
                    try {
                        finalizeSessionPayment($pdo, $attempt['payment_id'], $attempt['booking_id'], 'khalti', $attempt['order_reference'], false);
                    } catch (Throwable $finalizeError) {
                        error_log('Failed Khalti initiation cleanup failed: ' . $finalizeError->getMessage());
                    }
                }
                error_log('Payment initiation failed: ' . $error->getMessage());
                $checkoutError = $error instanceof RuntimeException ? $error->getMessage() : 'We could not start the payment. Please try again.';
            }
        }
    }
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Payment Checkout</h1>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">Session Details</h5>
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-calendar-alt fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($session['title']); ?></h6>
                                    <small class="text-muted">with <?php echo htmlspecialchars($session['mentor_name']); ?></small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Date:</strong> <?php echo formatDateTime($session['scheduled_at']); ?>
                            </div>
                            <div class="mb-3">
                                <strong>Duration:</strong> <?php echo $session['duration']; ?> minutes
                            </div>
                        </div>
                    </div>
                    
                     <div class="card border-0 shadow-sm">
                         <div class="card-body">
                             <h5 class="mb-3">Payment Method</h5>
                             <?php if ($checkoutError): ?><div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($checkoutError, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                             <form method="POST">
                                 <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                 <div class="row g-3">
                                     <div class="col-md-4">
                                         <div class="form-check p-3 border rounded">
                                             <input class="form-check-input" type="radio" name="payment_method" id="esewa" value="esewa" <?php echo paymentGatewayReady('esewa') ? '' : 'disabled'; ?> required>
                                             <label class="form-check-label w-100" for="esewa">
                                                 <div class="text-center">
                                                     <i class="fas fa-wallet fa-2x text-success mb-2"></i>
                                                     <div class="fw-bold">eSewa</div>
                                                     <small class="text-muted"><?php echo paymentGatewayReady('esewa') ? 'Verified eSewa checkout' : 'Not configured'; ?></small>
                                                 </div>
                                             </label>
                                         </div>
                                     </div>
                                     <div class="col-md-4">
                                         <div class="form-check p-3 border rounded">
                                             <input class="form-check-input" type="radio" name="payment_method" id="khalti" value="khalti" <?php echo paymentGatewayReady('khalti') ? '' : 'disabled'; ?>>
                                             <label class="form-check-label w-100" for="khalti">
                                                 <div class="text-center">
                                                     <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                                     <div class="fw-bold">Khalti</div>
                                                     <small class="text-muted"><?php echo paymentGatewayReady('khalti') ? 'Verified Khalti checkout' : 'Not configured'; ?></small>
                                                 </div>
                                             </label>
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <div class="d-flex justify-content-between align-items-center mt-4">
                                     <div>
                                         <strong>Total Amount:</strong>
                                         <h3 class="text-primary mb-0">NPR <?php echo number_format(paymentNprForUsd($session['price']), 0); ?></h3>
                                         <small class="text-muted">Approx. $<?php echo number_format($session['price'], 2); ?> USD</small>
                                     </div>
                                     <button type="submit" class="btn btn-primary btn-lg" <?php echo !paymentGatewayReady('esewa') && !paymentGatewayReady('khalti') ? 'disabled' : ''; ?>>
                                         <i class="fas fa-lock me-2"></i> Pay Now
                                     </button>
                                 </div>
                             </form>
                         </div>
                     </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="mb-3">Order Summary</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Session Fee</span>
                                <span>$<?php echo number_format($session['price'], 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Platform Fee</span>
                                <span>$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong class="text-primary">$<?php echo number_format($session['price'], 2); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
