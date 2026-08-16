<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Notifications';
$sidebar_role = 'fresher';
$sidebar_active = 'Notifications';

startDashboardPage();
?>

<style>
.notif-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
.notif-stat {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 16px 20px;
}
.notif-stat-value { font-size: 1.4rem; font-weight: 700; color: var(--text-primary); }
.notif-stat-label { font-size: 0.75rem; color: var(--text-muted); }
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.notif-list { display: flex; flex-direction: column; }
.notif-item {
    display: flex; align-items: flex-start; gap: 16px; padding: 16px 20px;
    border-bottom: 1px solid var(--glass-border); transition: all 0.3s ease;
}
.notif-item:hover { background: rgba(255,255,255,0.02); }
.notif-item.unread { background: rgba(59,130,246,0.04); border-left: 3px solid var(--primary-400); }
.notif-icon {
    width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    font-size: 1.1rem;
}
.notif-content { flex: 1; }
.notif-title { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }
.notif-message { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }
.notif-time { font-size: 0.7rem; color: var(--text-muted); white-space: nowrap; }
.icon-btn {
    width: 28px; height: 28px; border-radius: var(--radius-md); border: 1px solid var(--glass-border);
    background: transparent; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all 0.3s ease; font-size: 0.75rem;
}
.icon-btn:hover { background: rgba(255,255,255,0.08); color: var(--text-primary); }
.empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
</style>

<div class="notif-stats" id="notifStats">
    <div class="notif-stat"><div class="notif-stat-value" id="statTotal">0</div><div class="notif-stat-label">Total</div></div>
    <div class="notif-stat"><div class="notif-stat-value" id="statUnread">0</div><div class="notif-stat-label">Unread</div></div>
    <div class="notif-stat"><div class="notif-stat-value" id="statRead">0</div><div class="notif-stat-label">Read</div></div>
</div>

<div class="toolbar">
    <h3 style="margin:0;"><i class="fas fa-bell" style="color:var(--primary-400);margin-right:8px;"></i> Notifications</h3>
    <button class="btn btn-outline btn-sm" onclick="markAllRead()"><i class="fas fa-check-double"></i> Mark All Read</button>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <div class="notif-list" id="notifList">
        <div style="padding:40px;text-align:center;color:var(--text-muted);">Loading...</div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderNotifications(notifs) {
    var list = document.getElementById('notifList');
    if (!notifs.length) { list.innerHTML = '<div class="empty-state"><i class="fas fa-bell-slash" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:8px;"></i>No notifications</div>'; return; }
    var html = '';
    var iconMap = { booking: 'fa-calendar-check', assignment: 'fa-file-pen', certificate: 'fa-award', payment: 'fa-credit-card', message: 'fa-envelope', system: 'fa-info-circle' };
    var colorMap = { booking: 'var(--primary-400)', assignment: 'var(--secondary-400)', certificate: '#f59e0b', payment: 'var(--success)', message: 'var(--primary-500)', system: 'var(--text-muted)' };
    notifs.forEach(function(n) {
        html += '<div class="notif-item ' + (n.is_read ? '' : 'unread') + '">' +
            '<div class="notif-icon" style="background:' + (colorMap[n.type] || 'var(--primary-400)') + '20;color:' + (colorMap[n.type] || 'var(--primary-400)') + ';"><i class="fas ' + (n.icon || iconMap[n.type] || 'fa-info-circle') + '"></i></div>' +
            '<div class="notif-content"><div class="notif-title">' + SkillShare.escapeHtml(n.title) + (n.is_read ? '' : ' <span style="font-size:0.65rem;background:var(--primary-500);color:#fff;padding:2px 8px;border-radius:var(--radius-full);">New</span>') + '</div>' +
            '<div class="notif-message">' + SkillShare.escapeHtml(n.message || '') + '</div></div>' +
            '<div style="text-align:right;"><div class="notif-time">' + SkillShare.timeAgo(n.created_at) + '</div>' +
            (!n.is_read ? '<button class="icon-btn" style="margin-top:4px;" onclick="markRead(' + n.id + ')" title="Mark read"><i class="fas fa-check"></i></button>' : '') +
            '</div></div>';
    });
    list.innerHTML = html;
    document.getElementById('statTotal').textContent = notifs.length;
    document.getElementById('statUnread').textContent = notifs.filter(function(n){ return !n.is_read; }).length;
    document.getElementById('statRead').textContent = notifs.filter(function(n){ return n.is_read; }).length;
}

function loadNotifications() {
    SkillShare.apiFetch(BASE + 'api/notifications.php?action=list').then(function(res) {
        if (res.success) renderNotifications(res.data);
    });
}

function markRead(id) {
    fetch(BASE + 'api/notifications.php?action=mark_read&id=' + id, { method: 'POST' })
        .then(function(r) { return r.json(); })
        .then(function(res) { if (res.success) loadNotifications(); });
}

function markAllRead() {
    fetch(BASE + 'api/notifications.php?action=mark_all_read', { method: 'POST' })
        .then(function(r) { return r.json(); })
        .then(function(res) { if (res.success) { SkillShare.showToast('Success', 'All notifications marked as read', 'success'); loadNotifications(); } });
}

loadNotifications();
</script>

<?php
endDashboardPage();
