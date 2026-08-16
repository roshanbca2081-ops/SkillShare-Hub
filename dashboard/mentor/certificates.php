<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Certificates';
$sidebar_role = 'mentor';
$sidebar_active = 'Certificates';

startDashboardPage();
?>

<style>
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 12px 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); border-bottom: 1px solid var(--glass-border); }
.data-table td { padding: 12px 10px; font-size: 0.85rem; border-bottom: 1px solid var(--glass-border); vertical-align: middle; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.status-badge { padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; }
.status-badge.status-issued { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-valid { background: rgba(34,197,94,0.15); color: var(--success); }
.status-badge.status-pending { background: rgba(245,158,11,0.15); color: var(--warning); }
.status-badge.status-expired { background: rgba(239,68,68,0.15); color: var(--danger); }
.btn-sm { padding: 5px 12px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.75rem; cursor: pointer; transition: all 0.3s ease; }
.btn-sm:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
.btn-primary { padding: 8px 16px; border-radius: var(--radius-full); background: var(--gradient-primary); border: none; color: #fff; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.8rem; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; display: none; align-items: center; justify-content: center; padding: 20px; }
.modal-overlay.open { display: flex; }
.modal { background: #1a1a2e; border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 24px; max-width: 480px; width: 100%; max-height: 90vh; overflow-y: auto; }
.modal h3 { margin-bottom: 16px; }
.form-group { margin-bottom: 12px; }
.form-group label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px; }
.form-group input, .form-group select { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: var(--radius-md); color: var(--text-primary); outline: none; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; }
</style>

<div class="section-header">
    <h3><i class="fas fa-award" style="color:var(--primary-400);margin-right:8px;"></i>Certificates</h3>
    <button class="btn-primary" onclick="openAward()"><i class="fas fa-plus"></i> Award Certificate</button>
</div>

<div style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>Certificate</th><th>Student</th><th>Course</th><th>Issued</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="certBody"><tr><td colspan="6" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="awardModal">
    <div class="modal">
        <h3>Award Certificate</h3>
        <form id="awardForm">
            <div class="form-group"><label>Student Name</label><input type="text" name="recipient_name" required></div>
            <div class="form-group"><label>Course</label><input type="text" name="course_name" required></div>
            <div class="form-group"><label>Certificate Number</label><input type="text" name="certificate_number" required></div>
            <div class="modal-actions">
                <button type="button" class="btn-sm" onclick="document.getElementById('awardModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn-primary">Award</button>
            </div>
        </form>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
function openAward() { document.getElementById('awardModal').classList.add('open'); }

document.getElementById('awardForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    SkillShare.apiFetch(BASE + 'api/certificates.php?action=create', { method: 'POST', body: formData }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Certificate awarded', 'success'); document.getElementById('awardModal').classList.remove('open'); loadCerts(); }
        else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
    });
});

function loadCerts() {
    SkillShare.apiFetch(BASE + 'api/certificates.php?action=list').then(function(res) {
        var tbody = document.getElementById('certBody');
        if (res.success && res.data && res.data.length) {
            var html = '';
            res.data.forEach(function(c) {
                html += '<tr><td><strong>' + SkillShare.escapeHtml(c.certificate_number) + '</strong></td>' +
                    '<td>' + SkillShare.escapeHtml(c.recipient_name) + '</td>' +
                    '<td>' + SkillShare.escapeHtml(c.course_name || 'N/A') + '</td>' +
                    '<td>' + SkillShare.formatDate(c.issue_date) + '</td>' +
                    '<td><span class="status-badge status-' + (c.status === 'valid' ? 'valid' : (c.status === 'issued' ? 'issued' : 'pending')) + '">' + c.status + '</span></td>' +
                    '<td><button class="btn-sm" onclick="SkillShare.showToast(\'Download\',\'Downloading certificate...\',\'info\')"><i class="fas fa-download"></i></button></td></tr>';
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--text-muted);">No certificates found</td></tr>';
        }
    });
}
loadCerts();
</script>

<?php
endDashboardPage();
