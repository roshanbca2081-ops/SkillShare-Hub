<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Payments';
$pdo = getDB();
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT p.*, u.full_name as user_name FROM payments p JOIN users u ON p.user_id = u.id WHERE 1=1";
$params = [];

if ($statusFilter) {
    $sql .= " AND p.status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY p.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();

$totalPayments = (int)$pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();
$pendingPayments = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'pending'")->fetchColumn();
$paidPayments = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'paid'")->fetchColumn();
$failedPayments = (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'failed'")->fetchColumn();
$totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Payment Management</h3>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-credit-card"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value">$<?php echo number_format($totalRevenue, 2); ?></div>
                <div class="admin-stat-label">Total Revenue</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;"><i class="fas fa-receipt"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalPayments); ?></div>
                <div class="admin-stat-label">Total Payments</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-clock"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($pendingPayments); ?></div>
                <div class="admin-stat-label">Pending</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($paidPayments); ?></div>
                <div class="admin-stat-label">Paid</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-exclamation-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($failedPayments); ?></div>
                <div class="admin-stat-label">Failed</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <select name="status" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="paid" <?php echo $statusFilter === 'paid' ? 'selected' : ''; ?>>Paid</option>
                    <option value="failed" <?php echo $statusFilter === 'failed' ? 'selected' : ''; ?>>Failed</option>
                    <option value="refunded" <?php echo $statusFilter === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                </select>
                <?php if ($statusFilter): ?>
                <a href="<?php echo ADMIN_URL; ?>payments.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Transaction Ref</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                    <tr><td colspan="8" class="admin-empty-state"><i class="fas fa-credit-card"></i><p>No payments found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td>#<?php echo $payment['id']; ?></td>
                            <td><?php echo htmlspecialchars($payment['user_name']); ?></td>
                            <td><strong><?php echo AdminPanel.formatCurrency($payment['amount']); ?></strong></td>
                            <td><?php echo htmlspecialchars($payment['method'] ?: 'N/A'); ?></td>
                            <td><code><?php echo htmlspecialchars($payment['transaction_ref'] ?: 'N/A'); ?></code></td>
                            <td><span class="admin-badge <?php echo admin_status_badge($payment['status']); ?>"><?php echo htmlspecialchars($payment['status']); ?></span></td>
                            <td><?php echo admin_format_datetime($payment['created_at']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <?php if ($payment['status'] === 'pending'): ?>
                                    <button class="admin-action-btn approve" title="Mark Paid" onclick="updatePaymentStatus(<?php echo $payment['id']; ?>, 'paid')"><i class="fas fa-check"></i></button>
                                    <button class="admin-action-btn reject" title="Mark Failed" onclick="updatePaymentStatus(<?php echo $payment['id']; ?>, 'failed')"><i class="fas fa-times"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function updatePaymentStatus(id, status) {
    if (!AdminPanel.confirm('Update payment to ' + status + '?')) return;
    AdminUtils.apiFetch(AdminPanel.baseUrl + 'actions/payment-actions.php?action=update_status&id=' + id, {
        method: 'POST',
        body: {status: status}
    }).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', 'Payment updated', 'success');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

