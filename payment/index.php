<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/config.php';

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

$stmt = $pdo->prepare("SELECT b.*, u.full_name as mentor_name FROM bookings b JOIN users u ON b.mentor_id = u.id WHERE b.id = ? AND b.user_id = ? AND b.payment_status = 'pending'");
$stmt->execute([$bookingId, $userId]);
$booking = $stmt->fetch();

if (!$booking) {
    header('Location: ' . BASE_URL . 'dashboard/fresher/bookings.php');
    exit();
}

$paymentConfig = $GLOBALS['paymentConfig'] ?? include __DIR__ . '/config.php';
$esewaEnabled = true;
$fonepayEnabled = $paymentConfig['fonepay']['enabled'] ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Payment Method | SkillShare Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-400: #60a5fa;
            --primary-500: #3b82f6;
            --secondary-400: #a78bfa;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.8);
            --text-muted: rgba(255,255,255,0.4);
            --glass-bg: rgba(255,255,255,0.05);
            --glass-border: rgba(255,255,255,0.1);
            --border-hover: rgba(96,165,250,0.35);
            --shadow-lg: 0 8px 40px rgba(0,0,0,0.4);
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-full: 50px;
            --transition-bounce: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --gradient-primary: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --font-heading: 'Poppins', sans-serif;
            --font-primary: 'Inter', sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font-primary); background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1a1a2e 100%); background-attachment: fixed; min-height: 100vh; color: var(--text-primary); overflow-x: hidden; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 30px; margin-bottom: 20px; backdrop-filter: blur(20px); }
        .card h2 { font-family: var(--font-heading); font-weight: 700; font-size: 1.5rem; margin-bottom: 8px; }
        .card p { color: var(--text-secondary); font-size: 0.9rem; }
        .amount-display { font-size: 2.5rem; font-weight: 800; background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 16px 0; }
        .payment-methods { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-top: 20px; }
        .payment-method { background: rgba(255,255,255,0.04); border: 2px solid var(--glass-border); border-radius: var(--radius-lg); padding: 24px; text-align: center; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: block; }
        .payment-method:hover { border-color: var(--primary-500); background: rgba(255,255,255,0.08); transform: translateY(-4px); }
        .payment-method .pm-icon { font-size: 2.5rem; margin-bottom: 12px; }
        .payment-method .pm-name { font-weight: 600; color: var(--text-primary); font-size: 1rem; }
        .payment-method .pm-desc { color: var(--text-muted); font-size: 0.8rem; margin-top: 4px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: var(--radius-full); font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.3s ease; border: none; }
        .btn-primary { background: var(--gradient-primary); color: #fff; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-2px); }
        .btn-outline { background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); }
        .btn-outline:hover { background: rgba(255,255,255,0.06); color: var(--text-primary); }
        .back-link { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; padding: 8px 20px; border-radius: var(--radius-full); background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); font-size: 0.85rem; cursor: pointer; text-decoration: none; transition: all 0.3s ease; }
        .back-link:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); border-color: var(--border-hover); }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?php echo BASE_URL; ?>dashboard/fresher/bookings.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Bookings</a>

        <div class="card">
            <h2>Complete Your Payment</h2>
            <p>Mentor: <?php echo htmlspecialchars($booking['mentor_name']); ?></p>
            <p>Session: <?php echo htmlspecialchars($booking['session_title']); ?></p>
            <p>Date: <?php echo date('M j, Y', strtotime($booking['session_date'])); ?> at <?php echo date('g:i A', strtotime($booking['session_time'])); ?></p>
            <div class="amount-display">$<?php echo number_format($booking['total_amount'], 2); ?></div>
            <p style="color:var(--text-muted);font-size:0.8rem;">Booking #<?php echo htmlspecialchars($booking['booking_number']); ?></p>
        </div>

        <div class="card">
            <h2>Choose Payment Method</h2>
            <p>Select your preferred payment method to complete the booking.</p>
            <div class="payment-methods">
                <?php if ($esewaEnabled): ?>
                <a href="<?php echo BASE_URL; ?>payment/esewa-initiate.php?booking_id=<?php echo (int)$booking['id']; ?>" class="payment-method">
                    <div class="pm-icon"><i class="fas fa-wallet" style="color:#3b82f6;"></i></div>
                    <div class="pm-name">eSewa</div>
                    <div class="pm-desc">Pay securely with eSewa</div>
                </a>
                <?php endif; ?>
                <?php if ($fonepayEnabled): ?>
                <a href="#" class="payment-method" onclick="alert('Fonepay integration requires merchant credentials.'); return false;">
                    <div class="pm-icon"><i class="fas fa-money-bill-wave" style="color:#22c55e;"></i></div>
                    <div class="pm-name">Fonepay</div>
                    <div class="pm-desc">Pay via Fonepay (coming soon)</div>
                </a>
                <?php endif; ?>
                <?php if (!$esewaEnabled && !$fonepayEnabled): ?>
                <p style="color:var(--text-muted);text-align:center;padding:20px;">No payment methods configured. Please contact support.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
