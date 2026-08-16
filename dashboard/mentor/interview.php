<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Interview Prep';
$sidebar_role = 'mentor';
$sidebar_active = 'Interview';

startDashboardPage();
?>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 12px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--glass-border); }
.data-table td { padding: 12px 10px; font-size: 0.85rem; border-bottom: 1px solid var(--glass-border); vertical-align: middle; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.status-badge { padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; }
.status-badge.status-easy { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-medium { background: rgba(245,158,11,0.15); color: var(--warning); }
.status-badge.status-hard { background: rgba(239,68,68,0.15); color: var(--danger); }
.btn-sm { padding: 5px 12px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.75rem; cursor: pointer; transition: all 0.3s ease; }
.btn-sm:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
.btn-primary { padding: 8px 16px; border-radius: var(--radius-full); background: var(--gradient-primary); border: none; color: #fff; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.8rem; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px; }
.modal-overlay.open { display: flex; }
.modal { background: #1a1a2e; border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 24px; max-width: 560px; width: 100%; max-height: 90vh; overflow-y: auto; }
.modal h3 { margin-bottom: 16px; }
.form-group { margin-bottom: 12px; }
.form-group label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px; }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: var(--radius-md); color: var(--text-primary); outline: none; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; }
</style>

<div class="section-header">
    <h3><i class="fas fa-comments" style="color:var(--primary-400);margin-right:8px;"></i>Interview Questions</h3>
    <button class="btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i> Add Question</button>
</div>

<div style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Question</th><th>Category</th><th>Difficulty</th><th>Views</th><th>Actions</th></tr></thead>
            <tbody id="interviewBody"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="createModal">
    <div class="modal">
        <h3>Add Interview Question</h3>
        <form id="createForm">
            <div class="form-group"><label>Question</label><textarea name="question" rows="3" required></textarea></div>
            <div class="form-group"><label>Category</label><input type="text" name="category" required></div>
            <div class="form-group"><label>Sub Category</label><input type="text" name="sub_category"></div>
            <div class="form-group"><label>Difficulty</label><select name="difficulty"><option>easy</option><option>medium</option><option>hard</option></select></div>
            <div class="form-group"><label>Company (optional)</label><input type="text" name="company"></div>
            <div class="form-group"><label>Answer</label><textarea name="answer" rows="3"></textarea></div>
            <div class="form-group"><label>Tips</label><textarea name="tips" rows="2"></textarea></div>
            <div class="modal-actions">
                <button type="button" class="btn-sm" onclick="document.getElementById('createModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn-primary">Add Question</button>
            </div>
        </form>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
function openCreate() { document.getElementById('createModal').classList.add('open'); }

document.getElementById('createForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    SkillShare.apiFetch(BASE + 'api/interview.php?action=create', { method: 'POST', body: formData }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Question added', 'success'); document.getElementById('createModal').classList.remove('open'); loadInterview(); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
});

function loadInterview() {
    SkillShare.apiFetch(BASE + 'api/interview.php?action=list').then(function(res) {
        var tbody = document.getElementById('interviewBody');
        if (res.success && res.data && res.data.length) {
            var html = '';
            res.data.forEach(function(q) {
                html += '<tr><td><strong>' + SkillShare.escapeHtml(q.question) + '</strong></td>' +
                    '<td>' + SkillShare.escapeHtml(q.category) + '</td>' +
                    '<td><span class="status-badge status-' + q.difficulty + '">' + q.difficulty + '</span></td>' +
                    '<td>' + (q.views || 0) + '</td>' +
                    '<td><div style="display:flex;gap:6px;"><button class="btn-sm" onclick="SkillShare.showToast(\'Question\',\'' + SkillShare.escapeHtml(q.question).replace(/'/g, "\\'") + '\',\'info\',5000)"><i class="fas fa-eye"></i></button></div></td></tr>';
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No questions found</td></tr>';
        }
    });
}
loadInterview();
</script>

<?php
endDashboardPage();
