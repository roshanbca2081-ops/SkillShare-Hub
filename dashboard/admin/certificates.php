<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Certificates';
$sidebar_role = 'admin';
$sidebar_active = 'Certificates';

startDashboardPage();
?>
<style>
.cert-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="cert-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="cTotal">0</div><div class="stat-label">Issued</div></div><div class="stat-icon"><i class="fas fa-award"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="cValid">0</div><div class="stat-label">Valid</div></div><div class="stat-icon"><i class="fas fa-circle-check"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="cRevoked">0</div><div class="stat-label">Revoked</div></div><div class="stat-icon"><i class="fas fa-ban"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="cExpired">0</div><div class="stat-label">Expired</div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-award" style="color:var(--primary);"></i> Issued Certificates</h5></div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Certificate</th><th>Student</th><th>Course</th><th>Issued Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="certsTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='valid'||s==='issued'?'approved':'rejected'; }

SkillShare.apiFetch(BASE + 'api/certificates.php?action=list').then(function(res) {
    if (res.success && res.data) {
        var data = res.data;
        document.getElementById('cTotal').textContent = data.length;
        document.getElementById('cValid').textContent = data.filter(function(c){ return c.status==='valid'||c.status==='issued'; }).length;
        document.getElementById('cRevoked').textContent = data.filter(function(c){ return c.status==='revoked'; }).length;
        document.getElementById('cExpired').textContent = data.filter(function(c){ return c.status==='expired'; }).length;

        var html = '';
        data.forEach(function(c) {
            html += '<tr>' +
                '<td><input type="checkbox" class="row-check"></td>' +
                '<td><strong>' + SkillShare.escapeHtml(c.certificate_number||c.id) + '</strong></td>' +
                '<td><div class="user-cell"><img src="../../assets/images/profile/default.png" alt=""><strong>' + SkillShare.escapeHtml(c.user_name||c.student_name||'Student') + '</strong></div></td>' +
                '<td>' + SkillShare.escapeHtml(c.course_name||'Course') + '</td>' +
                '<td>' + SkillShare.formatDate(c.issue_date||c.created_at) + '</td>' +
                '<td><span class="status-pill ' + getStatusClass(c.status) + '">' + c.status + '</span></td>' +
                '<td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" onclick="downloadCert(' + c.id + ')" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td></tr>';
        });
        document.getElementById('certsTable').innerHTML = html || '<tr><td colspan="7" style="text-align:center;">No certificates found</td></tr>';
    }
});

function downloadCert(id) { SkillShare.showToast('Download', 'Certificate ' + id + ' downloading...', 'info'); }
</script>
<?php
endDashboardPage();
?>
