<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Messages';
$sidebar_role = 'admin';
$sidebar_active = 'Messages';

startDashboardPage();
?>
<style>
.msg-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
.conversation-list { display: flex; flex-direction: column; gap: 8px; }
.convo-item { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-md); padding: 14px 16px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: all 0.3s ease; }
.convo-item:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.2); }
.convo-item .c-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; flex-shrink: 0; }
.convo-item .c-body { flex: 1; }
.convo-item .c-title { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
.convo-item .c-preview { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }
.convo-item .c-meta { font-size: 0.7rem; color: var(--text-muted); white-space: nowrap; }
.unread-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary-500); display: inline-block; margin-left: 6px; }
</style>

<div class="msg-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="mTotal">0</div><div class="stat-label">Conversations</div></div><div class="stat-icon"><i class="fas fa-envelope"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="mUnread">0</div><div class="stat-label">Unread</div></div><div class="stat-icon"><i class="fas fa-envelope-open"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-envelope" style="color:var(--primary);"></i> Messages</h5></div>
    <div class="table-responsive-wrap">
        <div class="conversation-list" id="messagesList"><p style="color:var(--text-muted);text-align:center;padding:24px;">Loading...</p></div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

SkillShare.apiFetch(BASE + 'api/messages.php?action=conversations').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('mTotal').textContent = data.length;
        document.getElementById('mUnread').textContent = data.filter(function(c){ return c.unread_count > 0; }).length;

        var html = '';
        data.forEach(function(c) {
            html += '<div class="convo-item" onclick="openConversation(' + c.id + ')">' +
                '<div class="c-avatar">' + (c.other_user_name?c.other_user_name.charAt(0).toUpperCase():'?') + '</div>' +
                '<div class="c-body"><div class="c-title">' + SkillShare.escapeHtml(c.other_user_name||'User') + (c.unread_count>0?'<span class="unread-dot"></span>':'') + '</div>' +
                '<div class="c-preview">' + SkillShare.escapeHtml(c.last_message||'No messages') + '</div></div>' +
                '<div class="c-meta">' + SkillShare.timeAgo(c.updated_at||c.created_at) + '</div></div>';
        });
        document.getElementById('messagesList').innerHTML = html || '<p style="color:var(--text-muted);text-align:center;padding:24px;">No messages</p>';
    } else {
        document.getElementById('messagesList').innerHTML = '<p style="color:var(--text-muted);text-align:center;padding:24px;">No messages</p>';
    }
});

function openConversation(id) { SkillShare.showToast('Conversation', 'Open conversation ' + id, 'info'); }
</script>
<?php
endDashboardPage();
?>
