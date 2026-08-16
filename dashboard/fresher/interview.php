<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Interview';
$sidebar_role = 'fresher';
$sidebar_active = 'Interview';

startDashboardPage();
?>

<style>
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.table-responsive-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
.data-table th { color: var(--text-muted); font-weight: 500; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.difficulty-badge { padding: 4px 12px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
.difficulty-easy { background: rgba(34,197,94,0.15); color: var(--success); }
.difficulty-medium { background: rgba(245,158,11,0.15); color: var(--warning); }
.difficulty-hard { background: rgba(239,68,68,0.15); color: var(--danger); }
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
    padding: 24px; max-width: 640px; width: 100%; max-height: 90vh; overflow-y: auto;
}
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.modal-close { background: none; border: none; color: var(--text-muted); font-size: 1.2rem; cursor: pointer; }
.answer-box { background: var(--glass-bg); border-radius: var(--radius-md); padding: 16px; margin-top: 12px; font-size: 0.85rem; color: var(--text-secondary); }
.tips-box { background: rgba(245,158,11,0.05); border-left: 3px solid var(--warning); border-radius: var(--radius-md); padding: 12px; margin-top: 12px; font-size: 0.8rem; color: var(--text-secondary); }
</style>

<div class="toolbar">
    <h3 style="margin:0;"><i class="fas fa-comments" style="color:var(--primary-400);margin-right:8px;"></i> Interview Questions</h3>
    <div style="display:flex;gap:10px;align-items:center;">
        <select class="filter-select" id="categoryFilter"><option value="">All Categories</option></select>
        <select class="filter-select" id="difficultyFilter"><option value="">All Levels</option><option value="easy">Easy</option><option value="medium">Medium</option><option value="hard">Hard</option></select>
    </div>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-responsive-wrap">
        <table class="data-table">
            <thead><tr><th>Question</th><th>Category</th><th>Difficulty</th><th>Company</th><th>Actions</th></tr></thead>
            <tbody id="interviewTable"><tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:40px;">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="interviewModal">
    <div class="modal-box">
        <div class="modal-header"><h4 style="margin:0;">Practice Mode</h4><button class="modal-close" onclick="closeInterviewModal()">&times;</button></div>
        <div id="interviewDetails"></div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderInterviews(questions) {
    var tbody = document.getElementById('interviewTable');
    if (!questions.length) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:40px;"><i class="fas fa-comments" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:8px;"></i>No questions found</td></tr>'; return; }
    var html = '';
    questions.forEach(function(q) {
        html += '<tr>' +
            '<td><strong>' + SkillShare.escapeHtml(q.question) + '</strong></td>' +
            '<td><span class="badge badge-primary">' + SkillShare.escapeHtml(q.category) + '</span></td>' +
            '<td><span class="difficulty-badge difficulty-' + q.difficulty + '">' + q.difficulty + '</span></td>' +
            '<td>' + SkillShare.escapeHtml(q.company || '-') + '</td>' +
            '<td><div class="table-actions"><button class="icon-btn" title="Practice" onclick="practiceInterview(' + q.id + ')"><i class="fas fa-play"></i></button></div></td></tr>';
    });
    tbody.innerHTML = html;
}

SkillShare.apiFetch(BASE + 'api/interview.php?action=list').then(function(res) {
    if (res.success) {
        renderInterviews(res.data);
        var cats = {};
        res.data.forEach(function(q) { cats[q.category] = true; });
        var sel = document.getElementById('categoryFilter');
        Object.keys(cats).sort().forEach(function(c) {
            var opt = document.createElement('option');
            opt.value = c; opt.textContent = c;
            sel.appendChild(opt);
        });
    }
});

document.getElementById('categoryFilter').addEventListener('change', loadInterviews);
document.getElementById('difficultyFilter').addEventListener('change', loadInterviews);

function loadInterviews() {
    var cat = document.getElementById('categoryFilter').value;
    var diff = document.getElementById('difficultyFilter').value;
    var params = '?action=list';
    if (cat) params += '&category=' + encodeURIComponent(cat);
    if (diff) params += '&difficulty=' + diff;
    SkillShare.apiFetch(BASE + 'api/interview.php' + params).then(function(res) {
        if (res.success) renderInterviews(res.data);
    });
}

function practiceInterview(id) {
    SkillShare.apiFetch(BASE + 'api/interview.php?action=practice&id=' + id).then(function(res) {
        if (!res.success) { SkillShare.showToast('Error', 'Question not found', 'error'); return; }
        var q = res.data;
        var html = '<div style="margin-bottom:12px;"><span class="badge badge-primary">' + SkillShare.escapeHtml(q.category) + '</span> <span class="difficulty-badge difficulty-' + q.difficulty + '">' + q.difficulty + '</span></div>' +
            '<h5 style="color:var(--text-primary);">' + SkillShare.escapeHtml(q.question) + '</h5>' +
            '<div class="answer-box"><strong>Answer:</strong><p style="margin-top:6px;">' + SkillShare.escapeHtml(q.answer || 'No answer provided.') + '</p></div>' +
            (q.tips ? '<div class="tips-box"><strong><i class="fas fa-lightbulb"></i> Tips:</strong> ' + SkillShare.escapeHtml(q.tips) + '</div>' : '');
        document.getElementById('interviewDetails').innerHTML = html;
        document.getElementById('interviewModal').classList.add('active');
    });
}
function closeInterviewModal() { document.getElementById('interviewModal').classList.remove('active'); }
</script>

<?php
endDashboardPage();
