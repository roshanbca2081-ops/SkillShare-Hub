<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Interview';
$sidebar_role = 'admin';
$sidebar_active = 'Interview';

startDashboardPage();
?>
<style>
.interview-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="interview-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="iTotal">0</div><div class="stat-label">Questions</div></div><div class="stat-icon"><i class="fas fa-comments"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="iEasy">0</div><div class="stat-label">Easy</div></div><div class="stat-icon"><i class="fas fa-face-smile"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="iMedium">0</div><div class="stat-label">Medium</div></div><div class="stat-icon"><i class="fas fa-face-meh"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="iHard">0</div><div class="stat-label">Hard</div></div><div class="stat-icon"><i class="fas fa-face-frown"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-comments" style="color:var(--primary);"></i> Interview Questions</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Question</th><th>Field</th><th>Difficulty</th><th>Added By</th><th>Actions</th></tr></thead>
            <tbody id="interviewTable"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getDiffClass(d) { return d==='easy'?'approved':(d==='medium'?'pending':'rejected'); }

SkillShare.apiFetch(BASE + 'api/interview.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('iTotal').textContent = data.length;
        document.getElementById('iEasy').textContent = data.filter(function(q){ return q.difficulty==='easy'; }).length;
        document.getElementById('iMedium').textContent = data.filter(function(q){ return q.difficulty==='medium'; }).length;
        document.getElementById('iHard').textContent = data.filter(function(q){ return q.difficulty==='hard'; }).length;

        var html = '';
        data.forEach(function(q) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><strong>' + SkillShare.escapeHtml(q.question) + '</strong></td>' +
                '<td>' + SkillShare.escapeHtml(q.field_name||'General') + '</td>' +
                '<td><span class="status-pill ' + getDiffClass(q.difficulty) + '">' + q.difficulty + '</span></td>' +
                '<td>' + SkillShare.escapeHtml(q.added_by||'Admin') + '</td>' +
                '<td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button><button class="delete-btn" onclick="deleteQuestion(' + q.id + ')" aria-label="Delete"><i class="fa-solid fa-trash"></i></button></div></td></tr>';
        });
        document.getElementById('interviewTable').innerHTML = html || '<tr><td colspan="6" style="text-align:center;">No questions found</td></tr>';
    }
});

function deleteQuestion(id) {
    if (confirm('Delete this question?')) {
        SkillShare.apiFetch(BASE + 'api/interview.php?id=' + id, {method:'DELETE'}).then(function(res) {
            if (res.success) { SkillShare.showToast('Deleted', 'Question removed', 'success'); location.reload(); }
            else SkillShare.showToast('Error', res.message, 'error');
        });
    }
}
</script>
<?php
endDashboardPage();
?>
