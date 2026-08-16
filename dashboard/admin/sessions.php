<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Sessions';
$sidebar_role = 'admin';
$sidebar_active = 'Sessions';

startDashboardPage();
?>
<style>
.session-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="session-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="sTotal">0</div><div class="stat-label">Total Sessions</div></div><div class="stat-icon"><i class="fas fa-video"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="sScheduled">0</div><div class="stat-label">Scheduled</div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="sCompleted">0</div><div class="stat-label">Completed</div></div><div class="stat-icon"><i class="fas fa-circle-check"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="sCancelled">0</div><div class="stat-label">Cancelled</div></div><div class="stat-icon"><i class="fas fa-ban"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-video" style="color:var(--primary);"></i> All Sessions</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Session</th><th>Mentor</th><th>Date</th><th>Time</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="sessionsTable"><tr><td colspan="8" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='completed'?'approved':(s==='scheduled'||s==='ongoing'?'pending':'rejected'); }

SkillShare.apiFetch(BASE + 'api/sessions.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('sTotal').textContent = data.length;
        document.getElementById('sScheduled').textContent = data.filter(function(s){ return s.status==='scheduled'; }).length;
        document.getElementById('sCompleted').textContent = data.filter(function(s){ return s.status==='completed'; }).length;
        document.getElementById('sCancelled').textContent = data.filter(function(s){ return s.status==='cancelled'; }).length;

        var html = '';
        data.forEach(function(s) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><strong>' + SkillShare.escapeHtml(s.session_title||'Session') + '</strong></td>' +
                '<td>' + SkillShare.escapeHtml(s.mentor_name||'Mentor') + '</td>' +
                '<td>' + SkillShare.formatDate(s.session_date) + '</td>' +
                '<td>' + SkillShare.formatTime(s.session_time) + '</td>' +
                '<td>' + SkillShare.escapeHtml(s.duration||'60 min') + '</td>' +
                '<td><span class="status-pill ' + getStatusClass(s.status) + '">' + s.status + '</span></td>' +
                '<td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button></div></td></tr>';
        });
        document.getElementById('sessionsTable').innerHTML = html || '<tr><td colspan="8" style="text-align:center;">No sessions found</td></tr>';
    }
});
</script>
<?php
endDashboardPage();
?>
