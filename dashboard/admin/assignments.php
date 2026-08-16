<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Assignments';
$sidebar_role = 'admin';
$sidebar_active = 'Assignments';

startDashboardPage();
?>
<style>
.assign-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="assign-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="aTotal">0</div><div class="stat-label">Total</div></div><div class="stat-icon"><i class="fas fa-file-pen"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="aOpen">0</div><div class="stat-label">Open</div></div><div class="stat-icon"><i class="fas fa-folder-open"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="aClosed">0</div><div class="stat-label">Closed</div></div><div class="stat-icon"><i class="fas fa-folder"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="aGraded">0</div><div class="stat-label">Graded</div></div><div class="stat-icon"><i class="fas fa-check-double"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-file-pen" style="color:var(--primary);"></i> All Assignments</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Assignment</th><th>Course</th><th>Due Date</th><th>Submissions</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="assignTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='open'?'pending':(s==='graded'?'approved':'rejected'); }

SkillShare.apiFetch(BASE + 'api/assignments.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('aTotal').textContent = data.length;
        document.getElementById('aOpen').textContent = data.filter(function(a){ return a.status==='open'; }).length;
        document.getElementById('aClosed').textContent = data.filter(function(a){ return a.status==='closed'; }).length;
        document.getElementById('aGraded').textContent = data.filter(function(a){ return a.status==='graded'; }).length;

        var html = '';
        data.forEach(function(a) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><strong>' + SkillShare.escapeHtml(a.title) + '</strong></td>' +
                '<td>' + SkillShare.escapeHtml(a.course_name||'General') + '</td>' +
                '<td>' + SkillShare.formatDate(a.deadline||a.due_date) + '</td>' +
                '<td>' + (a.submission_count||0) + '</td>' +
                '<td><span class="status-pill ' + getStatusClass(a.status) + '">' + a.status + '</span></td>' +
                '<td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button></div></td></tr>';
        });
        document.getElementById('assignTable').innerHTML = html || '<tr><td colspan="7" style="text-align:center;">No assignments found</td></tr>';
    }
});
</script>
<?php
endDashboardPage();
?>
