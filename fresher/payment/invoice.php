<?php
$page_title = 'Payment Invoice';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$payment_id) {
    redirect('fresher/payment/index.php');
}

$stmt = $pdo->prepare("SELECT p.*, c.title as course_title, c.duration, u.full_name as mentor_name 
                       FROM payments p 
                       JOIN courses c ON p.course_id = c.id 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE p.id = ? AND p.fresher_id = ?");
$stmt->execute([$payment_id, $user_id]);
$payment = $stmt->fetch();

if (!$payment) {
    redirect('fresher/payment/index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $payment['id']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f7fa; padding: 40px 20px; }
        .invoice-container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .invoice-title { font-size: 2rem; font-weight: 700; color: #1E3A8A; }
        .invoice-meta { text-align: right; }
        .invoice-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .invoice-table th, .invoice-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .invoice-table th { background: #f9fafb; font-weight: 600; }
        .invoice-total { text-align: right; font-size: 1.25rem; font-weight: 700; color: #1E3A8A; margin-top: 20px; }
        .invoice-footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; color: #6b7280; }
        .btn-print { margin-top: 20px; }
        @media print { body { padding: 0; } .invoice-container { box-shadow: none; } .btn-print { display: none; } }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div>
                <h1 class="invoice-title">INVOICE</h1>
                <p class="text-muted">SkillShare Hub</p>
            </div>
            <div class="invoice-meta">
                <p><strong>Invoice #:</strong> <?php echo $payment['id']; ?></p>
                <p><strong>Date:</strong> <?php echo formatDateTime($payment['payment_date']); ?></p>
                <p><strong>Status:</strong> <span class="badge bg-success"><?php echo ucfirst($payment['status']); ?></span></p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h6>Billed To</h6>
                <p><strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong></p>
                <p class="text-muted"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
            <div class="col-md-6">
                <h6>Course Details</h6>
                <p><strong><?php echo htmlspecialchars($payment['course_title']); ?></strong></p>
                <p class="text-muted">Instructor: <?php echo htmlspecialchars($payment['mentor_name']); ?></p>
            </div>
        </div>
        
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Course: <?php echo htmlspecialchars($payment['course_title']); ?></td>
                    <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>
        
        <div class="invoice-total">
            Total: $<?php echo number_format($payment['amount'], 2); ?>
        </div>
        
        <div class="invoice-footer">
            <p>Thank you for your payment!</p>
            <p class="small">Payment Method: <?php echo ucfirst($payment['payment_method']); ?> | Transaction ID: <?php echo $payment['id']; ?></p>
        </div>
        
        <div class="text-center btn-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i> Print Invoice
            </button>
        </div>
    </div>
</body>
</html>
