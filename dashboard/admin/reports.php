<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Reports';
$sidebar_role = 'admin';
$sidebar_active = 'Reports';

startDashboardPage();
?>
<style>
.report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px; }
.bar-chart { display: flex; align-items: flex-end; gap: 8px; height: 180px; padding-top: 16px; }
.bar-chart .bar { flex: 1; background: var(--gradient-primary); border-radius: 6px 6px 0 0; min-height: 8px; transition: all 0.3s ease; position: relative; }
.bar-chart .bar:hover { opacity: 0.8; }
.bar-labels { display: flex; gap: 8px; margin-top: 8px; }
.bar-labels span { flex: 1; text-align: center; font-size: 0.7rem; color: var(--text-muted); }
</style>

<div class="report-grid">
    <div class="panel reveal">
        <div class="panel-header"><h5><i class="fa-solid fa-chart-pie" style="color:var(--primary);"></i> Enrollment by Field</h5></div>
        <div class="panel-body">
            <div id="fieldReport" style="display:flex;flex-direction:column;gap:12px;"><p style="color:var(--text-muted);font-size:0.85rem;">Loading...</p></div>
        </div>
    </div>
    <div class="panel reveal reveal-delay-1">
        <div class="panel-header"><h5><i class="fa-solid fa-chart-column" style="color:var(--secondary);"></i> Monthly Revenue</h5></div>
        <div class="panel-body">
            <div class="bar-chart" id="revenueChart"></div>
            <div class="bar-labels" id="revenueLabels"></div>
        </div>
    </div>
</div>

<div class="panel reveal">
    <div class="panel-header">
        <h5><i class="fa-solid fa-file-arrow-down" style="color:var(--primary);"></i> Download Reports</h5>
        <button class="btn btn-primary btn-sm" onclick="generateReport()"><i class="fa-solid fa-file-export"></i> Generate Report</button>
    </div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead><tr><th>Report</th><th>Period</th><th>Generated</th><th>Format</th><th>Actions</th></tr></thead>
            <tbody>
                <tr><td><strong>Monthly Revenue Report</strong></td><td>Dec 2024</td><td>Jan 01, 2025</td><td><span class="badge badge-success">PDF</span></td><td><div class="table-actions"><button class="view-btn" onclick="downloadReport('revenue')" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td></tr>
                <tr><td><strong>User Growth Report</strong></td><td>Q4 2024</td><td>Jan 01, 2025</td><td><span class="badge badge-success">PDF</span></td><td><div class="table-actions"><button class="view-btn" onclick="downloadReport('users')" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td></tr>
                <tr><td><strong>Course Performance Report</strong></td><td>Dec 2024</td><td>Dec 31, 2024</td><td><span class="badge badge-primary">CSV</span></td><td><div class="table-actions"><button class="view-btn" onclick="downloadReport('courses')" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td></tr>
                <tr><td><strong>Mentor Payout Report</strong></td><td>Nov 2024</td><td>Dec 01, 2024</td><td><span class="badge badge-success">PDF</span></td><td><div class="table-actions"><button class="view-btn" onclick="downloadReport('mentors')" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

SkillShare.apiFetch(BASE + 'api/admin.php?action=stats').then(function(res) {
    if (res.success && res.data && res.data.stats) {
        var s = res.data.stats;
        var html = '';
        var fields = [
            {name:'Engineering & Technology', pct:35},
            {name:'Medicine & Healthcare', pct:25},
            {name:'Business & Finance', pct:20},
            {name:'Creative Arts & Design', pct:12},
            {name:'Other Fields', pct:8}
        ];
        fields.forEach(function(f) {
            html += '<div><div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;font-size:.85rem;"><strong>' + f.name + '</strong><span>' + f.pct + '%</span></div><div class="progress-track"><div class="progress-fill" style="width:' + f.pct + '%;"></div></div></div>';
        });
        document.getElementById('fieldReport').innerHTML = html;
    }
});

var revenueData = [50,70,55,80,65,95,75];
var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul'];
var chart = document.getElementById('revenueChart');
var labels = document.getElementById('revenueLabels');
revenueData.forEach(function(h, i) {
    var bar = document.createElement('div'); bar.className = 'bar'; bar.style.height = h + '%'; chart.appendChild(bar);
    var lbl = document.createElement('span'); lbl.textContent = months[i]; labels.appendChild(lbl);
});

function generateReport() { SkillShare.showToast('Report', 'Generating report...', 'info'); }
function downloadReport(type) { SkillShare.showToast('Download', 'Downloading ' + type + ' report...', 'success'); }
</script>
<?php
endDashboardPage();
?>
