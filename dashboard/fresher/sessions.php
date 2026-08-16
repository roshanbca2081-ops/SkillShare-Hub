<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Sessions';
$sidebar_role = 'fresher';
$sidebar_active = 'Sessions';

startDashboardPage();
?>

<style>
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.table-responsive-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
.data-table th { color: var(--text-muted); font-weight: 500; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.user-cell { display: flex; align-items: center; gap: 10px; }
.user-cell img { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
.status-badge {
    padding: 4px 12px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
}
.status-scheduled { background: rgba(59,130,246,0.15); color: var(--primary-400); }
.status-completed { background: rgba(34,197,94,0.15); color: var(--success); }
.status-cancelled { background: rgba(239,68,68,0.15); color: var(--danger); }
.table-actions { display: flex; gap: 6px; }
.icon-btn {
    width: 32px; height: 32px; border-radius: var(--radius-md); border: 1px solid var(--glass-border);
    background: transparent; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all 0.3s ease; font-size: 0.8rem;
}
.icon-btn:hover { background: rgba(255,255,255,0.08); color: var(--text-primary); }
.empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
.modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000;
    display: none; align-items: center; justify-content: center; padding: 20px;
}
.modal-overlay.active { display: flex; }
.modal-box {
    background: rgba(20,20,40,0.95); border: 1px solid var(--glass-border); border-radius: var(--radius-lg);
    padding: 24px; max-width: 560px; width: 100%; max-height: 90vh; overflow-y: auto;
}
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.modal-close { background: none; border: none; color: var(--text-muted); font-size: 1.2rem; cursor: pointer; }
.detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
.detail-label { color: var(--text-muted); }
.detail-value { color: var(--text-primary); font-weight: 500; }
</style>

<div class="toolbar">
    <h3 style="margin:0;"><i class="fas fa-clock" style="color:var(--primary-400);margin-right:8px;"></i> My Sessions</h3>
    <select class="filter-select" id="sessionFilter"><option value="">All Sessions</option><option value="scheduled">Scheduled</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-responsive-wrap">
        <table class="data-table">
            <thead><tr><th>Session</th><th>Mentor</th><th>Date</th><th>Time</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="sessionsTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="sessionModal">
    <div class="modal-box">
        <div class="modal-header"><h4 style="margin:0;">Session Details</h4><button class="modal-close" onclick="closeSessionModal()">&times;</button></div>
        <div id="sessionDetails"></div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderSessions(sessions) {
    var tbody = document.getElementById('sessionsTable');
    if (!sessions.length) { tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;"><i class="fas fa-clock" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:8px;"></i>No sessions found</td></tr>'; return; }
    var html = '';
    sessions.forEach(function(s) {
        html += '<tr>' +
            '<td><strong>' + SkillShare.escapeHtml(s.session_title) + '</strong></td>' +
            '<td><div class="user-cell"><img src="' + (s.mentor_avatar ? BASE + 'frontend/assets/images/profile/' + s.mentor_avatar : 'https://ui-avatars.com/32/?background=3b82f6&color=fff') + '" alt="">' + SkillShare.escapeHtml(s.mentor_name) + '</div></td>' +
            '<td>' + SkillShare.formatDate(s.session_date) + '</td>' +
            '<td>' + SkillShare.formatTime(s.session_time) + '</td>' +
            '<td>' + SkillShare.formatDuration(s.duration) + '</td>' +
            '<td><span class="status-badge status-' + s.status + '">' + s.status + '</span></td>' +
            '<td><div class="table-actions">' +
                (s.meeting_link ? '<button class="icon-btn" title="Join" onclick="window.open(\'' + SkillShare.escapeHtml(s.meeting_link) + '\',\'_blank\')"><i class="fas fa-video"></i></button>' : '') +
                '<button class="icon-btn" title="Details" onclick="viewSession(' + s.id + ')"><i class="fas fa-eye"></i></button>' +
            '</div></td></tr>';
    });
    tbody.innerHTML = html;
}

document.getElementById('sessionFilter').addEventListener('change', function() {
    var params = '?action=list' + (this.value ? '&status=' + this.value : '');
    SkillShare.apiFetch(BASE + 'api/sessions.php' + params).then(function(res) {
        if (res.success) renderSessions(res.data);
    });
});

function viewSession(id) {
    SkillShare.apiFetch(BASE + 'api/sessions.php?action=show&id=' + id).then(function(res) {
        if (!res.success) { SkillShare.showToast('Error', 'Session not found', 'error'); return; }
        var s = res.data;
        var html = '<div class="detail-row"><span class="detail-label">Session</span><span class="detail-value">' + SkillShare.escapeHtml(s.session_title) + '</span></div>' +
            '<div class="detail-row"><span class="detail-label">Mentor</span><span class="detail-value">' + SkillShare.escapeHtml(s.mentor_name) + '</span></div>' +
            '<div class="detail-row"><span class="detail-label">Date</span><span class="detail-value">' + SkillShare.formatDate(s.session_date) + '</span></div>' +
            '<div class="detail-row"><span class="detail-label">Time</span><span class="detail-value">' + SkillShare.formatTime(s.session_time) + '</span></div>' +
            '<div class="detail-row"><span class="detail-label">Duration</span><span class="detail-value">' + SkillShare.formatDuration(s.duration) + '</span></div>' +
            '<div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><span class="status-badge status-' + s.status + '">' + s.status + '</span></span></div>' +
            (s.feedback_mentor ? '<div class="detail-row"><span class="detail-label">Mentor Feedback</span><span class="detail-value">' + SkillShare.escapeHtml(s.feedback_mentor) + '</span></div>' : '') +
            (s.meeting_link ? '<div class="detail-row"><span class="detail-label">Link</span><span class="detail-value"><a href="' + SkillShare.escapeHtml(s.meeting_link) + '" target="_blank" style="color:var(--primary-400);">Join Session</a></span></div>' : '');
        document.getElementById('sessionDetails').innerHTML = html;
        document.getElementById('sessionModal').classList.add('active');
    });
}
function closeSessionModal() { document.getElementById('sessionModal').classList.remove('active'); }

SkillShare.apiFetch(BASE + 'api/sessions.php?action=list').then(function(res) {
    if (res.success) renderSessions(res.data);
});
</script>

<?php
endDashboardPage();
