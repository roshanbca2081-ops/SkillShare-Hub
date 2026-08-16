<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Sessions';
$sidebar_role = 'mentor';
$sidebar_active = 'Sessions';

startDashboardPage();
?>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 12px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--glass-border); }
.data-table td { padding: 12px 10px; font-size: 0.85rem; border-bottom: 1px solid var(--glass-border); vertical-align: middle; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.status-badge { padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: capitalize; }
.status-badge.status-scheduled { background: rgba(139,92,246,0.15); color: var(--secondary-400); }
.status-badge.status-completed { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-cancelled { background: rgba(239,68,68,0.15); color: var(--danger); }
.status-badge.status-ongoing { background: rgba(59,130,246,0.15); color: var(--primary-400); }
.btn-sm { padding: 5px 12px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.75rem; cursor: pointer; transition: all 0.3s ease; }
.btn-sm:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px; }
.modal-overlay.open { display: flex; }
.modal { background: #1a1a2e; border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 24px; max-width: 520px; width: 100%; max-height: 90vh; overflow-y: auto; }
.modal h3 { margin-bottom: 16px; }
.modal .form-group { margin-bottom: 12px; }
.modal .form-group label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px; }
.modal .form-group textarea, .modal .form-group input { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: var(--radius-md); color: var(--text-primary); outline: none; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; }
</style>

<div style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Session</th><th>Student</th><th>Date</th><th>Time</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="sessionsBody"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="sessionModal">
    <div class="modal">
        <h3 id="modalTitle">Session Feedback</h3>
        <div class="form-group"><label>Feedback</label><textarea id="sessionFeedback" rows="4" placeholder="Enter your feedback..."></textarea></div>
        <div class="form-group"><label>Rating (1-5)</label><input type="number" id="sessionRating" min="1" max="5" value="5"></div>
        <div class="modal-actions">
            <button class="btn-sm" onclick="closeModal()">Cancel</button>
            <button class="btn-sm" onclick="submitFeedback()" style="background:var(--gradient-primary);border-color:var(--primary-500);color:#fff;">Save</button>
        </div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var currentSessionId = null;

function renderSessions(list) {
    var tbody = document.getElementById('sessionsBody');
    if (!list || !list.length) { tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);">No sessions found</td></tr>'; return; }
    var html = '';
    list.forEach(function(s) {
        var actions = '';
        if (s.status === 'scheduled' || s.status === 'ongoing') {
            actions = '<button class="btn-sm" onclick="openFeedback(' + s.id + ')"><i class="fas fa-star"></i> Feedback</button>';
        } else if (s.status === 'completed') {
            actions = '<button class="btn-sm" onclick="viewSession(' + s.id + ')"><i class="fas fa-eye"></i> View</button>';
        }
        html += '<tr><td><strong>' + SkillShare.escapeHtml(s.session_title) + '</strong></td>' +
            '<td>' + SkillShare.escapeHtml(s.fresher_name || 'N/A') + '</td>' +
            '<td>' + SkillShare.formatDate(s.session_date) + '</td>' +
            '<td>' + SkillShare.formatTime(s.session_time) + '</td>' +
            '<td>' + (s.duration || 60) + ' min</td>' +
            '<td><span class="status-badge status-' + s.status + '">' + s.status + '</span></td>' +
            '<td><div style="display:flex;gap:6px;flex-wrap:wrap;">' + actions + '</div></td></tr>';
    });
    tbody.innerHTML = html;
}

function openFeedback(id) {
    currentSessionId = id;
    document.getElementById('modalTitle').textContent = 'Session #' + id + ' Feedback';
    document.getElementById('sessionFeedback').value = '';
    document.getElementById('sessionRating').value = '5';
    document.getElementById('sessionModal').classList.add('open');
}

function closeModal() {
    document.getElementById('sessionModal').classList.remove('open');
    currentSessionId = null;
}

function submitFeedback() {
    if (!currentSessionId) return;
    var feedback = document.getElementById('sessionFeedback').value;
    var rating = document.getElementById('sessionRating').value;
    SkillShare.apiFetch(BASE + 'api/sessions.php?action=add_feedback&id=' + currentSessionId, {
        method: 'POST',
        body: JSON.stringify({ feedback: feedback, rating: rating })
    }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Feedback saved', 'success'); closeModal(); loadSessions(); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
}

function viewSession(id) {
    SkillShare.apiFetch(BASE + 'api/sessions.php?action=show&id=' + id).then(function(res) {
        if (res.success) {
            var s = res.data;
            SkillShare.showToast('Session Details', s.session_title + ' | Feedback: ' + (s.feedback_mentor || 'None'), 'info', 5000);
        }
    });
}

function loadSessions() {
    SkillShare.apiFetch(BASE + 'api/sessions.php?action=list').then(function(res) {
        if (res.success) renderSessions(res.data);
    });
}

loadSessions();
</script>

<?php
endDashboardPage();
