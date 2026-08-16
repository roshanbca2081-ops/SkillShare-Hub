<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Assignments';
$sidebar_role = 'mentor';
$sidebar_active = 'Assignments';

startDashboardPage();
?>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 12px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--glass-border); }
.data-table td { padding: 12px 10px; font-size: 0.85rem; border-bottom: 1px solid var(--glass-border); vertical-align: middle; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.status-badge { padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; }
.status-badge.status-open { background: rgba(59,130,246,0.15); color: var(--primary-400); }
.status-badge.status-graded { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-published { background: rgba(59,130,246,0.15); color: var(--primary-400); }
.status-badge.status-closed { background: rgba(239,68,68,0.15); color: var(--danger); }
.btn-sm { padding: 5px 12px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.75rem; cursor: pointer; transition: all 0.3s ease; }
.btn-sm:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
.btn-primary { padding: 8px 16px; border-radius: var(--radius-full); background: var(--gradient-primary); border: none; color: #fff; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.8rem; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(59,130,246,0.3); }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px; }
.modal-overlay.open { display: flex; }
.modal { background: #1a1a2e; border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 24px; max-width: 560px; width: 100%; max-height: 90vh; overflow-y: auto; }
.modal h3 { margin-bottom: 16px; }
.form-group { margin-bottom: 12px; }
.form-group label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px; }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: var(--radius-md); color: var(--text-primary); outline: none; }
.submission-item { background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: var(--radius-md); padding: 14px; margin-bottom: 10px; }
.submission-item .head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.submission-item p { font-size: 0.8rem; color: var(--text-secondary); margin: 4px 0; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; }
</style>

<div class="section-header">
    <h3><i class="fas fa-file-pen" style="color:var(--primary-400);margin-right:8px;"></i>Assignments</h3>
    <button class="btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i> New Assignment</button>
</div>

<div style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Assignment</th><th>Course</th><th>Deadline</th><th>Submissions</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="assignmentsBody"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="createModal">
    <div class="modal">
        <h3>Create Assignment</h3>
        <form id="createForm">
            <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            <div class="form-group"><label>Deadline</label><input type="datetime-local" name="deadline" required></div>
            <div class="modal-actions">
                <button type="button" class="btn-sm" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="reviewModal">
    <div class="modal">
        <h3>Review Submissions</h3>
        <div id="reviewContent"></div>
        <div class="modal-actions">
            <button type="button" class="btn-sm" onclick="closeReviewModal()">Close</button>
        </div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderAssignments(list) {
    var tbody = document.getElementById('assignmentsBody');
    if (!list || !list.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);">No assignments found</td></tr>'; return; }
    var html = '';
    list.forEach(function(a) {
        html += '<tr><td><strong>' + SkillShare.escapeHtml(a.title) + '</strong></td>' +
            '<td>' + SkillShare.escapeHtml(a.course_name || 'N/A') + '</td>' +
            '<td>' + SkillShare.formatDate(a.deadline) + '</td>' +
            '<td>' + (a.pending_submissions || a.total_submissions || 0) + '</td>' +
            '<td><span class="status-badge status-' + a.status + '">' + a.status + '</span></td>' +
            '<td><div style="display:flex;gap:6px;flex-wrap:wrap;"><button class="btn-sm" onclick="reviewSubmissions(' + a.id + ')">Review</button><button class="btn-sm" onclick="SkillShare.showToast(\'Edit\',\'Edit mode coming soon\',\'info\')">Edit</button></div></td></tr>';
    });
    tbody.innerHTML = html;
}

function openCreate() { document.getElementById('createModal').classList.add('open'); }
function closeModal() { document.getElementById('createModal').classList.remove('open'); }
function closeReviewModal() { document.getElementById('reviewModal').classList.remove('open'); }

document.getElementById('createForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    SkillShare.apiFetch(BASE + 'api/assignments.php?action=create', { method: 'POST', body: formData }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Assignment created', 'success'); closeModal(); loadAssignments(); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
});

function reviewSubmissions(id) {
    SkillShare.apiFetch(BASE + 'api/assignments.php?action=show&id=' + id).then(function(res) {
        if (res.success && res.data) {
            var d = res.data;
            var html = '<p style="color:var(--text-secondary);font-size:0.9rem;"><strong>' + SkillShare.escapeHtml(d.title) + '</strong> — ' + SkillShare.escapeHtml(d.course_name || '') + '</p>';
            if (d.submissions && d.submissions.length) {
                d.submissions.forEach(function(sub) {
                    html += '<div class="submission-item"><div class="head"><strong>' + SkillShare.escapeHtml(sub.fresher_name) + '</strong><span class="status-badge status-pending">' + (sub.status || 'submitted') + '</span></div>' +
                        '<p>Submitted: ' + SkillShare.formatDate(sub.submitted_at) + '</p>' +
                        (sub.submission_text ? '<p>' + SkillShare.escapeHtml(sub.submission_text) + '</p>' : '') +
                        (sub.file_path ? '<p><a href="' + BASE + 'uploads/submissions/' + sub.file_path + '" target="_blank" style="color:var(--primary-400);">Download File</a></p>' : '') +
                        '<div style="margin-top:8px;display:flex;gap:8px;"><input type="number" placeholder="Score" id="score-' + sub.id + '" style="width:80px;padding:6px 10px;background:rgba(255,255,255,0.05);border:1px solid var(--glass-border);border-radius:6px;color:var(--text-primary);"><button class="btn-sm" onclick="gradeSubmission(' + sub.id + ')">Grade</button></div></div>';
                });
            } else {
                html += '<p style="color:var(--text-muted);">No submissions yet.</p>';
            }
            document.getElementById('reviewContent').innerHTML = html;
            document.getElementById('reviewModal').classList.add('open');
        }
    });
}

function gradeSubmission(submissionId) {
    var score = document.getElementById('score-' + submissionId).value;
    if (!score) { SkillShare.showToast('Error', 'Enter a score', 'error'); return; }
    SkillShare.apiFetch(BASE + 'api/assignments.php?action=grade&submission_id=' + submissionId, {
        method: 'POST',
        body: JSON.stringify({ score: score })
    }).then(function(res) {
        if (res.success) { SkillShare.showToast('Graded', 'Submission graded', 'success'); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
}

function loadAssignments() {
    SkillShare.apiFetch(BASE + 'api/assignments.php?action=list').then(function(res) {
        if (res.success) renderAssignments(res.data);
    });
}

loadAssignments();
</script>

<?php
endDashboardPage();
