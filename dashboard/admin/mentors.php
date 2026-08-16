<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Mentors';
$sidebar_role = 'admin';
$sidebar_active = 'Mentors';

startDashboardPage();
?>
<style>
.mentor-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="mentor-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="mTotal">0</div><div class="stat-label">Total Mentors</div></div><div class="stat-icon"><i class="fas fa-user-tie"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="mApproved">0</div><div class="stat-label">Approved</div></div><div class="stat-icon"><i class="fas fa-circle-check"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="mPending">0</div><div class="stat-label">Pending</div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="mSuspended">0</div><div class="stat-label">Suspended</div></div><div class="stat-icon"><i class="fas fa-ban"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-user-tie" style="color:var(--primary);"></i> Mentors</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Mentor</th><th>Email</th><th>Specialization</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="mentorsTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='active'?'approved':(s==='pending'?'pending':'rejected'); }

SkillShare.apiFetch(BASE + 'api/admin.php?action=list&role=mentor').then(function(res) {
    if (res.success && res.data) {
        var total = res.data.length;
        var approved = res.data.filter(function(u){ return u.status==='active'; }).length;
        var pending = res.data.filter(function(u){ return u.status==='pending'; }).length;
        var suspended = res.data.filter(function(u){ return u.status==='suspended'; }).length;
        document.getElementById('mTotal').textContent = total;
        document.getElementById('mApproved').textContent = approved;
        document.getElementById('mPending').textContent = pending;
        document.getElementById('mSuspended').textContent = suspended;

        var html = '';
        res.data.forEach(function(u) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><div class="user-cell"><img src="../../assets/images/profile/' + (u.profile_picture||'default.png') + '" alt=""><strong>' + SkillShare.escapeHtml(u.full_name) + '</strong></div></td>' +
                '<td>' + SkillShare.escapeHtml(u.email) + '</td>' +
                '<td>' + SkillShare.escapeHtml(u.specialization||'General') + '</td>' +
                '<td><i class="fa-solid fa-star" style="color:var(--warning);"></i> ' + (u.rating ? Number(u.rating).toFixed(1) : '0.0') + '</td>' +
                '<td><span class="status-pill ' + getStatusClass(u.status) + '">' + u.status + '</span></td>' +
                '<td><div class="table-actions"><button class="view-btn" onclick="viewMentor(' + u.id + ')" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" onclick="verifyMentor(' + u.id + ')" aria-label="Verify"><i class="fa-solid fa-check"></i></button><button class="delete-btn" onclick="suspendMentor(' + u.id + ')" aria-label="Suspend"><i class="fa-solid fa-ban"></i></button></div></td></tr>';
        });
        document.getElementById('mentorsTable').innerHTML = html || '<tr><td colspan="7" style="text-align:center;">No mentors found</td></tr>';
    }
});

function viewMentor(id) {
    SkillShare.apiFetch(BASE + 'api/admin.php?action=show&id=' + id).then(function(res) {
        if (res.success) {
            var u = res.data;
            alert('Name: ' + u.full_name + '\nEmail: ' + u.email + '\nSpecialization: ' + (u.specialization||'N/A') + '\nRating: ' + (u.rating||0));
        }
    });
}

function verifyMentor(id) {
    fetch(BASE + 'api/admin.php?action=update_status&id=' + id, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'status=active'})
        .then(function(r){ return r.json(); }).then(function(res) {
            if (res.success) { SkillShare.showToast('Success', 'Mentor verified', 'success'); location.reload(); }
            else SkillShare.showToast('Error', res.message, 'error');
        });
}

function suspendMentor(id) {
    fetch(BASE + 'api/admin.php?action=update_status&id=' + id, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'status=suspended'})
        .then(function(r){ return r.json(); }).then(function(res) {
            if (res.success) { SkillShare.showToast('Success', 'Mentor suspended', 'success'); location.reload(); }
            else SkillShare.showToast('Error', res.message, 'error');
        });
}
</script>
<?php
endDashboardPage();
?>
