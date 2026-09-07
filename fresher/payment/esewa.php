<?php
$page_title = 'eSewa Payment';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$session_id = isset($_GET['session']) ? (int)$_GET['session'] : 0;
$amount = isset($_GET['amount']) ? (float)$_GET['amount'] : 0;

if (!$session_id || !$amount) {
    redirect('fresher/payment/index.php');
}

$stmt = $pdo->prepare("SELECT s.*, u.full_name as mentor_name FROM sessions s JOIN users u ON s.mentor_id = u.id WHERE s.id = ?");
$stmt->execute([$session_id]);
$session = $stmt->fetch();

if (!$session) {
    redirect('fresher/payment/index.php');
}

$transaction_id = 'ES-' . time() . '-' . $user_id;
$amount_npr = $amount * 130;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_id = sanitize($_POST['transaction_id'] ?? $transaction_id);
    $payment_status = 'completed';
    
    $stmt = $pdo->prepare("INSERT INTO payments (fresher_id, course_id, amount, payment_method, status, transaction_id, payment_date) 
                           VALUES (?, ?, ?, 'esewa', ?, ?, NOW())");
    $stmt->execute([$user_id, $session['course_id'] ?? 0, $amount, $payment_status, $transaction_id]);
    
    $payment_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, payment_method, payment_status, transaction_id, payment_date, status) 
                           VALUES (?, ?, 'esewa', 'paid', ?, NOW(), 'approved')");
    $stmt->execute([$user_id, $session_id, $transaction_id]);
    
    redirect('sucess.php?id=' . $payment_id);
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-wallet fa-4x text-success"></i>
                                <h3 class="mt-3">eSewa Payment</h3>
                                <p class="text-muted">Complete your payment using eSewa</p>
                            </div>
                            
                            <div class="alert alert-info">
                                <h5>Amount to Pay: NPR <?php echo number_format($amount_npr, 0); ?></h5>
                                <p class="mb-0">Approx. $<?php echo number_format($amount, 2); ?> USD</p>
                            </div>
                            
                            <div class="mb-4">
                                <h6>Payment Instructions:</h6>
                                <ol class="text-start">
                                    <li>Open your eSewa mobile app or visit <a href="https://esewa.com.np" target="_blank">esewa.com.np</a></li>
                                    <li>Send money to: <strong>SkillShare Hub</strong></li>
                                    <li>Amount: <strong>NPR <?php echo number_format($amount_npr, 0); ?></strong></li>
                                    <li>Note/Remark: <strong><?php echo $transaction_id; ?></strong></li>
                                    <li>Enter the transaction ID below after payment</li>
                                </ol>
                            </div>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">eSewa Transaction ID</label>
                                    <input type="text" name="transaction_id" class="form-control" value="<?php echo $transaction_id; ?>" required>
                                    <small class="text-muted">Enter the transaction ID from your eSewa payment</small>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check"></i> Confirm Payment
                                    </button>
                                    <a href="checkout.php?session=<?php echo $session_id; ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Checkout
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
