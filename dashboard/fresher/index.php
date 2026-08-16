<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../_layout.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Dashboard';
$sidebar_role = 'fresher';
$sidebar_active = 'Dashboard';

startDashboardPage();
?>

<style>
.welcome-banner {
    background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(139,92,246,0.15));
    border: 1px solid rgba(59,130,246,0.2);
    border-radius: var(--radius-lg); padding: 16px 20px; margin-bottom: 20px;
}
.welcome-banner h3 { color: var(--text-primary); font-size: 1.1rem; }
.welcome-banner p { color: var(--text-muted); font-size: 0.85rem; margin-top: 4px; }
.profile-completion { margin-top: 12px; }
.completion-bar {
    height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;
}
.completion-fill { height: 100%; background: var(--gradient-primary); border-radius: 3px; transition: width 0.5s ease; }
.card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 16px; margin-bottom: 16px;
}
.card h4 { color: var(--text-primary); font-size: 0.9rem; margin-bottom: 12px; }
.card.empty { text-align: center; padding: 24px 12px; }
.card.empty i { font-size: 2rem; color: var(--text-muted); opacity: 0.3; }
.card.empty p { color: var(--text-muted); font-size: 0.8rem; margin-top: 8px; }
</style>

<div class="welcome-banner">
    <h3><i class="fas fa-rocket"></i> Ready to continue your learning journey?</h3>
    <p>Track your progress, discover new courses, and connect with mentors.</p>
    <div class="profile-completion">
        <div style="display:flex;justify-content:space-between;font-size:0.8rem;">
            <span style="color:var(--text-secondary);">Profile completion</span>
            <span id="completionPct">0%</span>
        </div>
        <div class="completion-bar"><div class="completion-fill" id="completionBar" style="width:0%"></div></div>
    </div>
</div>

<div class="stats-grid" id="fresherStats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statCourses">0</div><div class="stat-label">Enrolled Courses</div></div><div class="stat-icon"><i class="fas fa-graduation-cap"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statCompleted">0</div><div class="stat-label">Completed</div></div><div class="stat-icon"><i class="fas fa-trophy"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statSessions">0</div><div class="stat-label">My Sessions</div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statCerts">0</div><div class="stat-label">Certificates</div></div><div class="stat-icon"><i class="fas fa-certificate"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statBookings">0</div><div class="stat-label">Upcoming Bookings</div></div><div class="stat-icon"><i class="fas fa-calendar-check"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statAssign">0</div><div class="stat-label">Pending Assignments</div></div><div class="stat-icon"><i class="fas fa-file-pen"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statMsgs">0</div><div class="stat-label">Unread Messages</div></div><div class="stat-icon"><i class="fas fa-envelope"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="statNotif">0</div><div class="stat-label">Notifications</div></div><div class="stat-icon"><i class="fas fa-bell"></i></div></div></div>
</div>

<div style="display:grid;grid-templateColumns:1fr 1fr;gap:16px;">
    <div>
        <div class="section-header"><h3><i class="fas fa-calendar-check"></i> Upcoming Bookings</h3><a href="<?php echo BASE_URL; ?>dashboard/fresher/bookings.php">View All</a></div>
        <div class="card" id="upcomingBookings">
            <p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p>
        </div>
    </div>
    <div>
        <div class="section-header"><h3><i class="fas fa-clock"></i> Upcoming Sessions</h3><a href="<?php echo BASE_URL; ?>dashboard/fresher/sessions.php">View All</a></div>
        <div class="card" id="upcomingSessions">
            <p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p>
        </div>
    </div>
</div>

<div style="display:grid;grid-templateColumns:1fr 1fr;gap:16px;">
    <div>
        <div class="section-header"><h3><i class="fas fa-file-pen"></i> Pending Assignments</h3><a href="<?php echo BASE_URL; ?>dashboard/fresher/assignments.php">View All</a></div>
        <div class="card" id="pendingAssignments">
            <p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p>
        </div>
    </div>
    <div>
        <div class="section-header"><h3><i class="fas fa-certificate"></i> My Certificates</h3><a href="<?php echo BASE_URL; ?>dashboard/fresher/certificates.php">View All</a></div>
        <div class="card" id="myCertificates">
            <p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p>
        </div>
    </div>
</div>

<div class="section-header"><h3><i class="fas fa-bell"></i> Recent Notifications</h3></div>
<div class="card" id="recentNotifications">
    <p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var userId = <?php echo getUserId(); ?>;

SkillShare.apiFetch(BASE + 'api/dashboard/index.php').then(function(res) {
    if (res.success && res.data) {
        var d = res.data;
        document.getElementById('statCourses').textContent = d.enrolled_courses || 0;
        document.getElementById('statCompleted').textContent = d.completed_courses || 0;
        document.getElementById('statSessions').textContent = d.total_sessions || 0;
        document.getElementById('statCerts').textContent = d.certificates || 0;
        document.getElementById('statBookings').textContent = d.upcoming_bookings || 0;
        document.getElementById('statAssign').textContent = d.pending_assignments || 0;
        document.getElementById('statMsgs').textContent = d.unread_messages || 0;
        document.getElementById('statNotif').textContent = d.unread_notifications || 0;

        var total = Object.values(d).reduce(function(a,b){ return a + (parseInt(b)||0); }, 0);
        var pct = Math.min(100, Math.round((total / 8) * 100));
        document.getElementById('completionPct').textContent = pct + '%';
        document.getElementById('completionBar').style.width = pct + '%';
    }
});

SkillShare.apiFetch(BASE + 'api/bookings.php?action=list').then(function(res) {
    if (res.success) {
        var html = '';
        var upcoming = res.data.filter(function(b) {
            return ['pending','confirmed'].includes(b.status);
        }).slice(0, 5);
        if (upcoming.length === 0) {
            html = '<div class="empty"><i class="fas fa-calendar-times"></i><p>No upcoming bookings</p></div>';
        } else {
            upcoming.forEach(function(b) {
                html += '<div class="table-row" style="display:flex;flex-direction:column;align-items:flex-start;">' +
                    '<div style="width:100%"><strong>' + SkillShare.escapeHtml(b.session_title) + '</strong> ' +
                    '<span class="status-badge status-' + b.status + '">' + b.status + '</span></div>' +
                    '<div style="font-size:0.8rem;color:var(--text-muted)">' +
                    SkillShare.formatDate(b.session_date) + ' • ' + SkillShare.formatTime(b.session_time) +
                    ' • with ' + SkillShare.escapeHtml(b.mentor_name) + '</div></div>';
            });
        }
        document.getElementById('upcomingBookings').innerHTML = html;
    }
});

SkillShare.apiFetch(BASE + 'api/sessions.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var html = '';
        var upcoming = res.data.filter(function(s) {
            return ['scheduled'].includes(s.status) && new Date(s.session_date) >= new Date();
        }).slice(0, 5);
        if (upcoming.length === 0) {
            html = '<div class="empty"><i class="fas fa-clock"></i><p>No upcoming sessions</p></div>';
        } else {
            upcoming.forEach(function(s) {
                html += '<div class="table-row" style="display:flex;flex-direction:column;align-items:flex-start;">' +
                    '<div style="width:100%"><strong>' + SkillShare.escapeHtml(s.session_title) + '</strong> ' +
                    '<span class="status-badge status-' + s.status + '">' + s.status + '</span></div>' +
                    '<div style="font-size:0.8rem;color:var(--text-muted)">' +
                    SkillShare.formatDate(s.session_date) + ' • ' + SkillShare.formatTime(s.session_time) +
                    ' • Mentor: ' + SkillShare.escapeHtml(s.mentor_name || '') + '</div></div>';
            });
        }
        document.getElementById('upcomingSessions').innerHTML = html;
    }
});

SkillShare.apiFetch(BASE + 'api/assignments.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var html = '';
        if (res.data.length === 0) {
            html = '<div class="empty"><i class="fas fa-file-pen"></i><p>No pending assignments</p></div>';
        } else {
            res.data.slice(0, 5).forEach(function(a) {
                html += '<div class="table-row">' +
                    '<div class="col-1"><strong>' + SkillShare.escapeHtml(a.title) + '</strong></div>' +
                    '<div class="col-2" style="color:var(--text-muted);font-size:0.8rem">' + SkillShare.formatDate(a.deadline) + '</div>' +
                    '<div class="col-3">' + (a.submission_id ? '<span class="status-badge status-completed">Submitted</span>' : '<span class="status-badge status-pending">Pending</span>') + '</div>' +
                    '<div class="col-4"><a href="' + BASE + 'dashboard/fresher/assignments.php?id=' + a.id + '" class="course-btn" style="padding:4px 12px;font-size:0.7rem">View</a></div>' +
                    '</div>';
            });
        }
        document.getElementById('pendingAssignments').innerHTML = html;
    }
});

SkillShare.apiFetch(BASE + 'api/certificates.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var html = '';
        var certs = res.data.slice(0, 5);
        if (certs.length === 0) {
            html = '<div class="empty"><i class="fas fa-certificate"></i><p>No certificates yet</p></div>';
        } else {
            certs.forEach(function(c) {
                html += '<div class="table-row" style="display:flex;align-items:center;gap:12px;">' +
                    '<div style="width:40px;height:40px;background:rgba(59,130,246,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;">' +
                    '<i class="fas fa-certificate" style="color:#f59e0b;font-size:1.2rem"></i></div>' +
                    '<div><strong>' + SkillShare.escapeHtml(c.course_name || 'Course Certificate') + '</strong>' +
                    '<div style="font-size:0.75rem;color:var(--text-muted)">Issued: ' + SkillShare.formatDate(c.issue_date) + '</div></div>' +
                    '<span class="status-badge status-completed">' + c.certificate_number + '</span></div>';
            });
        }
        document.getElementById('myCertificates').innerHTML = html;
    }
});

SkillShare.apiFetch(BASE + 'api/notifications.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var html = '';
        var notifs = res.data.slice(0, 10);
        if (notifs.length === 0) {
            html = '<div class="empty"><i class="fas fa-bell-slash"></i><p>No notifications</p></div>';
        } else {
            notifs.forEach(function(n) {
                html += '<div class="table-row" style="display:flex;align-items:center;gap:12px;">' +
                    '<div style="width:32px;height:32px;background:rgba(59,130,246,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;">' +
                    '<i class="fas fa-' + (n.icon || 'info-circle') + '" style="color:var(--primary-400)"></i></div>' +
                    '<div><strong>' + SkillShare.escapeHtml(n.title) + '</strong>' +
                    '<div style="font-size:0.75rem;color:var(--text-muted)">' + SkillShare.escapeHtml(n.message || '') + '</div></div>' +
                    '<div style="margin-left:auto;font-size:0.7rem;color:var(--text-muted)">' + SkillShare.timeAgo(n.created_at) + '</div></div>';
            });
        }
        document.getElementById('recentNotifications').innerHTML = html;
    }
});
</script>

<?php
endDashboardPage();
