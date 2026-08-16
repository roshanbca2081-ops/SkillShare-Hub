<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Resources';
$sidebar_role = 'admin';
$sidebar_active = 'Resources';

startDashboardPage();
?>
<style>
.resource-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="resource-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="rTotal">0</div><div class="stat-label">Resources</div></div><div class="stat-icon"><i class="fas fa-book-open"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="rDownloads">0</div><div class="stat-label">Downloads</div></div><div class="stat-icon"><i class="fas fa-download"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="rViews">0</div><div class="stat-label">Views</div></div><div class="stat-icon"><i class="fas fa-eye"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="rFeatured">0</div><div class="stat-label">Featured</div></div><div class="stat-icon"><i class="fas fa-star"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header">
        <h5><i class="fa-solid fa-book-open" style="color:var(--primary);"></i> Resources</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addResourceModal"><i class="fa-solid fa-plus"></i> Upload Resource</button>
    </div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Resource</th><th>Type</th><th>Field</th><th>Uploaded By</th><th>Downloads</th><th>Actions</th></tr></thead>
            <tbody id="resourcesTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addResourceModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:rgba(20,20,40,0.95);color:#fff;border:1px solid var(--glass-border);">
        <div class="modal-header" style="border-bottom:1px solid var(--glass-border);"><h5 class="modal-title">Upload Resource</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form id="addResourceForm">
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Title</label><input type="text" class="form-control" id="rrTitle" required></div>
                    <div class="form-group"><label class="form-label">Type</label><select class="form-control" id="rrType"><option>pdf</option><option>video</option><option>doc</option><option>link</option></select></div>
                    <div class="form-group"><label class="form-label">Field</label><input type="text" class="form-control" id="rrField"></div>
                    <div class="form-group"><label class="form-label">URL/File</label><input type="text" class="form-control" id="rrUrl" required></div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:12px;"><i class="fa-solid fa-upload"></i> Upload</button>
            </form>
        </div>
    </div></div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

SkillShare.apiFetch(BASE + 'api/resources.php').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('rTotal').textContent = data.length;
        document.getElementById('rDownloads').textContent = data.reduce(function(s,r){ return s + (r.downloads||0); }, 0);
        document.getElementById('rViews').textContent = data.reduce(function(s,r){ return s + (r.views||0); }, 0);
        document.getElementById('rFeatured').textContent = data.filter(function(r){ return r.featured; }).length;

        var html = '';
        data.forEach(function(r) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><strong>' + SkillShare.escapeHtml(r.title) + '</strong></td>' +
                '<td><span class="badge badge-primary">' + SkillShare.escapeHtml(r.type||'file') + '</span></td>' +
                '<td>' + SkillShare.escapeHtml(r.field_name||'General') + '</td>' +
                '<td>' + SkillShare.escapeHtml(r.uploaded_by||'Admin') + '</td>' +
                '<td>' + (r.downloads||0) + '</td>' +
                '<td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="delete-btn" onclick="deleteResource(' + r.id + ')" aria-label="Delete"><i class="fa-solid fa-trash"></i></button></div></td></tr>';
        });
        document.getElementById('resourcesTable').innerHTML = html || '<tr><td colspan="7" style="text-align:center;">No resources found</td></tr>';
    } else {
        document.getElementById('resourcesTable').innerHTML = '<tr><td colspan="7" style="text-align:center;">No resources found</td></tr>';
    }
});

document.getElementById('addResourceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    SkillShare.apiFetch(BASE + 'api/resources.php', {method:'POST', body:{title:document.getElementById('rrTitle').value, type:document.getElementById('rrType').value, field_name:document.getElementById('rrField').value, url:document.getElementById('rrUrl').value}}).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Resource uploaded', 'success'); location.reload(); }
        else SkillShare.showToast('Error', res.message, 'error');
    });
});

function deleteResource(id) {
    if (confirm('Delete this resource?')) {
        SkillShare.apiFetch(BASE + 'api/resources.php?id=' + id, {method:'DELETE'}).then(function(res) {
            if (res.success) { SkillShare.showToast('Deleted', 'Resource removed', 'success'); location.reload(); }
            else SkillShare.showToast('Error', res.message, 'error');
        });
    }
}
</script>
<?php
endDashboardPage();
?>
