<?php
$page_title = 'Payment report';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();
$paymentCounts = $pdo->query('SELECT status, COUNT(*) AS total, COALESCE(SUM(amount), 0) AS amount FROM payments GROUP BY status ORDER BY status')->fetchAll();
$payments = $pdo->query("SELECT p.id, p.amount, p.payment_method, p.status, p.payment_date, p.transaction_id, u.full_name AS fresher_name, c.title AS course_title FROM payments p LEFT JOIN users u ON u.id = p.fresher_id LEFT JOIN courses c ON c.id = p.course_id ORDER BY p.payment_date DESC LIMIT 100")->fetchAll();
$totalAmount = (float)$pdo->query('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = \'completed\'')->fetchColumn();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell"><?php include __DIR__ . '/../includes/sidebar.php'; ?><main class="admin-main">
	<div class="admin-topbar"><div><h1 class="admin-title">Payment report</h1><p class="admin-subtitle">Recorded transactions; status values are not confirmed with a payment gateway.</p></div><a class="btn btn-outline-secondary" href="index.php">All reports</a></div>
	<div class="admin-kpis"><div class="admin-card admin-kpi"><span>Recorded completed total</span><strong><?php echo number_format($totalAmount, 2); ?></strong></div><?php foreach ($paymentCounts as $row): ?><div class="admin-card admin-kpi"><span><?php echo htmlspecialchars(ucfirst($row['status']), ENT_QUOTES, 'UTF-8'); ?> records</span><strong><?php echo (int)$row['total']; ?></strong><small><?php echo number_format((float)$row['amount'], 2); ?></small></div><?php endforeach; ?></div>
	<div class="admin-card admin-table-wrap"><table class="admin-table"><thead><tr><th>Buyer</th><th>Course</th><th>Method</th><th>Reference</th><th>Status</th><th>Amount</th><th>Date</th></tr></thead><tbody><?php foreach ($payments as $payment): ?><tr><td><?php echo htmlspecialchars($payment['fresher_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($payment['course_title'] ?? 'Unavailable', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($payment['payment_method'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($payment['transaction_id'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars(ucfirst($payment['status']), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo number_format((float)$payment['amount'], 2); ?></td><td><?php echo htmlspecialchars(date('M d, Y', strtotime($payment['payment_date'])), ENT_QUOTES, 'UTF-8'); ?></td></tr><?php endforeach; ?><?php if (!$payments): ?><tr><td colspan="7" class="text-center text-muted">No payment records.</td></tr><?php endif; ?></tbody></table></div>
</main></div><?php include __DIR__ . '/../includes/footer.php'; ?>
