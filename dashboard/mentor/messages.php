<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Messages';
$sidebar_role = 'mentor';
$sidebar_active = 'Messages';

startDashboardPage();
?>

<style>
.messages-layout { display: grid; grid-template-columns: 300px 1fr; gap: 0; border: 1px solid var(--glass-border); border-radius: var(--radius-lg); overflow: hidden; min-height: 500px; }
@media (max-width: 768px) { .messages-layout { grid-template-columns: 1fr; } }
.msg-sidebar { background: rgba(255,255,255,0.02); border-right: 1px solid var(--glass-border); overflow-y: auto; }
.msg-search { padding: 14px; border-bottom: 1px solid var(--glass-border); }
.msg-search input { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: var(--radius-full); color: var(--text-primary); outline: none; font-size: 0.85rem; }
.conversation-item { display: flex; align-items: center; gap: 10px; padding: 12px 14px; cursor: pointer; transition: all 0.3s ease; border-bottom: 1px solid var(--glass-border); }
.conversation-item:hover, .conversation-item.active { background: rgba(59,130,246,0.08); }
.conversation-item img { width: 40px; height: 40px; border-radius: var(--radius-full); object-fit: cover; flex-shrink: 0; }
.conversation-item .info { flex: 1; min-width: 0; }
.conversation-item .info strong { display: block; font-size: 0.85rem; }
.conversation-item .info p { margin: 0; font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.conversation-item .time { font-size: 0.7rem; color: var(--text-muted); white-space: nowrap; }
.conversation-item.unread .info strong { color: var(--primary-400); }
.msg-thread { display: flex; flex-direction: column; }
.thread-header { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--glass-border); }
.thread-header img { width: 40px; height: 40px; border-radius: var(--radius-full); object-fit: cover; }
.thread-header strong { font-size: 0.9rem; }
.thread-header p { margin: 0; font-size: 0.75rem; color: var(--text-muted); }
.msg-history { flex: 1; padding: 18px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: rgba(0,0,0,0.15); min-height: 350px; }
.msg-bubble { max-width: 70%; padding: 10px 14px; border-radius: var(--radius-lg); font-size: 0.85rem; line-height: 1.4; }
.msg-bubble.received { align-self: flex-start; background: var(--glass-bg); border: 1px solid var(--glass-border); border-bottom-left-radius: 4px; }
.msg-bubble.sent { align-self: flex-end; background: var(--gradient-primary); color: #fff; border-bottom-right-radius: 4px; }
.msg-input { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-top: 1px solid var(--glass-border); }
.msg-input input { flex: 1; padding: 10px 16px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: var(--radius-full); color: var(--text-primary); outline: none; }
.msg-input button { padding: 8px 16px; border-radius: var(--radius-full); background: var(--gradient-primary); border: none; color: #fff; cursor: pointer; }
.empty-chat { display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); font-size: 0.9rem; }
</style>

<div class="messages-layout">
    <div class="msg-sidebar">
        <div class="msg-search"><input type="text" id="convSearch" placeholder="Search conversations..."></div>
        <div id="convList"><div style="padding:20px;text-align:center;color:var(--text-muted);">Loading...</div></div>
    </div>
    <div class="msg-thread" id="msgThread">
        <div class="empty-chat">Select a conversation to view messages</div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var currentConvId = null;

function renderConversations(list) {
    var container = document.getElementById('convList');
    if (!list || !list.length) { container.innerHTML = '<div style="padding:20px;text-align:center;color:var(--text-muted);">No conversations</div>'; return; }
    var html = '';
    list.forEach(function(c) {
        var avatar = c.avatar ? BASE + 'frontend/assets/images/profile/' + c.avatar : 'https://ui-avatars.com/40/' + encodeURIComponent(c.name) + '?background=3b82f6&color=fff';
        html += '<div class="conversation-item" data-id="' + c.user_id + '" onclick="openConversation(' + c.user_id + ',\'' + SkillShare.escapeHtml(c.name).replace(/'/g, "\\'") + '\',\'' + avatar + '\')">' +
            '<img src="' + avatar + '" alt=""><div class="info"><strong>' + SkillShare.escapeHtml(c.name) + '</strong><p>' + SkillShare.escapeHtml(c.last_message) + '</p></div>' +
            '<span class="time">' + SkillShare.timeAgo(c.last_time) + '</span></div>';
    });
    container.innerHTML = html;
}

function openConversation(userId, name, avatar) {
    currentConvId = userId;
    document.querySelectorAll('.conversation-item').forEach(function(el) { el.classList.toggle('active', el.dataset.id == userId); });
    var thread = document.getElementById('msgThread');
    thread.innerHTML = '<div class="thread-header"><img src="' + avatar + '" alt=""><div><strong>' + name + '</strong><p>Mentor</p></div></div>' +
        '<div class="msg-history" id="msgHistory"><div style="text-align:center;color:var(--text-muted);padding:40px;">Loading messages...</div></div>' +
        '<div class="msg-input"><input type="text" id="msgText" placeholder="Type a message..." onkeydown="if(event.key===\'Enter\') sendMessage()"><button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button></div>';
    loadMessages(userId);
}

function loadMessages(userId) {
    SkillShare.apiFetch(BASE + 'api/messages.php?action=conversation&id=' + userId).then(function(res) {
        var history = document.getElementById('msgHistory');
        if (res.success && res.data && res.data.length) {
            var html = '';
            res.data.forEach(function(m) {
                var type = m.from_user == <?php echo getUserId(); ?> ? 'sent' : 'received';
                html += '<div class="msg-bubble ' + type + '">' + SkillShare.escapeHtml(m.message) + '</div>';
            });
            history.innerHTML = html;
            history.scrollTop = history.scrollHeight;
        } else {
            history.innerHTML = '<div style="text-align:center;color:var(--text-muted);padding:40px;">No messages yet. Start the conversation!</div>';
        }
    });
}

function sendMessage() {
    var input = document.getElementById('msgText');
    var text = input.value.trim();
    if (!text || !currentConvId) return;
    SkillShare.apiFetch(BASE + 'api/messages.php?action=send', { method: 'POST', body: { to_user: currentConvId, message: text } }).then(function(res) {
        if (res.success) { input.value = ''; loadMessages(currentConvId); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
}

document.getElementById('convSearch').addEventListener('input', function() {
    var term = this.value.toLowerCase();
    document.querySelectorAll('.conversation-item').forEach(function(el) {
        el.style.display = el.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});

SkillShare.apiFetch(BASE + 'api/messages.php?action=conversations').then(function(res) {
    if (res.success) renderConversations(res.data);
});
</script>

<?php
endDashboardPage();
