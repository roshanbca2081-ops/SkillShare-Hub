<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Payments';
$sidebar_role = 'fresher';
$sidebar_active = 'Payments';

startDashboardPage();
?>

<style>
.payment-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
.payment-stat {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 16px 20px;
}
.payment-stat-value { font-size: 1.4rem; font-weight: 700; color: var(--text-primary); }
.payment-stat-label { font-size: 0.75rem; color: var(--text-muted); }
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.table-responsive-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
.data-table th { color: var(--text-muted); font-weight: 500; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.status-badge {
    padding: 4px 12px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
}
.status-completed { background: rgba(34,197,94,0.15); color: var(--success); }
.status-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
.status-failed { background: rgba(239,68,68,0.15); color: var(--danger); }
.table-actions { display: flex; gap: 6px; }
.icon-btn {
    width: 32px; height: 32px; border-radius: var(--radius-md); border: 1px solid var(--glass-border);
    background: transparent; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all 0.3s ease; font-size: 0.8rem;
}
.icon-btn:hover { background: rgba(255,255,255,0.08); color: var(--text-primary); }
.empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
</style>

<div class="payment-stats" id="paymentStats">
    <div class="payment-stat"><div class="payment-stat-value" id="statTotal">$0</div><div class="payment-stat-label">Total Spent</div></div>
    <div class="payment-stat"><div class="payment-stat-value" id="statPending">$0</div><div class="payment-stat-label">Pending</div></div>
    <div class="payment-stat"><div class="payment-stat-value" id="statCompleted">$0</div><div class="payment-stat-label">Completed</div></div>
</div>

<div class="toolbar">
    <h3 style="margin:0;"><i class="fas fa-credit-card" style="color:var(--primary-400);margin-right:8px;"></i> Payment History</h3>
    <select class="filter-select" id="paymentFilter"><option value="">All</option><option value="completed">Completed</option><option value="pending">Pending</option><option value="failed">Failed</option></select>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-responsive-wrap">
        <table class="data-table">
            <thead><tr><th>Invoice</th><th>Description</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="paymentsTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderPayments(payments) {
    var tbody = document.getElementById('paymentsTable');
    if (!payments.length) { tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;"><i class="fas fa-receipt" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:8px;"></i>No payments found</td></tr>'; return; }
    var html = '';
    var total = 0, pending = 0, completed = 0;
    payments.forEach(function(p) {
        var amt = parseFloat(p.total_amount || p.amount || 0);
        total += amt;
        if (p.status === 'completed') completed += amt;
        if (p.status === 'pending') pending += amt;
        html += '<tr>' +
            '<td><strong>' + SkillShare.escapeHtml(p.invoice_number || '#' + p.id) + '</strong></td>' +
            '<td>' + SkillShare.escapeHtml(p.session_title || 'Payment') + '</td>' +
            '<td style="color:var(--primary-400);font-weight:600;">' + SkillShare.formatCurrency(amt) + '</td>' +
            '<td>' + SkillShare.escapeHtml(p.payment_method || '-') + '</td>' +
            '<td>' + SkillShare.formatDate(p.payment_date || p.created_at) + '</td>' +
            '<td><span class="status-badge status-' + p.status + '">' + p.status + '</span></td>' +
            '<td><div class="table-actions"><button class="icon-btn" title="Download" onclick="SkillShare.showToast(\'Receipt\', \'Invoice ' + SkillShare.escapeHtml(p.invoice_number || '') + ' downloaded\', \'success\')"><i class="fas fa-file-invoice"></i></button></div></td></tr>';
    });
    tbody.innerHTML = html;
    document.getElementById('statTotal').textContent = SkillShare.formatCurrency(total);
    document.getElementById('statPending').textContent = SkillShare.formatCurrency(pending);
    document.getElementById('statCompleted').textContent = SkillShare.formatCurrency(completed);
}

document.getElementById('paymentFilter').addEventListener('change', function() {
    var params = '?action=list' + (this.value ? '&status=' + this.value : '');
    SkillShare.apiFetch(BASE + 'api/payments.php' + params).then(function(res) {
        if (res.success) renderPayments(res.data);
    });
});

SkillShare.apiFetch(BASE + 'api/payments.php?action=list').then(function(res) {
    if (res.success) renderPayments(res.data);
});
</script>

<?php
endDashboardPage();
