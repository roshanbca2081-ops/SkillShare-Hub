<?php
$page_title = 'Payment Checkout';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$session_id = isset($_GET['session']) ? (int)$_GET['session'] : 0;

if (!$session_id) {
    redirect('fresher/sessions/index.php');
}

$stmt = $pdo->prepare("SELECT s.*, u.full_name as mentor_name, u.avatar as mentor_avatar 
                       FROM sessions s 
                       JOIN users u ON s.mentor_id = u.id 
                       WHERE s.id = ? AND s.status = 'scheduled'");
$stmt->execute([$session_id]);
$session = $stmt->fetch();

if (!$session) {
    redirect('fresher/sessions/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = sanitize($_POST['payment_method'] ?? 'card');
    $amount = $session['price'];
    
    $stmt = $pdo->prepare("INSERT INTO payments (fresher_id, course_id, amount, payment_method, status, payment_date) 
                           VALUES (?, ?, ?, ?, 'completed', NOW())");
    $stmt->execute([$user_id, $session['course_id'] ?? 0, $amount, $payment_method]);
    
    $booking_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, status, booking_date) 
                           VALUES (?, ?, 'approved', NOW())");
    $stmt->execute([$user_id, $session_id]);
    
    redirect('fresher/payment/sucess.php?id=' . $booking_id);
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
                            <form method="POST">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="card" value="card" checked>
                                        <label class="form-check-label" for="card">
                                            <i class="fas fa-credit-card me-2"></i> Credit/Debit Card
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                        <label class="form-check-label" for="paypal">
                                            <i class="fab fa-paypal me-2"></i> PayPal
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="bank" value="bank">
                                        <label class="form-check-label" for="bank">
                                            <i class="fas fa-university me-2"></i> Bank Transfer
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div>
                                        <strong>Total Amount:</strong>
                                        <h3 class="text-primary mb-0">$<?php echo number_format($session['price'], 2); ?></h3>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg">
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
