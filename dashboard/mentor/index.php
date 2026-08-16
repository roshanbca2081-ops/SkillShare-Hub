<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Dashboard';
$sidebar_role = 'mentor';
$sidebar_active = 'Dashboard';

startDashboardPage();
?>

<style>
.stat-card { position: relative; overflow: hidden; }
.stat-card .stat-icon { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 2.5rem; opacity: 0.08; }
.empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); }
.empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 12px; }
.empty-state p { font-size: 0.9rem; }
.table-row-item { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--glass-border); }
.table-row-item:last-child { border-bottom: none; }
.table-row-item img { width: 40px; height: 40px; border-radius: var(--radius-full); object-fit: cover; }
.table-row-item .info { flex: 1; min-width: 0; }
.table-row-item .info strong { display: block; font-size: 0.9rem; color: var(--text-primary); }
.table-row-item .info span { font-size: 0.8rem; color: var(--text-muted); }
.status-badge { padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: capitalize; }
.status-badge.status-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
.status-badge.status-confirmed { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-cancelled { background: rgba(239,68,68,0.15); color: var(--danger); }
.status-badge.status-completed { background: rgba(59,130,246,0.15); color: var(--primary-400); }
.status-badge.status-scheduled { background: rgba(139,92,246,0.15); color: var(--secondary-400); }
.status-badge.status-draft { background: rgba(255,255,255,0.08); color: var(--text-muted); }
.status-badge.status-open { background: rgba(59,130,246,0.15); color: var(--primary-400); }
.status-badge.status-graded { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-reviewed { background: rgba(139,92,246,0.15); color: var(--secondary-400); }
.status-badge.status-issued { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-valid { background: rgba(34,197,94,0.15); color: var(--success); }
.action-btn { padding: 5px 12px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.75rem; cursor: pointer; transition: all 0.3s ease; }
.action-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
.action-btn.danger:hover { background: var(--danger); border-color: var(--danger); color: #fff; }
.quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 24px; }
.quick-action-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 16px 18px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; color: var(--text-secondary); display: flex; align-items: center; gap: 12px; }
.quick-action-card:hover { transform: translateY(-2px); background: rgba(255,255,255,0.06); border-color: var(--primary-400); color: var(--text-primary); }
.quick-action-card .qa-icon { width: 40px; height: 40px; border-radius: var(--radius-md); background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: var(--primary-400); }
</style>

<div class="stats-grid" id="mentorStats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statStudents">0</div><div class="stat-label">Total Students</div></div><div class="stat-icon"><i class="fas fa-user-graduate"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statEarnings">$0</div><div class="stat-label">Earnings</div></div><div class="stat-icon"><i class="fas fa-circle-dollar-to-point"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statPending">0</div><div class="stat-label">Pending Bookings</div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statSessions">0</div><div class="stat-label">Sessions</div></div><div class="stat-icon"><i class="fas fa-video"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statRating">0</div><div class="stat-label">Rating</div></div><div class="stat-icon"><i class="fas fa-star"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statReviews">0</div><div class="stat-label">Reviews</div></div><div class="stat-icon"><i class="fas fa-comments"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statMessages">0</div><div class="stat-label">Messages</div></div><div class="stat-icon"><i class="fas fa-envelope"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statNotifications">0</div><div class="stat-label">Notifications</div></div><div class="stat-icon"><i class="fas fa-bell"></i></div></div></div>
</div>

<div class="quick-actions">
    <a href="<?php echo BASE_URL; ?>dashboard/mentor/my-courses.php" class="quick-action-card"><div class="qa-icon"><i class="fas fa-plus"></i></div><div><strong>New Course</strong><br><small style="color:var(--text-muted);font-size:0.75rem;">Create a course</small></div></a>
    <a href="<?php echo BASE_URL; ?>dashboard/mentor/assignments.php" class="quick-action-card"><div class="qa-icon"><i class="fas fa-file-pen"></i></div><div><strong>New Assignment</strong><br><small style="color:var(--text-muted);font-size:0.75rem;">Add assignment</small></div></a>
    <a href="<?php echo BASE_URL; ?>dashboard/mentor/sessions.php" class="quick-action-card"><div class="qa-icon"><i class="fas fa-calendar-plus"></i></div><div><strong>Schedule Session</strong><br><small style="color:var(--text-muted);font-size:0.75rem;">Plan a session</small></div></a>
    <a href="<?php echo BASE_URL; ?>dashboard/mentor/resources.php" class="quick-action-card"><div class="qa-icon"><i class="fas fa-upload"></i></div><div><strong>Upload Resource</strong><br><small style="color:var(--text-muted);font-size:0.75rem;">Share material</small></div></a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div>
        <div class="section-header"><h3><i class="fas fa-calendar-check" style="color:var(--primary-400);margin-right:8px;"></i>Recent Bookings</h3><a href="<?php echo BASE_URL; ?>dashboard/mentor/bookings.php">View All</a></div>
        <div class="card" id="recentBookings"><p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p></div>
    </div>
    <div>
        <div class="section-header"><h3><i class="fas fa-clock" style="color:var(--secondary-400);margin-right:8px;"></i>Upcoming Sessions</h3><a href="<?php echo BASE_URL; ?>dashboard/mentor/sessions.php">View All</a></div>
        <div class="card" id="upcomingSessions"><p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
    <div>
        <div class="section-header"><h3><i class="fas fa-bell" style="color:var(--warning);margin-right:8px;"></i>Recent Notifications</h3><a href="<?php echo BASE_URL; ?>dashboard/mentor/notifications.php">View All</a></div>
        <div class="card" id="recentNotifications"><p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p></div>
    </div>
    <div>
        <div class="section-header"><h3><i class="fas fa-envelope" style="color:var(--success);margin-right:8px;"></i>Recent Messages</h3><a href="<?php echo BASE_URL; ?>dashboard/mentor/messages.php">View All</a></div>
        <div class="card" id="recentMessages"><p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p></div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

SkillShare.apiFetch(BASE + 'api/dashboard/index.php').then(function(res) {
    if (res.success && res.data) {
        var d = res.data;
        document.getElementById('statStudents').textContent = (d.students || 0);
        document.getElementById('statEarnings').textContent = SkillShare.formatCurrency(d.earnings || 0);
        document.getElementById('statPending').textContent = (d.pending || 0);
        document.getElementById('statSessions').textContent = (d.sessions || 0);
        document.getElementById('statRating').textContent = parseFloat(d.rating || 0).toFixed(1);
        document.getElementById('statReviews').textContent = (d.reviews || 0);
        document.getElementById('statMessages').textContent = (d.unread_messages || 0);
        document.getElementById('statNotifications').textContent = (d.unread_notifications || 0);
    }
});

SkillShare.apiFetch(BASE + 'api/bookings.php?action=list').then(function(res) {
    if (res.success) {
        var html = '';
        var recent = res.data.slice(0, 5);
        if (recent.length === 0) {
            html = '<div class="empty-state"><i class="fas fa-calendar-times"></i><p>No bookings yet</p></div>';
        } else {
            recent.forEach(function(b) {
                html += '<div class="table-row-item">' +
                    '<div class="info"><strong>' + SkillShare.escapeHtml(b.session_title) + '</strong>' +
                    '<span>' + SkillShare.escapeHtml(b.mentor_name || 'Student') + ' • ' + SkillShare.formatDate(b.session_date) + '</span></div>' +
                    '<span class="status-badge status-' + b.status + '">' + b.status + '</span></div>';
            });
        }
        document.getElementById('recentBookings').innerHTML = html;
    }
});

SkillShare.apiFetch(BASE + 'api/sessions.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var html = '';
        var upcoming = res.data.filter(function(s) { return ['scheduled'].includes(s.status); }).slice(0, 5);
        if (upcoming.length === 0) {
            html = '<div class="empty-state"><i class="fas fa-clock"></i><p>No upcoming sessions</p></div>';
        } else {
            upcoming.forEach(function(s) {
                html += '<div class="table-row-item">' +
                    '<div class="info"><strong>' + SkillShare.escapeHtml(s.session_title) + '</strong>' +
                    '<span>' + SkillShare.escapeHtml(s.fresher_name || '') + ' • ' + SkillShare.formatDate(s.session_date) + ' at ' + SkillShare.formatTime(s.session_time) + '</span></div>' +
                    '<span class="status-badge status-' + s.status + '">' + s.status + '</span></div>';
            });
        }
        document.getElementById('upcomingSessions').innerHTML = html;
    }
});

SkillShare.apiFetch(BASE + 'api/notifications.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var html = '';
        var notifs = res.data.slice(0, 5);
        if (notifs.length === 0) {
            html = '<div class="empty-state"><i class="fas fa-bell-slash"></i><p>No notifications</p></div>';
        } else {
            notifs.forEach(function(n) {
                html += '<div class="table-row-item">' +
                    '<div class="info"><strong>' + SkillShare.escapeHtml(n.title) + '</strong>' +
                    '<span>' + SkillShare.escapeHtml(n.message || '') + '</span></div>' +
                    '<small style="color:var(--text-muted);font-size:0.75rem;">' + SkillShare.timeAgo(n.created_at) + '</small></div>';
            });
        }
        document.getElementById('recentNotifications').innerHTML = html;
    }
});

SkillShare.apiFetch(BASE + 'api/messages.php?action=conversations').then(function(res) {
    if (res.success && res.data) {
        var html = '';
        var convs = res.data.slice(0, 5);
        if (convs.length === 0) {
            html = '<div class="empty-state"><i class="fas fa-envelope-open"></i><p>No messages</p></div>';
        } else {
            convs.forEach(function(c) {
                html += '<div class="table-row-item" style="cursor:pointer;">' +
                    '<img src="' + (c.avatar ? BASE + 'frontend/assets/images/profile/' + c.avatar : 'https://ui-avatars.com/40/' + encodeURIComponent(c.name) + '?background=3b82f6&color=fff') + '" alt="">' +
                    '<div class="info"><strong>' + SkillShare.escapeHtml(c.name) + '</strong>' +
                    '<span>' + SkillShare.escapeHtml(c.last_message) + '</span></div>' +
                    '<small style="color:var(--text-muted);font-size:0.75rem;">' + SkillShare.timeAgo(c.last_time) + '</small></div>';
            });
        }
        document.getElementById('recentMessages').innerHTML = html;
    }
});
</script>

<?php
endDashboardPage();
