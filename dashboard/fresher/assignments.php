<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Assignments';
$sidebar_role = 'fresher';
$sidebar_active = 'Assignments';

startDashboardPage();
?>

<style>
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.table-responsive-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
.data-table th { color: var(--text-muted); font-weight: 500; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.status-badge {
    padding: 4px 12px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
}
.status-submitted { background: rgba(59,130,246,0.15); color: var(--primary-400); }
.status-graded { background: rgba(34,197,94,0.15); color: var(--success); }
.status-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
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
.submission-area { margin-top: 16px; padding: 16px; background: var(--glass-bg); border-radius: var(--radius-md); }
.file-drop {
    border: 2px dashed var(--glass-border); border-radius: var(--radius-md); padding: 24px; text-align: center;
    color: var(--text-muted); cursor: pointer; transition: all 0.3s ease;
}
.file-drop:hover { border-color: var(--primary-400); color: var(--primary-400); }
.file-drop input { display: none; }
</style>

<div class="toolbar">
    <h3 style="margin:0;"><i class="fas fa-file-pen" style="color:var(--primary-400);margin-right:8px;"></i> Assignments</h3>
    <select class="filter-select" id="assignmentFilter"><option value="">All</option><option value="submitted">Submitted</option><option value="pending">Pending</option><option value="graded">Graded</option></select>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-responsive-wrap">
        <table class="data-table">
            <thead><tr><th>Assignment</th><th>Course</th><th>Mentor</th><th>Deadline</th><th>Status</th><th>Score</th><th>Actions</th></tr></thead>
            <tbody id="assignmentsTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="assignmentModal">
    <div class="modal-box">
        <div class="modal-header"><h4 style="margin:0;" id="modalTitle">Assignment</h4><button class="modal-close" onclick="closeAssignmentModal()">&times;</button></div>
        <div id="assignmentDetails"></div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderAssignments(assignments) {
    var tbody = document.getElementById('assignmentsTable');
    if (!assignments.length) { tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;"><i class="fas fa-file-pen" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:8px;"></i>No assignments found</td></tr>'; return; }
    var html = '';
    assignments.forEach(function(a) {
        var statusKey = a.submission_status || (a.submission_id ? 'submitted' : 'pending');
        var scoreDisplay = a.score !== null && a.score !== undefined ? a.score + '/' + a.max_score : (a.submission_id ? 'Pending' : 'Not Submitted');
        html += '<tr>' +
            '<td><strong>' + SkillShare.escapeHtml(a.title) + '</strong><div style="font-size:0.75rem;color:var(--text-muted);">' + SkillShare.escapeHtml((a.description || '').substring(0, 60)) + '</div></td>' +
            '<td>' + SkillShare.escapeHtml(a.course_name || 'General') + '</td>' +
            '<td>' + SkillShare.escapeHtml(a.mentor_name || '') + '</td>' +
            '<td>' + SkillShare.formatDate(a.deadline) + '</td>' +
            '<td><span class="status-badge status-' + statusKey + '">' + (statusKey.charAt(0).toUpperCase() + statusKey.slice(1)) + '</span></td>' +
            '<td>' + scoreDisplay + '</td>' +
            '<td><div class="table-actions">' +
                '<button class="icon-btn" title="View" onclick="viewAssignment(' + a.id + ')"><i class="fas fa-eye"></i></button>' +
                (!a.submission_id ? '<button class="icon-btn" title="Submit" onclick="submitAssignment(' + a.id + ')"><i class="fas fa-upload"></i></button>' : '') +
            '</div></td></tr>';
    });
    tbody.innerHTML = html;
}

document.getElementById('assignmentFilter').addEventListener('change', function() {
    var val = this.value;
    var params = '?action=list';
    if (val === 'graded') params += '&status=graded';
    SkillShare.apiFetch(BASE + 'api/assignments.php' + params).then(function(res) {
        if (res.success) {
            var filtered = res.data;
            if (val === 'submitted') filtered = res.data.filter(function(a){ return a.submission_id; });
            else if (val === 'pending') filtered = res.data.filter(function(a){ return !a.submission_id; });
            renderAssignments(filtered);
        }
    });
});

function viewAssignment(id) {
    SkillShare.apiFetch(BASE + 'api/assignments.php?action=show&id=' + id).then(function(res) {
        if (!res.success) { SkillShare.showToast('Error', 'Assignment not found', 'error'); return; }
        var a = res.data;
        var html = '<div class="detail-row"><span class="detail-label">Title</span><span class="detail-value">' + SkillShare.escapeHtml(a.title) + '</span></div>' +
            '<div class="detail-row"><span class="detail-label">Course</span><span class="detail-value">' + SkillShare.escapeHtml(a.course_name || 'General') + '</span></div>' +
            '<div class="detail-row"><span class="detail-label">Deadline</span><span class="detail-value">' + SkillShare.formatDate(a.deadline) + '</span></div>' +
            '<div class="detail-row"><span class="detail-label">Max Score</span><span class="detail-value">' + (a.max_score || 100) + '</span></div>' +
            '<div style="margin-top:12px;"><strong>Description:</strong><p style="color:var(--text-secondary);font-size:0.85rem;margin-top:4px;">' + SkillShare.escapeHtml(a.description || '') + '</p></div>' +
            (a.submission ? '<div style="margin-top:16px;padding:12px;background:var(--glass-bg);border-radius:var(--radius-md);"><strong>Your Submission:</strong>' +
                (a.submission.submission_text ? '<p style="font-size:0.85rem;margin-top:4px;">' + SkillShare.escapeHtml(a.submission.submission_text) + '</p>' : '') +
                (a.submission.file_path ? '<div style="margin-top:8px;"><a href="' + BASE + 'uploads/submissions/' + a.submission.file_path + '" target="_blank" class="btn btn-outline btn-sm"><i class="fas fa-download"></i> Download</a></div>' : '') +
                '<div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Submitted: ' + SkillShare.formatDate(a.submission.submitted_at) + '</div></div>' : '');
        if (a.feedback) html += '<div style="margin-top:12px;padding:12px;background:rgba(34,197,94,0.05);border-radius:var(--radius-md);"><strong>Feedback:</strong><p style="font-size:0.85rem;margin-top:4px;">' + SkillShare.escapeHtml(a.feedback) + '</p></div>';
        document.getElementById('modalTitle').textContent = a.title;
        document.getElementById('assignmentDetails').innerHTML = html;
        document.getElementById('assignmentModal').classList.add('active');
    });
}

function submitAssignment(id) {
    SkillShare.apiFetch(BASE + 'api/assignments.php?action=show&id=' + id).then(function(res) {
        if (!res.success) return;
        var a = res.data;
        var html = '<p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:12px;">Submit for: <strong>' + SkillShare.escapeHtml(a.title) + '</strong></p>' +
            '<div class="submission-area">' +
            '<label class="file-drop" for="subFile">' +
            '<input type="file" id="subFile" accept=".pdf,.doc,.docx,.txt,.zip">' +
            '<i class="fas fa-cloud-upload-alt" style="font-size:2rem;display:block;margin-bottom:8px;"></i>' +
            '<span id="fileName">Click or drag file here</span></label>' +
            '<div style="margin-top:12px;"><label class="form-label">Notes</label><textarea class="form-control" id="subNotes" rows="3" placeholder="Add notes..."></textarea></div>' +
            '<button class="btn btn-primary" style="margin-top:12px;" onclick="doSubmit(' + id + ')"><i class="fas fa-paper-plane"></i> Submit</button></div>';
        document.getElementById('modalTitle').textContent = 'Submit Assignment';
        document.getElementById('assignmentDetails').innerHTML = html;
        document.getElementById('assignmentModal').classList.add('active');
    });
}

function doSubmit(id) {
    var fd = new FormData();
    var fileInput = document.getElementById('subFile');
    if (fileInput.files[0]) fd.append('submission_file', fileInput.files[0]);
    fd.append('submission_text', document.getElementById('subNotes').value || '');
    fetch(BASE + 'api/assignments.php?action=submit&id=' + id, { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) { SkillShare.showToast('Success', 'Assignment submitted', 'success'); closeAssignmentModal(); SkillShare.apiFetch(BASE + 'api/assignments.php?action=list').then(function(r){ if(r.success) renderAssignments(r.data); }); }
            else { SkillShare.showToast('Error', res.message, 'error'); }
        });
}
function closeAssignmentModal() { document.getElementById('assignmentModal').classList.remove('active'); }

SkillShare.apiFetch(BASE + 'api/assignments.php?action=list').then(function(res) {
    if (res.success) renderAssignments(res.data);
});
</script>

<?php
endDashboardPage();
