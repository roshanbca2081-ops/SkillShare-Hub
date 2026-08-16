<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Mentors';
$sidebar_role = 'fresher';
$sidebar_active = 'Mentors';

startDashboardPage();
?>

<style>
.mentors-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
.mentor-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 20px; text-align: center; transition: all 0.3s ease;
}
.mentor-card:hover { transform: translateY(-4px); background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.2); }
.mentor-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid var(--glass-border); margin-bottom: 10px; }
.mentor-name { font-size: 0.95rem; font-weight: 600; color: var(--text-primary); }
.mentor-spec { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
.mentor-meta { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; margin: 12px 0; }
.mentor-rating { color: var(--warning); font-size: 0.75rem; }
.mentor-rate { color: var(--primary-400); font-weight: 600; font-size: 0.85rem; }
.mentor-btn {
    width: 100%; padding: 8px 16px; border-radius: var(--radius-full); border: 1px solid var(--glass-border);
    background: transparent; color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;
}
.mentor-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
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
    <h3 style="margin:0;"><i class="fas fa-chalkboard-user" style="color:var(--primary-400);margin-right:8px;"></i> Mentors</h3>
    <div style="display:flex;gap:10px;align-items:center;">
        <select class="filter-select" id="fieldFilter"><option value="">All Fields</option></select>
        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="mentorSearch" placeholder="Search mentors..."></div>
    </div>
</div>

<div class="mentors-grid" id="mentorsGrid"><p style="color:var(--text-muted);">Loading...</p></div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderMentors(mentors) {
    var grid = document.getElementById('mentorsGrid');
    if (!mentors.length) { grid.innerHTML = '<div class="card empty" style="grid-column:1/-1;"><i class="fas fa-user-tie"></i><p>No mentors found</p></div>'; return; }
    var html = '';
    mentors.forEach(function(m) {
        html += '<div class="mentor-card">' +
            '<img src="' + (m.profile_picture ? BASE + 'frontend/assets/images/profile/' + m.profile_picture : 'https://ui-avatars.com/80/?background=3b82f6&color=fff&name=' + encodeURIComponent(m.full_name)) + '" alt="" class="mentor-avatar">' +
            '<div class="mentor-name">' + SkillShare.escapeHtml(m.full_name) + '</div>' +
            '<div class="mentor-spec">' + SkillShare.escapeHtml(m.specialization || m.field_name || 'Mentor') + '</div>' +
            '<div class="mentor-meta">' +
                '<span class="mentor-rating"><i class="fas fa-star"></i> ' + parseFloat(m.rating || 0).toFixed(1) + '</span>' +
                '<span style="font-size:0.75rem;color:var(--text-muted);">' + (m.total_students || 0) + ' students</span>' +
            '</div>' +
            '<div class="mentor-rate">' + SkillShare.formatCurrency(m.hourly_rate) + '/hr</div>' +
            '<button class="mentor-btn" style="margin-top:12px;" onclick="bookMentor(' + m.id + ')"><i class="fas fa-calendar-check"></i> Book Session</button></div>';
    });
    grid.innerHTML = html;
}

function bookMentor(mentorId) {
    window.location.href = BASE + 'dashboard/fresher/bookings.php?mentor_id=' + mentorId;
}

SkillShare.apiFetch(BASE + 'api/mentors.php?action=list').then(function(res) {
    if (res.success) {
        renderMentors(res.data);
        var fields = res.fields || [];
        var sel = document.getElementById('fieldFilter');
        fields.forEach(function(f) {
            var opt = document.createElement('option');
            opt.value = f.id; opt.textContent = f.name;
            sel.appendChild(opt);
        });
    }
});

document.getElementById('fieldFilter').addEventListener('change', function() {
    var params = '?action=list';
    if (this.value) params += '&field_id=' + this.value;
    SkillShare.apiFetch(BASE + 'api/mentors.php' + params).then(function(res) {
        if (res.success) renderMentors(res.data);
    });
});

document.getElementById('mentorSearch').addEventListener('input', function() {
    var params = '?action=list&search=' + encodeURIComponent(this.value);
    SkillShare.apiFetch(BASE + 'api/mentors.php' + params).then(function(res) {
        if (res.success) renderMentors(res.data);
    });
});
</script>

<?php
endDashboardPage();
