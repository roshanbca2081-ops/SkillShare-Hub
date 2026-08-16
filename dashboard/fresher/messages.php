<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Messages';
$sidebar_role = 'fresher';
$sidebar_active = 'Messages';

startDashboardPage();
?>

<style>
.chat-layout { display: grid; grid-template-columns: 300px 1fr; height: calc(100vh - 200px); min-height: 400px; border: 1px solid var(--glass-border); border-radius: var(--radius-lg); overflow: hidden; }
.chat-sidebar { background: rgba(255,255,255,0.02); border-right: 1px solid var(--glass-border); display: flex; flex-direction: column; }
.chat-sidebar .search-box { margin: 12px; }
.chat-list { flex: 1; overflow-y: auto; }
.chat-item {
    display: flex; align-items: center; gap: 12px; padding: 12px 16px; cursor: pointer;
    transition: all 0.3s ease; border-bottom: 1px solid rgba(255,255,255,0.03);
}
.chat-item:hover, .chat-item.active { background: rgba(59,130,246,0.08); }
.chat-item img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.chat-info { flex: 1; min-width: 0; }
.chat-info h6 { font-size: 0.85rem; margin: 0; color: var(--text-primary); }
.chat-info p { font-size: 0.75rem; color: var(--text-muted); margin: 2px 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.chat-time { font-size: 0.7rem; color: var(--text-muted); }
.chat-main { display: flex; flex-direction: column; background: rgba(255,255,255,0.01); }
.chat-header { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; }
.chat-body { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 10px; }
.msg { max-width: 70%; padding: 10px 14px; border-radius: var(--radius-lg); font-size: 0.85rem; line-height: 1.5; }
.msg.sent { align-self: flex-end; background: var(--gradient-primary); color: #fff; border-bottom-right-radius: 4px; }
.msg.received { align-self: flex-start; background: rgba(255,255,255,0.05); color: var(--text-secondary); border-bottom-left-radius: 4px; }
.chat-input { display: flex; gap: 10px; padding: 12px 20px; border-top: 1px solid var(--glass-border); }
.chat-input input { flex: 1; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-full); padding: 10px 16px; color: var(--text-primary); font-size: 0.85rem; outline: none; }
.chat-input input::placeholder { color: var(--text-muted); }
.chat-input button { padding: 10px 20px; border-radius: var(--radius-full); background: var(--gradient-primary); border: none; color: #fff; cursor: pointer; }
.empty-chat { display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); text-align: center; padding: 40px; }
@media (max-width: 768px) { .chat-layout { grid-template-columns: 1fr; height: auto; } }
</style>

<div class="chat-layout">
    <div class="chat-sidebar">
        <div class="search-box" style="display:flex;align-items:center;background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-full);padding:8px 16px;">
            <i class="fas fa-search" style="color:var(--text-muted);margin-right:8px;"></i>
            <input type="text" id="chatSearch" placeholder="Search chats..." style="background:transparent;border:none;padding:4px 8px;color:var(--text-primary);font-size:0.85rem;outline:none;flex:1;">
        </div>
        <div class="chat-list" id="chatList"><p style="padding:20px;text-align:center;color:var(--text-muted);font-size:0.85rem;">Loading...</p></div>
    </div>
    <div class="chat-main" id="chatMain">
        <div class="empty-chat"><div><i class="fas fa-comments" style="font-size:3rem;opacity:0.3;margin-bottom:12px;display:block;"></i>Select a conversation to start messaging</div></div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var currentConversation = null;

function renderConversations(convs) {
    var list = document.getElementById('chatList');
    if (!convs.length) { list.innerHTML = '<p style="padding:20px;text-align:center;color:var(--text-muted);font-size:0.85rem;">No conversations</p>'; return; }
    var html = '';
    convs.forEach(function(c) {
        html += '<div class="chat-item" data-id="' + c.user_id + '" onclick="openConversation(' + c.user_id + ', \'' + SkillShare.escapeHtml(c.name) + '\', \'' + (c.avatar || '') + '\')">' +
            '<img src="' + (c.avatar ? BASE + 'frontend/assets/images/profile/' + c.avatar : 'https://ui-avatars.com/40/?background=3b82f6&color=fff&name=' + encodeURIComponent(c.name)) + '" alt="">' +
            '<div class="chat-info"><h6>' + SkillShare.escapeHtml(c.name) + '</h6><p>' + SkillShare.escapeHtml(c.last_message || '') + '</p></div>' +
            (c.unread ? '<span style="background:var(--danger);color:#fff;font-size:0.65rem;padding:2px 8px;border-radius:var(--radius-full);">' + c.unread + '</span>' : '<span class="chat-time">' + SkillShare.timeAgo(c.last_time) + '</span>') +
        '</div>';
    });
    list.innerHTML = html;
}

function openConversation(userId, name, avatar) {
    currentConversation = userId;
    var items = document.querySelectorAll('.chat-item');
    items.forEach(function(el) { el.classList.toggle('active', parseInt(el.dataset.id) === parseInt(userId)); });
    var header = document.querySelector('.chat-header');
    if (header) header.remove();
    var main = document.getElementById('chatMain');
    var headerHtml = '<div class="chat-header"><div style="display:flex;align-items:center;gap:12px;"><img src="' + (avatar ? BASE + 'frontend/assets/images/profile/' + avatar : 'https://ui-avatars.com/44/?background=3b82f6&color=fff&name=' + encodeURIComponent(name)) + '" style="width:44px;height:44px;border-radius:50%;"><div><h6 style="margin:0;font-size:0.95rem;">' + SkillShare.escapeHtml(name) + '</h6><small style="color:var(--success);">Online</small></div></div></div>';
    var bodyHtml = '<div class="chat-body" id="chatBody" style="flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:10px;"><div class="empty-chat" style="flex:1;"><i class="fas fa-spinner fa-spin" style="font-size:1.5rem;opacity:0.5;"></i></div></div>';
    var inputHtml = '<div class="chat-input" style="border-top:1px solid var(--glass-border);padding:12px 20px;display:flex;gap:10px;"><input type="text" id="messageInput" placeholder="Type a message..." onkeydown="if(event.key===\'Enter\')sendMessage()"><button onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button></div>';
    main.innerHTML = headerHtml + bodyHtml + inputHtml;

    SkillShare.apiFetch(BASE + 'api/messages.php?action=conversation&id=' + userId).then(function(res) {
        if (!res.success) { document.getElementById('chatBody').innerHTML = '<div class="empty-chat">Failed to load messages</div>'; return; }
        var body = document.getElementById('chatBody');
        var html = '';
        res.data.forEach(function(m) {
            html += '<div class="msg ' + (m.from_user == <?php echo getUserId(); ?> ? 'sent' : 'received') + '">' + SkillShare.escapeHtml(m.message) + '</div>';
        });
        body.innerHTML = html;
        body.scrollTop = body.scrollHeight;
    });
}

function sendMessage() {
    var input = document.getElementById('messageInput');
    var text = input.value.trim();
    if (!text || !currentConversation) return;
    var fd = new FormData();
    fd.append('to_user', currentConversation);
    fd.append('message', text);
    fetch(BASE + 'api/messages.php?action=send', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                var body = document.getElementById('chatBody');
                var msgDiv = document.createElement('div');
                msgDiv.className = 'msg sent'; msgDiv.textContent = text;
                body.appendChild(msgDiv);
                body.scrollTop = body.scrollHeight;
                input.value = '';
            }
        });
}

document.getElementById('chatSearch').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    SkillShare.apiFetch(BASE + 'api/messages.php?action=conversations').then(function(res) {
        if (res.success) {
            var filtered = res.data.filter(function(c) { return c.name.toLowerCase().includes(q); });
            renderConversations(filtered);
        }
    });
});

SkillShare.apiFetch(BASE + 'api/messages.php?action=conversations').then(function(res) {
    if (res.success) renderConversations(res.data);
});
</script>

<?php
endDashboardPage();
