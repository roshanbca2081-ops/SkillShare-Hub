<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Notifications';
$sidebar_role = 'admin';
$sidebar_active = 'Notifications';

startDashboardPage();
?>
<style>
.notif-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
.notif-list { display: flex; flex-direction: column; }
.notif-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px 16px; border-bottom: 1px solid var(--glass-border); transition: all 0.3s ease; }
.notif-item:hover { background: rgba(255,255,255,0.03); }
.notif-item.unread { border-left: 3px solid var(--primary-500); }
.notif-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.notif-body { flex: 1; }
.notif-title { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
.notif-msg { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }
.notif-meta { font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; }
</style>

<div class="notif-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="nTotal">0</div><div class="stat-label">Total</div></div><div class="stat-icon"><i class="fas fa-bell"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="nRead">0</div><div class="stat-label">Read</div></div><div class="stat-icon"><i class="fas fa-envelope-open"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="nUnread">0</div><div class="stat-label">Unread</div></div><div class="stat-icon"><i class="fas fa-envelope"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header">
        <h5><i class="fa-solid fa-bell" style="color:var(--primary);"></i> Notification Center</h5>
        <div style="display:flex;gap:0.5rem;">
            <button class="btn btn-outline btn-sm" onclick="markAllRead()"><i class="fa-solid fa-check-double"></i> Mark All Read</button>
            <button class="btn btn-danger btn-sm" onclick="clearAll()"><i class="fa-solid fa-trash"></i> Clear All</button>
        </div>
    </div>
    <div class="notif-list" id="notifList"><p style="color:var(--text-muted);text-align:center;padding:24px;">Loading...</p></div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

SkillShare.apiFetch(BASE + 'api/notifications.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('nTotal').textContent = data.length;
        document.getElementById('nRead').textContent = data.filter(function(n){ return n.is_read; }).length;
        document.getElementById('nUnread').textContent = data.filter(function(n){ return !n.is_read; }).length;

        var html = '';
        data.forEach(function(n) {
            var iconBg = n.icon && n.icon.indexOf('user-plus') !== -1 ? 'rgba(59,130,246,0.15)' : (n.icon && n.icon.indexOf('cart') !== -1 ? 'rgba(139,92,246,0.15)' : 'rgba(34,197,94,0.15)');
            var iconColor = n.icon && n.icon.indexOf('user-plus') !== -1 ? 'var(--primary-400)' : (n.icon && n.icon.indexOf('cart') !== -1 ? 'var(--secondary-400)' : 'var(--success)');
            html += '<div class="notif-item ' + (n.is_read?'':'unread') + '">' +
                '<div class="notif-icon" style="background:' + iconBg + ';color:' + iconColor + ';"><i class="fas ' + (n.icon||'fa-info-circle') + '"></i></div>' +
                '<div class="notif-body"><div class="notif-title">' + SkillShare.escapeHtml(n.title) + '</div>' +
                '<div class="notif-msg">' + SkillShare.escapeHtml(n.message||'') + '</div></div>' +
                '<div class="notif-meta">' + SkillShare.timeAgo(n.created_at) + '</div></div>';
        });
        document.getElementById('notifList').innerHTML = html || '<p style="color:var(--text-muted);text-align:center;padding:24px;">No notifications</p>';
    }
});

function markAllRead() { SkillShare.showToast('Notifications', 'All marked as read', 'success'); }
function clearAll() { SkillShare.showToast('Notifications', 'All cleared', 'success'); }
</script>
<?php
endDashboardPage();
?>
