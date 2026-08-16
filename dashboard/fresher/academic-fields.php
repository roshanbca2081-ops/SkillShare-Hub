<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Academic Fields';
$sidebar_role = 'fresher';
$sidebar_active = 'Academic Fields';

startDashboardPage();
?>

<style>
.fields-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;
}
.field-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 20px; text-align: center;
    cursor: pointer; transition: all 0.3s ease;
}
.field-card:hover { transform: translateY(-4px); background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.2); }
.field-icon { font-size: 2rem; margin-bottom: 10px; }
.field-name { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }
.field-desc { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
.field-count { font-size: 0.7rem; color: var(--primary-400); margin-top: 8px; }
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.search-box {
    display: flex; align-items: center; background: var(--glass-bg);
    border: 1px solid var(--glass-border); border-radius: var(--radius-full);
    padding: 8px 16px; min-width: 260px;
}
.search-box input { background: transparent; border: none; padding: 4px 8px; color: var(--text-primary); font-size: 0.85rem; outline: none; flex: 1; }
.search-box input::placeholder { color: var(--text-muted); }
.search-box i { color: var(--text-muted); margin-right: 8px; }
</style>

<div class="toolbar">
    <h3 style="margin:0;"><i class="fas fa-layer-group" style="color:var(--primary-400);margin-right:8px;"></i> Academic Fields</h3>
    <div class="search-box"><i class="fas fa-search"></i><input type="text" id="fieldSearch" placeholder="Search fields..."></div>
</div>

<div class="fields-grid" id="fieldsGrid">
    <p style="color:var(--text-muted);">Loading...</p>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

SkillShare.apiFetch(BASE + 'api/fields.php?action=list').then(function(res) {
    if (res.success) renderFields(res.data);
});

document.getElementById('fieldSearch').addEventListener('input', function() {
    SkillShare.apiFetch(BASE + 'api/fields.php?action=list').then(function(res) {
        if (res.success) {
            var q = this.value.toLowerCase();
            var filtered = res.data.filter(function(f) {
                return f.name.toLowerCase().includes(q) || (f.description && f.description.toLowerCase().includes(q));
            }.bind(this));
            renderFields(filtered);
        }
    }.bind(this));
});

function renderFields(fields) {
    var grid = document.getElementById('fieldsGrid');
    if (!fields.length) { grid.innerHTML = '<div class="card empty" style="grid-column:1/-1;"><i class="fas fa-book"></i><p>No fields found</p></div>'; return; }
    var html = '';
    fields.forEach(function(f) {
        html += '<div class="field-card" onclick="window.location.href=\'' + BASE + 'dashboard/fresher/courses.php?field_id=' + f.id + '\'">' +
            '<div class="field-icon"><i class="fas fa-' + (f.icon || 'book') + '" style="color:' + (f.color || 'var(--primary-400)') + '"></i></div>' +
            '<div class="field-name">' + SkillShare.escapeHtml(f.name) + '</div>' +
            '<div class="field-desc">' + SkillShare.escapeHtml(f.description || '') + '</div>' +
            '<div class="field-count">' + (f.total_courses || 0) + ' courses</div></div>';
    });
    grid.innerHTML = html;
}
</script>

<?php
endDashboardPage();
