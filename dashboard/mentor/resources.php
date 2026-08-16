<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Resources';
$sidebar_role = 'mentor';
$sidebar_active = 'Resources';

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
.modal { background: #1a1a2e; border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 24px; max-width: 520px; width: 100%; max-height: 90vh; overflow-y: auto; }
.modal h3 { margin-bottom: 16px; }
.form-group { margin-bottom: 12px; }
.form-group label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px; }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: var(--radius-md); color: var(--text-primary); outline: none; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; }
</style>

<div class="section-header">
    <h3><i class="fas fa-book-open" style="color:var(--primary-400);margin-right:8px;"></i>Learning Resources</h3>
    <button class="btn-primary" onclick="openCreate()"><i class="fas fa-plus"></i> Upload Resource</button>
</div>

<div style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Resource</th><th>Type</th><th>Field</th><th>Downloads</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="resourcesBody"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="createModal">
    <div class="modal">
        <h3>Upload Resource</h3>
        <form id="createForm">
            <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
            <div class="form-group"><label>Type</label><select name="type"><option>PDF</option><option>Video</option><option>Slide</option><option>Code</option></select></div>
            <div class="form-group"><label>Category / Field</label><input type="text" name="category"></div>
            <div class="form-group"><label>External URL (optional)</label><input type="url" name="external_url"></div>
            <div class="modal-actions">
                <button type="button" class="btn-sm" onclick="document.getElementById('createModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn-primary">Upload</button>
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
        if (res.success) { SkillShare.showToast('Success', 'Resource uploaded', 'success'); document.getElementById('createModal').classList.remove('open'); loadResources(); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
});

function loadResources() {
    SkillShare.apiFetch(BASE + 'api/research.php?action=list').then(function(res) {
        var tbody = document.getElementById('resourcesBody');
        if (res.success && res.data && res.data.length) {
            var html = '';
            res.data.forEach(function(r) {
                html += '<tr><td><strong>' + SkillShare.escapeHtml(r.title) + '</strong></td>' +
                    '<td><span class="status-badge status-pending">' + SkillShare.escapeHtml(r.type || 'Document') + '</span></td>' +
                    '<td>' + SkillShare.escapeHtml(r.field_name || 'General') + '</td>' +
                    '<td>' + (r.downloads || 0) + '</td>' +
                    '<td><span class="status-badge status-' + (r.status === 'approved' ? 'approved' : (r.status === 'draft' ? 'rejected' : 'pending')) + '">' + r.status + '</span></td>' +
                    '<td><button class="btn-sm" onclick="SkillShare.showToast(\'Download\',\'Downloading resource...\',\'info\')"><i class="fas fa-download"></i></button></td></tr>';
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);">No resources found</td></tr>';
        }
    });
}
loadResources();
</script>

<?php
endDashboardPage();
