<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Activity Logs';
$sidebar_role = 'admin';
$sidebar_active = 'Activity Logs';

startDashboardPage();
?>
<style>
.log-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
.log-list { display: flex; flex-direction: column; }
.log-item { display: flex; align-items: flex-start; gap: 14px; padding: 12px 16px; border-bottom: 1px solid var(--glass-border); }
.log-dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 6px; flex-shrink: 0; }
.log-body { flex: 1; }
.log-title { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
.log-desc { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }
.log-time { font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; }
</style>

<div class="log-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="lTotal">0</div><div class="stat-label">Total Logs</div></div><div class="stat-icon"><i class="fas fa-history"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="lToday">0</div><div class="stat-label">Today</div></div><div class="stat-icon"><i class="fas fa-calendar-day"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="lUsers">0</div><div class="stat-label">User Actions</div></div><div class="stat-icon"><i class="fas fa-users"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header">
        <h5><i class="fa-solid fa-history" style="color:var(--primary);"></i> Activity Logs</h5>
        <button class="btn btn-outline btn-sm" onclick="refreshLogs()"><i class="fa-solid fa-rotate"></i> Refresh</button>
    </div>
    <div class="table-responsive-wrap">
        <div class="log-list" id="logList"><p style="color:var(--text-muted);text-align:center;padding:24px;">Loading...</p></div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var logColors = {login:'var(--success)', update:'var(--primary-400)', delete:'var(--danger)', create:'var(--secondary-400)', view:'var(--warning)'};

SkillShare.apiFetch(BASE + 'api/admin.php?action=stats').then(function(res) {
    if (res.success && res.data && res.data.recent_users) {
        var users = res.data.recent_users;
        document.getElementById('lTotal').textContent = users.length;
        document.getElementById('lToday').textContent = users.filter(function(u){ return SkillShare.timeAgo(u.created_at).indexOf('d')===-1 && SkillShare.timeAgo(u.created_at).indexOf('w')===-1 && SkillShare.timeAgo(u.created_at).indexOf('m')===-1 && SkillShare.timeAgo(u.created_at).indexOf('s')===-1; }).length;
        document.getElementById('lUsers').textContent = users.length;

        var html = '';
        users.forEach(function(u) {
            var action = 'User ' + u.full_name + ' registered (' + u.role + ')';
            html += '<div class="log-item"><div class="log-dot" style="background:' + logColors.create + ';"></div>' +
                '<div class="log-body"><div class="log-title">User Registration</div><div class="log-desc">' + SkillShare.escapeHtml(action) + '</div></div>' +
                '<div class="log-time">' + SkillShare.escapeHtml(u.created_at) + '</div></div>';
        });
        document.getElementById('logList').innerHTML = html || '<p style="color:var(--text-muted);text-align:center;padding:24px;">No logs found</p>';
    }
});

function refreshLogs() { location.reload(); }
</script>
<?php
endDashboardPage();
?>
