<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Notifications';
$sidebar_role = 'mentor';
$sidebar_active = 'Notifications';

startDashboardPage();
?>

<style>
.notif-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 20px; }
.notif-stat { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 14px 16px; text-align: center; }
.notif-stat .num { font-family: var(--font-heading); font-weight: 700; font-size: 1.4rem; }
.notif-stat .lbl { font-size: 0.75rem; color: var(--text-muted); }
.notif-list { display: flex; flex-direction: column; gap: 0; }
.notif-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px 18px; border-bottom: 1px solid var(--glass-border); transition: all 0.3s ease; }
.notif-item:hover { background: rgba(255,255,255,0.02); }
.notif-item.unread { border-left: 3px solid var(--primary-500); }
.notif-icon { width: 40px; height: 40px; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem; }
.notif-item .content { flex: 1; min-width: 0; }
.notif-item .content strong { display: block; font-size: 0.9rem; margin-bottom: 2px; }
.notif-item .content p { margin: 0; font-size: 0.8rem; color: var(--text-muted); }
.notif-item .time { font-size: 0.7rem; color: var(--text-muted); white-space: nowrap; }
.notif-item .del-btn { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1rem; padding: 4px; }
.notif-item .del-btn:hover { color: var(--danger); }
</style>

<div class="notif-stats" id="notifStats">
    <div class="notif-stat"><div class="num" id="nTotal">0</div><div class="lbl">Total</div></div>
    <div class="notif-stat"><div class="num" id="nRead" style="color:var(--success);">0</div><div class="lbl">Read</div></div>
    <div class="notif-stat"><div class="num" id="nUnread" style="color:var(--warning);">0</div><div class="lbl">Unread</div></div>
</div>

<div style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;">
    <div style="padding:12px 18px;border-bottom:1px solid var(--glass-border);display:flex;justify-content:space-between;align-items:center;">
        <strong style="font-size:0.95rem;">Notifications</strong>
        <button class="btn-sm" onclick="markAllRead()">Mark All Read</button>
    </div>
    <div class="notif-list" id="notifList">
        <div style="padding:40px;text-align:center;color:var(--text-muted);">Loading...</div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderNotifications(list) {
    var container = document.getElementById('notifList');
    var total = list ? list.length : 0;
    var read = list ? list.filter(function(n) { return n.is_read; }).length : 0;
    document.getElementById('nTotal').textContent = total;
    document.getElementById('nRead').textContent = read;
    document.getElementById('nUnread').textContent = total - read;

    if (!list || !list.length) { container.innerHTML = '<div style="padding:40px;text-align:center;color:var(--text-muted);">No notifications</div>'; return; }
    var html = '';
    list.forEach(function(n) {
        var iconBg = n.color || 'rgba(59,130,246,0.12)';
        var iconColor = 'var(--primary-400)';
        html += '<div class="notif-item ' + (n.is_read ? '' : 'unread') + '">' +
            '<div class="notif-icon" style="background:' + iconBg + ';color:' + iconColor + ';"><i class="fas ' + (n.icon || 'fa-info-circle') + '"></i></div>' +
            '<div class="content"><strong>' + SkillShare.escapeHtml(n.title) + '</strong><p>' + SkillShare.escapeHtml(n.message || '') + '</p></div>' +
            '<span class="time">' + SkillShare.timeAgo(n.created_at) + '</span>' +
            '<button class="del-btn" onclick="deleteNotif(' + n.id + ')">&times;</button></div>';
    });
    container.innerHTML = html;
}

function markAllRead() {
    SkillShare.apiFetch(BASE + 'api/notifications.php?action=mark_all_read', { method: 'POST' }).then(function(res) {
        if (res.success) { SkillShare.showToast('Done', 'All marked as read', 'success'); loadNotifications(); }
    });
}

function deleteNotif(id) {
    SkillShare.apiFetch(BASE + 'api/notifications.php?action=delete&id=' + id, { method: 'POST' }).then(function(res) {
        if (res.success) loadNotifications();
    });
}

function loadNotifications() {
    SkillShare.apiFetch(BASE + 'api/notifications.php?action=list').then(function(res) {
        if (res.success) renderNotifications(res.data);
    });
}

loadNotifications();
</script>

<?php
endDashboardPage();
