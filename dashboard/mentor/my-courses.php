<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'My Courses';
$sidebar_role = 'mentor';
$sidebar_active = 'My Courses';

startDashboardPage();
?>

<style>
.course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
.course-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 18px; transition: all 0.3s ease; }
.course-card:hover { transform: translateY(-4px); background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.2); }
.course-card .top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
.course-card .icon { width: 42px; height: 42px; border-radius: var(--radius-md); background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: var(--primary-400); }
.course-card .badge { font-size: 0.65rem; padding: 2px 10px; border-radius: var(--radius-full); background: rgba(59,130,246,0.12); color: var(--primary-400); }
.course-card h4 { font-size: 0.95rem; margin-bottom: 4px; }
.course-card p { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 8px; }
.course-card .meta { display: flex; gap: 12px; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px; flex-wrap: wrap; }
.course-card .meta span { display: flex; align-items: center; gap: 4px; }
.course-card .actions { display: flex; gap: 8px; }
.course-card .actions button { flex: 1; padding: 6px 12px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.75rem; cursor: pointer; transition: all 0.3s ease; }
.course-card .actions button:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
.empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
.empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 12px; }
</style>

<div class="section-header">
    <h3><i class="fas fa-book-open" style="color:var(--primary-400);margin-right:8px;"></i>My Courses</h3>
    <button class="btn-primary" onclick="SkillShare.showToast('Coming Soon','Course creation wizard coming soon','info')"><i class="fas fa-plus"></i> Create Course</button>
</div>

<div class="course-grid" id="courseGrid">
    <div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Loading courses...</p></div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
SkillShare.apiFetch(BASE + 'api/courses.php?action=list').then(function(res) {
    var grid = document.getElementById('courseGrid');
    if (res.success && res.data && res.data.length) {
        var html = '';
        res.data.forEach(function(c) {
            html += '<div class="course-card">' +
                '<div class="top"><div class="icon"><i class="fas fa-book"></i></div><span class="badge">' + SkillShare.escapeHtml(c.field_name || 'General') + '</span></div>' +
                '<h4>' + SkillShare.escapeHtml(c.name) + '</h4>' +
                '<p>' + SkillShare.escapeHtml((c.description || '').substring(0, 80)) + (c.description && c.description.length > 80 ? '...' : '') + '</p>' +
                '<div class="meta"><span><i class="far fa-clock"></i> ' + SkillShare.escapeHtml(c.duration || 'N/A') + '</span>' +
                '<span><i class="fas fa-users"></i> ' + (c.total_students || 0) + '</span>' +
                '<span><i class="fas fa-star"></i> ' + parseFloat(c.rating || 0).toFixed(1) + '</span></div>' +
                '<div class="actions"><button onclick="SkillShare.showToast(\'Course Details\',\'Opening ' + SkillShare.escapeHtml(c.name) + '\',\'info\')">View</button><button onclick="SkillShare.showToast(\'Edit\',\'Edit mode coming soon\',\'info\')">Edit</button></div>' +
                '</div>';
        });
        grid.innerHTML = html;
    } else {
        grid.innerHTML = '<div class="empty-state"><i class="fas fa-book-open"></i><p>No courses found</p></div>';
    }
});
</script>

<?php
endDashboardPage();
