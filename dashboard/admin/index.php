<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Dashboard';
$sidebar_role = 'admin';
$sidebar_active = 'Dashboard';

startDashboardPage();
?>
<style>
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px; }
.learning-banner { background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(139,92,246,0.15)); border: 1px solid rgba(59,130,246,0.2); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.learning-banner h3 { font-family: var(--font-heading); font-weight: 700; font-size: 1.2rem; color: var(--text-primary); }
.learning-banner p { color: var(--text-muted); font-size: 0.85rem; margin-top: 4px; }
.learning-banner .btn-view { display: inline-block; margin-top: 10px; padding: 8px 24px; border-radius: var(--radius-full); background: var(--gradient-primary); color: #fff; text-decoration: none; font-weight: 500; font-size: 0.85rem; border: none; cursor: pointer; }
.learning-banner .btn-view:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(59,130,246,0.3); }
.people-float { display: flex; gap: 8px; align-items: center; }
.person-float { display: flex; flex-direction: column; align-items: center; animation: floatP 3s ease-in-out infinite; }
.person-float:nth-child(1) { animation-delay: 0s; }
.person-float:nth-child(2) { animation-delay: 0.8s; }
.person-float:nth-child(3) { animation-delay: 1.6s; }
.person-float:nth-child(4) { animation-delay: 2.4s; }
@keyframes floatP { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
.person-float .p-avatar { width: 44px; height: 44px; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; border: 2px solid var(--glass-border); }
.person-float .p-label { font-size: 0.65rem; color: var(--text-muted); margin-top: 2px; }
</style>

<div class="stat-grid" id="adminStats"></div>

<div class="learning-banner">
    <div>
        <h3>People are learning right now</h3>
        <p>Join 1,200+ students building their skills</p>
        <a href="courses.php" class="btn-view"><i class="fas fa-arrow-right"></i> View Courses</a>
    </div>
    <div class="people-float">
        <div class="person-float"><div class="p-avatar" style="background:#8b5cf6;">S</div><span class="p-label">Learning</span></div>
        <div class="person-float"><div class="p-avatar" style="background:#3b82f6;">J</div><span class="p-label">Coding</span></div>
        <div class="person-float"><div class="p-avatar" style="background:#22c55e;">M</div><span class="p-label">Design</span></div>
        <div class="person-float"><div class="p-avatar" style="background:#ec4899;">R</div><span class="p-label">Research</span></div>
        <div class="person-float"><div class="p-avatar" style="background:#f59e0b;">K</div><span class="p-label">Mentoring</span></div>
    </div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-users" style="color:var(--primary);"></i> Recent Users</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th></tr></thead>
            <tbody id="recentUsers"></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var iconMap = {users:'fa-users',courses:'fa-graduation-cap',mentors:'fa-user-tie',freshers:'fa-user-graduate',fields:'fa-layer-group',bookings:'fa-calendar-check',sessions:'fa-clock',assignments:'fa-file-pen',research:'fa-flask',payments:'fa-credit-card',certificates:'fa-award',revenue:'fa-circle-dollar'};

function renderStats(container, stats) {
    var items = [
        {label:'Total Users',value: stats.users ?? 0, icon:'users'},
        {label:'Active Courses',value: stats.courses ?? 0, icon:'courses'},
        {label:'Mentors',value: stats.mentors ?? 0, icon:'mentors'},
        {label:'Revenue',value: '$' + (stats.revenue ? Number(stats.revenue).toLocaleString() : '0'), icon:'revenue'},
        {label:'Bookings',value: stats.bookings ?? 0, icon:'bookings'},
        {label:'Sessions',value: stats.sessions ?? 0, icon:'sessions'},
        {label:'Assignments',value: stats.assignments ?? 0, icon:'assignments'},
        {label:'Certificates',value: stats.certificates ?? 0, icon:'certificates'},
        {label:'Fields',value: stats.fields ?? 0, icon:'fields'},
        {label:'Research',value: stats.research ?? 0, icon:'research'},
        {label:'Pending Bookings',value: stats.pending_bookings ?? 0, icon:'bookings'},
        {label:'Freshers',value: stats.freshers ?? 0, icon:'freshers'},
    ];
    var html = '';
    items.forEach(function(it) {
        html += '<div class="stat-card"><div class="stat-top"><div><div class="stat-number">' + it.value + '</div><div class="stat-label">' + it.label + '</div></div><div class="stat-icon"><i class="fas ' + (iconMap[it.icon]||'fa-circle') + '"></i></div></div></div>';
    });
    document.getElementById('adminStats').innerHTML = html;
}

SkillShare.apiFetch(BASE + 'api/dashboard/index.php').then(function(res) {
    if (res.success && res.data) renderStats(null, res.data);
});

SkillShare.apiFetch(BASE + 'api/admin.php?action=stats').then(function(res) {
    if (res.success && res.data && res.data.recent_users) {
        var html = '';
        res.data.recent_users.forEach(function(u) {
            var badge = u.role === 'mentor' ? 'secondary' : (u.role === 'admin' ? 'danger' : 'primary');
            var pill = u.status === 'active' ? 'approved' : (u.status === 'pending' ? 'pending' : 'rejected');
            html += '<tr><td><div class="user-cell"><img src="../../assets/images/profile/' + (u.profile_picture||'default.png') + '" alt=""><strong>' + SkillShare.escapeHtml(u.full_name) + '</strong></div></td><td>' + SkillShare.escapeHtml(u.email) + '</td><td><span class="badge badge-' + badge + '">' + u.role + '</span></td><td><span class="status-pill ' + pill + '">' + u.status + '</span></td><td>' + SkillShare.escapeHtml(u.created_at) + '</td></tr>';
        });
        document.getElementById('recentUsers').innerHTML = html || '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">No users found</td></tr>';
    }
});
</script>
<?php
endDashboardPage();
?>
