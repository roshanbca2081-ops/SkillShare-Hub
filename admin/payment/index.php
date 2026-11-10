<?php
$page_title = 'Payments';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

$status = $_GET['status'] ?? '';
$allowedStatuses = ['pending', 'completed', 'failed'];
if (!in_array($status, $allowedStatuses, true)) {
	$status = '';
}
$where = $status ? ' WHERE p.status = ?' : '';
$params = $status ? [$status] : [];
$stmt = $pdo->prepare("SELECT p.id, p.amount, p.payment_method, p.status, p.payment_date, p.transaction_id, u.full_name AS fresher_name, u.email, c.title AS course_title FROM payments p LEFT JOIN users u ON u.id = p.fresher_id LEFT JOIN courses c ON c.id = p.course_id$where ORDER BY p.payment_date DESC LIMIT 200");
$stmt->execute($params);
$payments = $stmt->fetchAll();
$counts = $pdo->query('SELECT status, COUNT(*) AS total FROM payments GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
$recordedTotal = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'")->fetchColumn();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell"><?php include __DIR__ . '/../includes/sidebar.php'; ?><main class="admin-main">
	<div class="admin-topbar"><div><h1 class="admin-title">Payments</h1><p class="admin-subtitle">Review recorded transactions. This page does not verify payment-provider receipts.</p></div></div>
	<div class="admin-kpis"><div class="admin-card admin-kpi"><span>Pending</span><strong><?php echo (int)($counts['pending'] ?? 0); ?></strong></div><div class="admin-card admin-kpi"><span>Completed records</span><strong><?php echo (int)($counts['completed'] ?? 0); ?></strong></div><div class="admin-card admin-kpi"><span>Failed</span><strong><?php echo (int)($counts['failed'] ?? 0); ?></strong></div><div class="admin-card admin-kpi"><span>Recorded completed total</span><strong><?php echo number_format($recordedTotal, 2); ?></strong></div></div>
	<form method="GET" class="admin-card d-flex align-items-end gap-2 mb-3"><label class="form-label mb-0">Status<select class="form-select" name="status"><option value="">All statuses</option><?php foreach ($allowedStatuses as $option): ?><option value="<?php echo $option; ?>" <?php echo $status === $option ? 'selected' : ''; ?>><?php echo ucfirst($option); ?></option><?php endforeach; ?></select></label><button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?php echo appUrl('admin/payment/index.php'); ?>">Reset</a></form>
	<div class="admin-card admin-table-wrap"><table class="admin-table"><thead><tr><th>Buyer</th><th>Course</th><th>Method</th><th>Reference</th><th>Status</th><th>Amount</th><th>Date</th></tr></thead><tbody><?php foreach ($payments as $payment): ?><tr><td><strong><?php echo htmlspecialchars($payment['fresher_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></strong><br><small><?php echo htmlspecialchars($payment['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small></td><td><?php echo htmlspecialchars($payment['course_title'] ?? 'Unavailable', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($payment['payment_method'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($payment['transaction_id'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td><td><span class="badge bg-<?php echo $payment['status'] === 'completed' ? 'success' : ($payment['status'] === 'failed' ? 'danger' : 'warning'); ?>"><?php echo htmlspecialchars(ucfirst($payment['status']), ENT_QUOTES, 'UTF-8'); ?></span></td><td><?php echo number_format((float)$payment['amount'], 2); ?></td><td><?php echo htmlspecialchars(date('M d, Y h:i A', strtotime($payment['payment_date'])), ENT_QUOTES, 'UTF-8'); ?></td></tr><?php endforeach; ?><?php if (!$payments): ?><tr><td colspan="7" class="text-center text-muted py-4">No payment records found.</td></tr><?php endif; ?></tbody></table></div>
</main></div><?php include __DIR__ . '/../includes/footer.php'; ?>
