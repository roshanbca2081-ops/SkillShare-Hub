<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Research';
$sidebar_role = 'admin';
$sidebar_active = 'Research';

startDashboardPage();
?>
<style>
.research-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="research-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="rTotal">0</div><div class="stat-label">Total Projects</div></div><div class="stat-icon"><i class="fas fa-flask"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="rProgress">0</div><div class="stat-label">In Progress</div></div><div class="stat-icon"><i class="fas fa-spinner"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="rReview">0</div><div class="stat-label">Review</div></div><div class="stat-icon"><i class="fas fa-magnifying-glass"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="rDraft">0</div><div class="stat-label">Draft</div></div><div class="stat-icon"><i class="fas fa-file"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-flask" style="color:var(--primary);"></i> Research Projects</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Project</th><th>Researcher</th><th>Field</th><th>Progress</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="researchTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='review'?'approved':(s==='draft'?'rejected':'pending'); }

SkillShare.apiFetch(BASE + 'api/research.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('rTotal').textContent = data.length;
        document.getElementById('rProgress').textContent = data.filter(function(r){ return r.status==='in_progress'; }).length;
        document.getElementById('rReview').textContent = data.filter(function(r){ return r.status==='review'; }).length;
        document.getElementById('rDraft').textContent = data.filter(function(r){ return r.status==='draft'; }).length;

        var html = '';
        data.forEach(function(p) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><strong>' + SkillShare.escapeHtml(p.title||p.project_name) + '</strong></td>' +
                '<td>' + SkillShare.escapeHtml(p.researcher_name||p.user_name||'Researcher') + '</td>' +
                '<td><span class="badge badge-primary">' + SkillShare.escapeHtml(p.field_name||'General') + '</span></td>' +
                '<td><div style="display:flex;align-items:center;gap:0.6rem;"><div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:' + (p.progress||0) + '%;"></div></div><span style="font-size:.8rem;font-weight:600;">' + (p.progress||0) + '%</span></div></td>' +
                '<td><span class="status-pill ' + getStatusClass(p.status) + '">' + p.status + '</span></td>' +
                '<td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button></div></td></tr>';
        });
        document.getElementById('researchTable').innerHTML = html || '<tr><td colspan="7" style="text-align:center;">No projects found</td></tr>';
    }
});
</script>
<?php
endDashboardPage();
?>
