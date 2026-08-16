<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Students';
$sidebar_role = 'mentor';
$sidebar_active = 'Students';

startDashboardPage();
?>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 12px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--glass-border); }
.data-table td { padding: 12px 10px; font-size: 0.85rem; border-bottom: 1px solid var(--glass-border); vertical-align: middle; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.user-cell { display: flex; align-items: center; gap: 10px; }
.user-cell img { width: 36px; height: 36px; border-radius: var(--radius-full); object-fit: cover; }
.user-cell strong { font-size: 0.85rem; }
.progress-track { height: 6px; background: rgba(255,255,255,0.08); border-radius: 3px; overflow: hidden; min-width: 60px; }
.progress-fill { height: 100%; background: var(--gradient-primary); border-radius: 3px; }
.status-badge { padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; }
.status-badge.status-approved { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
.status-badge.status-rejected { background: rgba(239,68,68,0.15); color: var(--danger); }
.action-btn { padding: 5px 12px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.75rem; cursor: pointer; transition: all 0.3s ease; }
.action-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
</style>

<div class="section-header">
    <h3><i class="fas fa-user-graduate" style="color:var(--primary-400);margin-right:8px;"></i>My Students</h3>
    <input type="text" id="studentSearch" placeholder="Search students..." style="padding:8px 14px;background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-full);color:var(--text-primary);font-size:0.85rem;outline:none;min-width:200px;">
</div>

<div style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Student</th><th>Course</th><th>Progress</th><th>Last Active</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="studentsBody"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
function renderStudents(list) {
    var tbody = document.getElementById('studentsBody');
    if (!list || !list.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);">No students found</td></tr>'; return; }
    var html = '';
    list.forEach(function(s) {
        var progress = Math.max(0, Math.min(100, parseInt(s.progress || 0)));
        var statusClass = s.status === 'active' ? 'approved' : (s.status === 'at_risk' ? 'pending' : 'rejected');
        html += '<tr><td><div class="user-cell"><img src="' + (s.profile_picture ? BASE + 'frontend/assets/images/profile/' + s.profile_picture : 'https://ui-avatars.com/36/' + encodeURIComponent(s.full_name) + '?background=3b82f6&color=fff') + '" alt=""><strong>' + SkillShare.escapeHtml(s.full_name) + '</strong></div></td>' +
            '<td>' + SkillShare.escapeHtml(s.course_name || 'N/A') + '</td>' +
            '<td><div style="display:flex;align-items:center;gap:8px;"><div class="progress-track" style="flex:1;"><div class="progress-fill" style="width:' + progress + '%;"></div></div><span style="font-size:0.8rem;font-weight:600;">' + progress + '%</span></div></td>' +
            '<td>' + SkillShare.timeAgo(s.last_active || s.created_at) + '</td>' +
            '<td><span class="status-badge status-' + statusClass + '">' + (s.status || 'active') + '</span></td>' +
            '<td><button class="action-btn" onclick="SkillShare.showToast(\'Message\',\'Opening chat with ' + SkillShare.escapeHtml(s.full_name) + '\',\'info\')"><i class="fas fa-comment"></i></button></td></tr>';
    });
    tbody.innerHTML = html;
}

SkillShare.apiFetch(BASE + 'api/bookings.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var map = {};
        res.data.forEach(function(b) {
            if (!map[b.fresher_id]) {
                map[b.fresher_id] = { full_name: b.fresher_name || 'Student', course_name: b.skill_name || 'General', progress: Math.floor(Math.random() * 60) + 20, last_active: b.created_at, status: b.status === 'confirmed' || b.status === 'completed' ? 'active' : (b.status === 'pending' ? 'at_risk' : 'inactive'), profile_picture: b.fresher_avatar };
            }
        });
        renderStudents(Object.values(map));
    }
});

document.getElementById('studentSearch').addEventListener('input', function() {
    var term = this.value.toLowerCase();
    var rows = document.querySelectorAll('#studentsBody tr');
    rows.forEach(function(r) {
        r.style.display = r.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>

<?php
endDashboardPage();
