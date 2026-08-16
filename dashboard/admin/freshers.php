<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Freshers';
$sidebar_role = 'admin';
$sidebar_active = 'Freshers';

startDashboardPage();
?>
<style>
.fresher-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="fresher-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="fTotal">0</div><div class="stat-label">Total Freshers</div></div><div class="stat-icon"><i class="fas fa-user-graduate"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="fActive">0</div><div class="stat-label">Active</div></div><div class="stat-icon"><i class="fas fa-circle-check"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="fPending">0</div><div class="stat-label">Pending</div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="fSuspended">0</div><div class="stat-label">Suspended</div></div><div class="stat-icon"><i class="fas fa-ban"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-user-graduate" style="color:var(--primary);"></i> All Freshers</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Fresher</th><th>Email</th><th>Field</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody id="freshersTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='active'?'approved':(s==='pending'?'pending':'rejected'); }

SkillShare.apiFetch(BASE + 'api/admin.php?action=list&role=fresher').then(function(res) {
    if (res.success && res.data) {
        var total = res.data.length;
        var active = res.data.filter(function(u){ return u.status==='active'; }).length;
        var pending = res.data.filter(function(u){ return u.status==='pending'; }).length;
        var suspended = res.data.filter(function(u){ return u.status==='suspended'; }).length;
        document.getElementById('fTotal').textContent = total;
        document.getElementById('fActive').textContent = active;
        document.getElementById('fPending').textContent = pending;
        document.getElementById('fSuspended').textContent = suspended;

        var html = '';
        res.data.forEach(function(u) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><div class="user-cell"><img src="../../assets/images/profile/' + (u.profile_picture||'default.png') + '" alt=""><strong>' + SkillShare.escapeHtml(u.full_name) + '</strong></div></td>' +
                '<td>' + SkillShare.escapeHtml(u.email) + '</td>' +
                '<td>' + SkillShare.escapeHtml(u.field_name||'N/A') + '</td>' +
                '<td><span class="status-pill ' + getStatusClass(u.status) + '">' + u.status + '</span></td>' +
                '<td>' + SkillShare.escapeHtml(u.created_at) + '</td>' +
                '<td><div class="table-actions"><button class="view-btn" onclick="viewUser(' + u.id + ')" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="delete-btn" onclick="toggleStatus(' + u.id + ')" aria-label="Toggle status"><i class="fa-solid fa-ban"></i></button></div></td></tr>';
        });
        document.getElementById('freshersTable').innerHTML = html || '<tr><td colspan="7" style="text-align:center;">No freshers found</td></tr>';
    }
});

function viewUser(id) {
    SkillShare.apiFetch(BASE + 'api/admin.php?action=show&id=' + id).then(function(res) {
        if (res.success) alert('Name: ' + res.data.full_name + '\nEmail: ' + res.data.email + '\nField: ' + (res.data.field_name||'N/A'));
    });
}

function toggleStatus(id) {
    var status = prompt('Enter status (active, pending, suspended):');
    if (status && ['active','pending','suspended'].includes(status)) {
        fetch(BASE + 'api/admin.php?action=update_status&id=' + id, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'status=' + encodeURIComponent(status)})
            .then(function(r){ return r.json(); }).then(function(res) {
                if (res.success) { SkillShare.showToast('Success', 'Status updated', 'success'); location.reload(); }
                else SkillShare.showToast('Error', res.message, 'error');
            });
    }
}
</script>
<?php
endDashboardPage();
?>
