<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Courses';
$sidebar_role = 'admin';
$sidebar_active = 'Courses';

startDashboardPage();
?>
<style>
.course-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="course-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="cTotal">0</div><div class="stat-label">Total Courses</div></div><div class="stat-icon"><i class="fas fa-book-open"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="cPublished">0</div><div class="stat-label">Published</div></div><div class="stat-icon"><i class="fas fa-circle-check"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="cDraft">0</div><div class="stat-label">Draft</div></div><div class="stat-icon"><i class="fas fa-pen-clip"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="cArchived">0</div><div class="stat-label">Archived</div></div><div class="stat-icon"><i class="fas fa-eye-slash"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header">
        <h5><i class="fa-solid fa-book-open" style="color:var(--primary);"></i> All Courses</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal"><i class="fa-solid fa-plus"></i> Add Course</button>
    </div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Course</th><th>Instructor</th><th>Category</th><th>Price</th><th>Students</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="coursesTable"><tr><td colspan="8" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:rgba(20,20,40,0.95);color:#fff;border:1px solid var(--glass-border);">
        <div class="modal-header" style="border-bottom:1px solid var(--glass-border);"><h5 class="modal-title">Add Course</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form id="addCourseForm">
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Course Name</label><input type="text" class="form-control" id="acName" required></div>
                    <div class="form-group"><label class="form-label">Category</label><input type="text" class="form-control" id="acCat" required></div>
                    <div class="form-group"><label class="form-label">Price</label><input type="number" step="0.01" class="form-control" id="acPrice" required></div>
                    <div class="form-group"><label class="form-label">Status</label><select class="form-control" id="acStatus"><option>published</option><option>draft</option><option>archived</option></select></div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:12px;"><i class="fa-solid fa-plus"></i> Add Course</button>
            </form>
        </div>
    </div></div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='published'?'approved':(s==='draft'?'pending':'rejected'); }

SkillShare.apiFetch(BASE + 'api/courses.php').then(function(res) {
    if (res.success && res.data) {
        var total = res.data.length;
        var published = res.data.filter(function(c){ return c.status==='published'; }).length;
        var draft = res.data.filter(function(c){ return c.status==='draft'; }).length;
        var archived = res.data.filter(function(c){ return c.status==='archived'; }).length;
        document.getElementById('cTotal').textContent = total;
        document.getElementById('cPublished').textContent = published;
        document.getElementById('cDraft').textContent = draft;
        document.getElementById('cArchived').textContent = archived;

        var html = '';
        res.data.forEach(function(c) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><div class="user-cell"><img src="../../assets/images/course/' + (c.image||'default.png') + '" alt=""><strong>' + SkillShare.escapeHtml(c.name) + '</strong></div></td>' +
                '<td>' + SkillShare.escapeHtml(c.instructor||'N/A') + '</td>' +
                '<td><span class="badge badge-primary">' + SkillShare.escapeHtml(c.category||'General') + '</span></td>' +
                '<td><strong>$' + Number(c.price||0).toFixed(2) + '</strong></td>' +
                '<td>' + (c.student_count||0) + '</td>' +
                '<td><span class="status-pill ' + getStatusClass(c.status) + '">' + c.status + '</span></td>' +
                '<td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button><button class="delete-btn" onclick="deleteCourse(' + c.id + ')" aria-label="Delete"><i class="fa-solid fa-trash"></i></button></div></td></tr>';
        });
        document.getElementById('coursesTable').innerHTML = html || '<tr><td colspan="8" style="text-align:center;">No courses found</td></tr>';
    } else {
        document.getElementById('coursesTable').innerHTML = '<tr><td colspan="8" style="text-align:center;">No courses found</td></tr>';
    }
});

document.getElementById('addCourseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    SkillShare.apiFetch(BASE + 'api/courses.php', {method:'POST', body:{name:document.getElementById('acName').value, category:document.getElementById('acCat').value, price:document.getElementById('acPrice').value, status:document.getElementById('acStatus').value}}).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Course added', 'success'); location.reload(); }
        else SkillShare.showToast('Error', res.message, 'error');
    });
});

function deleteCourse(id) {
    if (confirm('Delete this course?')) {
        SkillShare.apiFetch(BASE + 'api/courses.php?id=' + id, {method:'DELETE'}).then(function(res) {
            if (res.success) { SkillShare.showToast('Deleted', 'Course removed', 'success'); location.reload(); }
            else SkillShare.showToast('Error', res.message, 'error');
        });
    }
}
</script>
<?php
endDashboardPage();
?>
