<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Courses';
$sidebar_role = 'fresher';
$sidebar_active = 'Courses';

startDashboardPage();
?>

<style>
.courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
.course-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 16px; transition: all 0.3s ease;
}
.course-card:hover { transform: translateY(-4px); background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.2); }
.course-thumb {
    height: 140px; border-radius: var(--radius-md); display: flex;
    align-items: center; justify-content: center; font-size: 2rem; color: #fff;
    margin-bottom: 12px; background: var(--gradient-primary);
}
.course-title { font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
.course-mentor { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 8px; }
.course-meta { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
.course-meta span { font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
.course-rating { color: var(--warning); font-size: 0.75rem; }
.course-price { font-weight: 700; color: var(--primary-400); font-size: 0.9rem; }
.course-btn {
    width: 100%; padding: 8px 16px; border-radius: var(--radius-full); border: 1px solid var(--glass-border);
    background: transparent; color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;
}
.course-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
.course-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.search-box {
    display: flex; align-items: center; background: var(--glass-bg);
    border: 1px solid var(--glass-border); border-radius: var(--radius-full);
    padding: 8px 16px; min-width: 260px;
}
.search-box input { background: transparent; border: none; padding: 4px 8px; color: var(--text-primary); font-size: 0.85rem; outline: none; flex: 1; }
.search-box input::placeholder { color: var(--text-muted); }
.search-box i { color: var(--text-muted); margin-right: 8px; }
.filter-select {
    background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-full);
    padding: 8px 16px; color: var(--text-secondary); font-size: 0.85rem; outline: none;
}
</style>

<div class="toolbar">
    <h3 style="margin:0;"><i class="fas fa-book-open" style="color:var(--primary-400);margin-right:8px;"></i> Courses</h3>
    <div style="display:flex;gap:10px;align-items:center;">
        <select class="filter-select" id="fieldFilter"><option value="">All Fields</option></select>
        <select class="filter-select" id="levelFilter"><option value="">All Levels</option><option>Beginner</option><option>Intermediate</option><option>Advanced</option><option>Expert</option></select>
        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="courseSearch" placeholder="Search courses..."></div>
    </div>
</div>

<div class="courses-grid" id="coursesGrid"><p style="color:var(--text-muted);">Loading...</p></div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var allFields = [];
var allCourses = [];

function renderCourses(courses) {
    var grid = document.getElementById('coursesGrid');
    if (!courses.length) { grid.innerHTML = '<div class="card empty" style="grid-column:1/-1;"><i class="fas fa-book"></i><p>No courses found</p></div>'; return; }
    var html = '';
    courses.forEach(function(c) {
        html += '<div class="course-card">' +
            '<div class="course-thumb"><i class="fas fa-play-circle"></i></div>' +
            '<div class="course-title">' + SkillShare.escapeHtml(c.name) + '</div>' +
            '<div class="course-mentor">' + SkillShare.escapeHtml(c.field_name || 'General') + '</div>' +
            '<div class="course-meta">' +
                '<span><i class="fas fa-star"></i> ' + parseFloat(c.rating || 0).toFixed(1) + '</span>' +
                '<span><i class="fas fa-users"></i> ' + (c.total_students || 0) + '</span>' +
                '<span><i class="fas fa-clock"></i> ' + SkillShare.escapeHtml(c.duration || '') + '</span>' +
                '<span class="course-rating">' + SkillShare.escapeHtml(c.level || '') + '</span>' +
            '</div>' +
            '<div style="display:flex;justify-content:space-between;align-items:center;">' +
                '<span class="course-price">Free</span>' +
                '<button class="course-btn" onclick="enrollCourse(' + c.id + ', this)">Enroll Now</button>' +
            '</div></div>';
    });
    grid.innerHTML = html;
}

function enrollCourse(id, btn) {
    btn.disabled = true; btn.textContent = 'Enrolling...';
    SkillShare.apiFetch(BASE + 'api/courses.php?action=enroll', {
        method: 'POST',
        body: JSON.stringify({ course_id: id })
    }).then(function(res) {
        if (res.success) { btn.textContent = 'Enrolled'; btn.classList.add('enrolled'); SkillShare.showToast('Success', res.message, 'success'); }
        else { btn.disabled = false; btn.textContent = 'Enroll Now'; SkillShare.showToast('Error', res.message, 'error'); }
    });
}

SkillShare.apiFetch(BASE + 'api/fields.php?action=list').then(function(res) {
    if (res.success) {
        allFields = res.data;
        var sel = document.getElementById('fieldFilter');
        allFields.forEach(function(f) {
            var opt = document.createElement('option');
            opt.value = f.id; opt.textContent = f.name;
            sel.appendChild(opt);
        });
    }
});

function loadCourses() {
    var params = '?action=list';
    var fieldId = document.getElementById('fieldFilter').value;
    var search = document.getElementById('courseSearch').value;
    var level = document.getElementById('levelFilter').value;
    if (fieldId) params += '&field_id=' + fieldId;
    if (search) params += '&search=' + encodeURIComponent(search);
    if (level) params += '&level=' + encodeURIComponent(level);

    SkillShare.apiFetch(BASE + 'api/courses.php' + params).then(function(res) {
        if (res.success) { allCourses = res.data; renderCourses(allCourses); }
    });
}

document.getElementById('fieldFilter').addEventListener('change', loadCourses);
document.getElementById('levelFilter').addEventListener('change', loadCourses);
document.getElementById('courseSearch').addEventListener('input', loadCourses);

loadCourses();
</script>

<?php
endDashboardPage();
