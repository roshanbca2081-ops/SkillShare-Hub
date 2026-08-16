<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Certificates';
$sidebar_role = 'fresher';
$sidebar_active = 'Certificates';

startDashboardPage();
?>

<style>
.cert-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
.cert-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 24px; text-align: center; transition: all 0.3s ease;
}
.cert-card:hover { transform: translateY(-4px); background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.2); }
.cert-icon { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 12px; background: var(--gradient-primary); color: #fff; }
.cert-title { font-size: 1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
.cert-meta { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 8px; }
.cert-number { font-size: 0.75rem; color: var(--primary-400); margin-bottom: 12px; font-family: monospace; }
.cert-actions { display: flex; gap: 8px; justify-content: center; }
.cert-btn {
    padding: 8px 16px; border-radius: var(--radius-full); border: 1px solid var(--glass-border);
    background: transparent; color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;
}
.cert-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
.empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
.empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 12px; }
</style>

<h3 style="margin:0 0 20px;"><i class="fas fa-award" style="color:var(--primary-400);margin-right:8px;"></i> My Certificates</h3>
<div class="cert-grid" id="certGrid"><p style="color:var(--text-muted);">Loading...</p></div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function renderCerts(certs) {
    var grid = document.getElementById('certGrid');
    if (!certs.length) { grid.innerHTML = '<div class="card empty" style="grid-column:1/-1;"><i class="fas fa-certificate"></i><p>No certificates yet. Keep learning!</p></div>'; return; }
    var html = '';
    certs.forEach(function(c) {
        html += '<div class="cert-card">' +
            '<div class="cert-icon"><i class="fas fa-award"></i></div>' +
            '<div class="cert-title">' + SkillShare.escapeHtml(c.course_name || 'Certificate') + '</div>' +
            '<div class="cert-meta">Issued: ' + SkillShare.formatDate(c.issue_date) + '</div>' +
            '<div class="cert-number">' + SkillShare.escapeHtml(c.certificate_number || '') + '</div>' +
            '<div class="cert-actions">' +
                '<button class="cert-btn" onclick="window.print()"><i class="fas fa-print"></i> Print</button>' +
                '<button class="cert-btn" onclick="SkillShare.showToast(\'Download\', \'Certificate download starting...\', \'success\')"><i class="fas fa-download"></i> Download</button>' +
            '</div></div>';
    });
    grid.innerHTML = html;
}

SkillShare.apiFetch(BASE + 'api/certificates.php?action=list').then(function(res) {
    if (res.success) renderCerts(res.data);
});
</script>

<?php
endDashboardPage();
