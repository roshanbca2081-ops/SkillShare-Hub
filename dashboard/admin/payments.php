<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Payments';
$sidebar_role = 'admin';
$sidebar_active = 'Payments';

startDashboardPage();
?>
<style>
.payment-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="payment-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="pRevenue">$0</div><div class="stat-label">Revenue</div></div><div class="stat-icon"><i class="fas fa-circle-dollar"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="pTrans">0</div><div class="stat-label">Transactions</div></div><div class="stat-icon"><i class="fas fa-receipt"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="pSuccess">0</div><div class="stat-label">Successful</div></div><div class="stat-icon"><i class="fas fa-circle-check"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="pPending">0</div><div class="stat-label">Pending</div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-credit-card" style="color:var(--primary);"></i> Transactions</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Transaction ID</th><th>Student</th><th>Course</th><th>Amount</th><th>Method</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="paymentsTable"><tr><td colspan="8" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='completed'?'approved':(s==='pending'?'pending':'rejected'); }

SkillShare.apiFetch(BASE + 'api/dashboard/index.php').then(function(res) {
    if (res.success && res.data) {
        var d = res.data;
        document.getElementById('pRevenue').textContent = '$' + (d.revenue ? Number(d.revenue).toLocaleString() : '0');
        document.getElementById('pTrans').textContent = d.payments || 0;
    }
});

SkillShare.apiFetch(BASE + 'api/payments.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('pSuccess').textContent = data.filter(function(p){ return p.status==='completed'; }).length;
        document.getElementById('pPending').textContent = data.filter(function(p){ return p.status==='pending'; }).length;

        var html = '';
        data.forEach(function(p) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><strong>' + SkillShare.escapeHtml(p.transaction_id||p.id) + '</strong></td>' +
                '<td><div class="user-cell"><img src="../../assets/images/profile/default.png" alt=""><strong>' + SkillShare.escapeHtml(p.user_name||p.student_name||'Student') + '</strong></div></td>' +
                '<td>' + SkillShare.escapeHtml(p.course_name||'Course') + '</td>' +
                '<td><strong>$' + Number(p.amount||0).toFixed(2) + '</strong></td>' +
                '<td><span class="badge badge-primary">' + SkillShare.escapeHtml(p.method||'Card') + '</span></td>' +
                '<td><span class="status-pill ' + getStatusClass(p.status) + '">' + p.status + '</span></td>' +
                '<td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Invoice"><i class="fa-solid fa-file-invoice"></i></button></div></td></tr>';
        });
        document.getElementById('paymentsTable').innerHTML = html || '<tr><td colspan="8" style="text-align:center;">No transactions found</td></tr>';
    }
});
</script>
<?php
endDashboardPage();
?>
