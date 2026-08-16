<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Resources';
$sidebar_role = 'fresher';
$sidebar_active = 'Resources';

startDashboardPage();
?>

<style>
.resources-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
.resource-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 20px; transition: all 0.3s ease;
}
.resource-card:hover { transform: translateY(-4px); background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.2); }
.resource-icon { width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 12px; }
.resource-title { font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
.resource-meta { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 8px; }
.resource-desc { font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 12px; }
.resource-link { color: var(--primary-400); text-decoration: none; font-size: 0.8rem; font-weight: 500; }
.resource-link:hover { text-decoration: underline; }
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
    <h3 style="margin:0;"><i class="fas fa-book-open" style="color:var(--primary-400);margin-right:8px;"></i> Learning Resources</h3>
    <div style="display:flex;gap:10px;align-items:center;">
        <select class="filter-select" id="resourceFilter"><option value="">All Types</option><option value="article">Articles</option><option value="video">Videos</option><option value="document">Documents</option></select>
        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="resourceSearch" placeholder="Search resources..."></div>
    </div>
</div>

<div class="resources-grid" id="resourcesGrid"><p style="color:var(--text-muted);">Loading...</p></div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var sampleResources = [
    { id: 1, title: 'Introduction to Data Science', type: 'article', category: 'Data Science', description: 'A comprehensive guide to data science fundamentals for beginners.', views: 1240, created_at: '2025-01-10T08:00:00Z', file_path: '', author_name: 'Dr. Aisha Khan' },
    { id: 2, title: 'Machine Learning Basics', type: 'video', category: 'Machine Learning', description: 'Video tutorial covering supervised and unsupervised learning concepts.', views: 890, created_at: '2025-01-08T10:00:00Z', file_path: '', author_name: 'Prof. James Carter' },
    { id: 3, title: 'Web Development Handbook', type: 'document', category: 'Web Development', description: 'PDF handbook with HTML, CSS, and JavaScript best practices.', views: 2100, created_at: '2025-01-05T14:00:00Z', file_path: '', author_name: 'Robert Garcia' },
    { id: 4, title: 'Career Guidance Notes', type: 'document', category: 'Career', description: 'Notes on resume building, interview preparation, and career growth.', views: 670, created_at: '2025-01-02T09:00:00Z', file_path: '', author_name: 'Dr. Emily Chen' },
    { id: 5, title: 'Python for Data Analysis', type: 'article', category: 'Data Science', description: 'Learn pandas, numpy, and matplotlib for effective data analysis.', views: 1560, created_at: '2024-12-28T11:00:00Z', file_path: '', author_name: 'David Brown' },
    { id: 6, title: 'UI/UX Design Principles', type: 'video', category: 'Design', description: 'Video series on user interface and experience design fundamentals.', views: 980, created_at: '2024-12-25T16:00:00Z', file_path: '', author_name: 'Lisa Anderson' },
];

function renderResources(resources) {
    var grid = document.getElementById('resourcesGrid');
    if (!resources.length) { grid.innerHTML = '<div class="card empty" style="grid-column:1/-1;"><i class="fas fa-book-open"></i><p>No resources found</p></div>'; return; }
    var html = '';
    var iconMap = { article: 'fa-newspaper', video: 'fa-play-circle', document: 'fa-file-pdf' };
    var colorMap = { article: 'var(--primary-400)', video: 'var(--secondary-400)', document: 'var(--success)' };
    resources.forEach(function(r) {
        html += '<div class="resource-card">' +
            '<div class="resource-icon" style="background:' + (colorMap[r.type] || 'var(--primary-400)') + '20;color:' + (colorMap[r.type] || 'var(--primary-400)') + ';"><i class="fas ' + (iconMap[r.type] || 'fa-file') + '"></i></div>' +
            '<div class="resource-title">' + SkillShare.escapeHtml(r.title) + '</div>' +
            '<div class="resource-meta">' + SkillShare.escapeHtml(r.category || '') + ' • by ' + SkillShare.escapeHtml(r.author_name || 'Unknown') + '</div>' +
            '<div class="resource-desc">' + SkillShare.escapeHtml(r.description || '') + '</div>' +
            '<div style="display:flex;justify-content:space-between;align-items:center;">' +
                '<span style="font-size:0.7rem;color:var(--text-muted);"><i class="fas fa-eye"></i> ' + (r.views || 0) + '</span>' +
                (r.file_path ? '<a href="' + r.file_path + '" target="_blank" class="resource-link"><i class="fas fa-download"></i> Download</a>' : '<span style="font-size:0.7rem;color:var(--text-muted);">Online</span>') +
            '</div></div>';
    });
    grid.innerHTML = html;
}

SkillShare.apiFetch(BASE + 'api/research.php?action=list').then(function(res) {
    if (!res.success || !res.data.length) { renderResources(sampleResources); return; }
    var mapped = res.data.map(function(r) { return { id: r.id, title: r.title, type: r.type || 'article', category: r.field_name || r.category || 'General', description: r.description || '', views: r.views || 0, created_at: r.created_at, file_path: r.file_path || r.external_url || '', author_name: r.author_name || 'SkillShare Hub' }; });
    renderResources(mapped);
}).catch(function() { renderResources(sampleResources); });

document.getElementById('resourceSearch').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    var type = document.getElementById('resourceFilter').value;
    var all = window._allResources || sampleResources;
    var filtered = all.filter(function(r) {
        return (r.title.toLowerCase().includes(q) || r.description.toLowerCase().includes(q)) && (!type || r.type === type);
    });
    renderResources(filtered);
});

document.getElementById('resourceFilter').addEventListener('change', function() {
    var type = this.value;
    var all = window._allResources || sampleResources;
    var filtered = all.filter(function(r) { return !type || r.type === type; });
    renderResources(filtered);
});
</script>

<?php
endDashboardPage();
