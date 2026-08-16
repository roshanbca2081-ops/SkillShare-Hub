<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Academic Fields';
$sidebar_role = 'admin';
$sidebar_active = 'Academic Fields';

startDashboardPage();
?>
<style>
.field-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
</style>

<div class="panel reveal">
    <div class="panel-header">
        <h5><i class="fa-solid fa-layer-group" style="color:var(--primary);"></i> Academic Fields</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFieldModal"><i class="fa-solid fa-plus"></i> Add Field</button>
    </div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Field</th><th>Icon</th><th>Courses</th><th>Mentors</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="fieldsTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addFieldModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:rgba(20,20,40,0.95);color:#fff;border:1px solid var(--glass-border);">
        <div class="modal-header" style="border-bottom:1px solid var(--glass-border);"><h5 class="modal-title">Add Field</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form id="addFieldForm">
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Field Name</label><input type="text" class="form-control" id="afName" required></div>
                    <div class="form-group"><label class="form-label">Icon Class</label><input type="text" class="form-control" id="afIcon" value="fa-book" placeholder="fa-book"></div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:12px;"><i class="fa-solid fa-plus"></i> Add Field</button>
            </form>
        </div>
    </div></div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='active'?'approved':(s==='inactive'?'rejected':'pending'); }

SkillShare.apiFetch(BASE + 'api/fields.php').then(function(res) {
    if (res.success && res.data) {
        var html = '';
        res.data.forEach(function(f) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><strong>' + SkillShare.escapeHtml(f.name) + '</strong></td>' +
                '<td><span class="badge badge-primary"><i class="fa-solid ' + SkillShare.escapeHtml(f.icon||'fa-book') + '"></i></span></td>' +
                '<td>' + (f.course_count || 0) + '</td>' +
                '<td>' + (f.mentor_count || 0) + '</td>' +
                '<td><span class="status-pill ' + getStatusClass(f.status) + '">' + f.status + '</span></td>' +
                '<td><div class="table-actions"><button class="edit-btn" onclick="editField(' + f.id + ')" aria-label="Edit"><i class="fa-solid fa-pen"></i></button><button class="delete-btn" onclick="deleteField(' + f.id + ')" aria-label="Delete"><i class="fa-solid fa-trash"></i></button></div></td></tr>';
        });
        document.getElementById('fieldsTable').innerHTML = html || '<tr><td colspan="7" style="text-align:center;">No fields found</td></tr>';
    } else {
        document.getElementById('fieldsTable').innerHTML = '<tr><td colspan="7" style="text-align:center;">No fields found</td></tr>';
    }
});

document.getElementById('addFieldForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var name = document.getElementById('afName').value;
    var icon = document.getElementById('afIcon').value;
    SkillShare.apiFetch(BASE + 'api/fields.php', {method:'POST', body:{name:name, icon:icon}}).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Field added', 'success'); location.reload(); }
        else SkillShare.showToast('Error', res.message, 'error');
    });
});

function editField(id) { SkillShare.showToast('Edit', 'Edit field ' + id, 'info'); }
function deleteField(id) {
    if (confirm('Delete this field?')) {
        SkillShare.apiFetch(BASE + 'api/fields.php?id=' + id, {method:'DELETE'}).then(function(res) {
            if (res.success) { SkillShare.showToast('Deleted', 'Field removed', 'success'); location.reload(); }
            else SkillShare.showToast('Error', res.message, 'error');
        });
    }
}
</script>
<?php
endDashboardPage();
?>
