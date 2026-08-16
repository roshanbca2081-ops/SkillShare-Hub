<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Research';
$sidebar_role = 'mentor';
$sidebar_active = 'Research';

startDashboardPage();
?>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 12px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--glass-border); }
.data-table td { padding: 12px 10px; font-size: 0.85rem; border-bottom: 1px solid var(--glass-border); vertical-align: middle; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.status-badge { padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; }
.status-badge.status-approved { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
.status-badge.status-rejected { background: rgba(239,68,68,0.15); color: var(--danger); }
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
    <h3><i class="fas fa-flask" style="color:var(--primary-400);margin-right:8px;"></i>Research Projects</h3>
    <button class="btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i> New Project</button>
</div>

<div style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Project</th><th>Type</th><th>Field</th><th>Progress</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="researchBody"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="createModal">
    <div class="modal">
        <h3>Create Research Project</h3>
        <form id="createForm">
            <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            <div class="form-group"><label>Type</label><select name="type"><option>Paper</option><option>Thesis</option><option>Case Study</option><option>Survey</option></select></div>
            <div class="form-group"><label>Category</label><input type="text" name="category"></div>
            <div class="modal-actions">
                <button type="button" class="btn-sm" onclick="document.getElementById('createModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn-primary">Create</button>
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
    SkillShare.apiFetch(BASE + 'api/research.php?action=create', { method: 'POST', body: formData }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Project created', 'success'); document.getElementById('createModal').classList.remove('open'); loadResearch(); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
});

function loadResearch() {
    SkillShare.apiFetch(BASE + 'api/research.php?action=list').then(function(res) {
        var tbody = document.getElementById('researchBody');
        if (res.success && res.data && res.data.length) {
            var html = '';
            res.data.forEach(function(r) {
                var progress = Math.min(100, Math.max(0, parseInt(r.views || 0) + parseInt(r.downloads || 0) + parseInt(r.likes || 0)));
                html += '<tr><td><strong>' + SkillShare.escapeHtml(r.title) + '</strong></td>' +
                    '<td>' + SkillShare.escapeHtml(r.type || 'N/A') + '</td>' +
                    '<td><span class="status-badge status-pending">' + SkillShare.escapeHtml(r.field_name || 'General') + '</span></td>' +
                    '<td><div class="progress-track" style="width:100px;"><div class="progress-fill" style="width:' + progress + '%;"></div></div><small>' + progress + '%</small></td>' +
                    '<td><span class="status-badge status-' + (r.status === 'approved' ? 'approved' : (r.status === 'draft' ? 'rejected' : 'pending')) + '">' + r.status + '</span></td>' +
                    '<td><div style="display:flex;gap:6px;"><button class="btn-sm" onclick="SkillShare.showToast(\'View\',\'Opening project details\',\'info\')"><i class="fas fa-eye"></i></button><button class="btn-sm" onclick="SkillShare.showToast(\'Edit\',\'Edit mode coming soon\',\'info\')"><i class="fas fa-pen"></i></button></div></td></tr>';
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);">No research projects found</td></tr>';
        }
    });
}
loadResearch();
</script>

<?php
endDashboardPage();
