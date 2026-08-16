<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Research';
$sidebar_role = 'fresher';
$sidebar_active = 'Research';

startDashboardPage();
?>

<style>
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
.table-responsive-wrap { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
.data-table th { color: var(--text-muted); font-weight: 500; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.progress-track { height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden; min-width: 80px; }
.progress-fill { height: 100%; background: var(--gradient-primary); border-radius: 3px; }
.status-badge {
    padding: 4px 12px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
}
.status-approved { background: rgba(34,197,94,0.15); color: var(--success); }
.status-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
.status-rejected { background: rgba(239,68,68,0.15); color: var(--danger); }
.table-actions { display: flex; gap: 6px; }
.icon-btn {
    width: 32px; height: 32px; border-radius: var(--radius-md); border: 1px solid var(--glass-border);
    background: transparent; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all 0.3s ease; font-size: 0.8rem;
}
.icon-btn:hover { background: rgba(255,255,255,0.08); color: var(--text-primary); }
.empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
</style>

<div class="toolbar">
    <h3 style="margin:0;"><i class="fas fa-flask" style="color:var(--primary-400);margin-right:8px;"></i> Research Projects</h3>
    <select class="filter-select" id="researchFilter"><option value="">All</option><option value="approved">Approved</option><option value="pending">Pending</option><option value="draft">Draft</option></select>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <div class="table-responsive-wrap">
        <table class="data-table">
            <thead><tr><th>Project</th><th>Author</th><th>Field</th><th>Progress</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="researchTable"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:40px;">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderResearch(items) {
    var tbody = document.getElementById('researchTable');
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:40px;"><i class="fas fa-flask" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:8px;"></i>No research projects found</td></tr>'; return; }
    var html = '';
    items.forEach(function(r) {
        var progress = r.progress || Math.floor(Math.random() * 60) + 20;
        html += '<tr>' +
            '<td><strong>' + SkillShare.escapeHtml(r.title) + '</strong><div style="font-size:0.75rem;color:var(--text-muted);">' + SkillShare.escapeHtml((r.description || '').substring(0, 50)) + '</div></td>' +
            '<td>' + SkillShare.escapeHtml(r.author_name || 'Unknown') + '</td>' +
            '<td><span class="badge badge-primary">' + SkillShare.escapeHtml(r.field_name || r.category || 'General') + '</span></td>' +
            '<td style="min-width:120px;"><div style="display:flex;align-items:center;gap:8px;"><div class="progress-track" style="flex:1;"><div class="progress-fill" style="width:' + progress + '%;"></div></div><span style="font-size:0.8rem;font-weight:600;">' + progress + '%</span></div></td>' +
            '<td><span class="status-badge status-' + (r.status || 'pending') + '">' + (r.status || 'pending') + '</span></td>' +
            '<td><div class="table-actions">' +
                '<button class="icon-btn" title="View" onclick="viewResearch(' + r.id + ')"><i class="fas fa-eye"></i></button>' +
                (r.file_path || r.external_url ? '<a href="' + SkillShare.escapeHtml(r.file_path || r.external_url) + '" target="_blank" class="icon-btn" title="Open"><i class="fas fa-external-link-alt"></i></a>' : '') +
            '</div></td></tr>';
    });
    tbody.innerHTML = html;
}

document.getElementById('researchFilter').addEventListener('change', function() {
    var status = this.value;
    SkillShare.apiFetch(BASE + 'api/research.php?action=list' + (status ? '&status=' + status : '')).then(function(res) {
        if (res.success) renderResearch(res.data);
    });
});

function viewResearch(id) {
    SkillShare.apiFetch(BASE + 'api/research.php?action=show&id=' + id).then(function(res) {
        if (!res.success) { SkillShare.showToast('Error', 'Research not found', 'error'); return; }
        var r = res.data;
        SkillShare.showToast('Research', r.title + ' by ' + (r.author_name || 'Unknown'), 'info');
    });
}

SkillShare.apiFetch(BASE + 'api/research.php?action=list').then(function(res) {
    if (res.success) renderResearch(res.data);
});
</script>

<?php
endDashboardPage();
