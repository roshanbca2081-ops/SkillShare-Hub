<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Reports';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Admin - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include '../../components/loader.php'; ?>
    <?php include '../../components/sidebar.php'; ?>

    <div class="dashboard-main">
        <?php include __DIR__ . '/_topbar.php'; ?>

        <div class="dash-grid-2" style="margin-bottom:var(--spacing-5);">
            <div class="panel reveal">
                <div class="panel-header"><h5><i class="fa-solid fa-chart-pie" style="color:var(--primary);"></i> Enrollment by Field</h5></div>
                <div class="panel-body">
                    <div style="display:flex;flex-direction:column;gap:var(--spacing-4);">
                        <?php
                        $reportBars = [
                            ['Engineering & Technology', 35, 'var(--gradient-primary)'],
                            ['Medicine & Healthcare', 25, 'var(--gradient-secondary)'],
                            ['Business & Finance', 20, 'var(--gradient-accent)'],
                            ['Creative Arts & Design', 12, '#fdcb6e'],
                            ['Other Fields', 8, '#a29bfe'],
                        ];
                        foreach ($reportBars as $r): ?>
                            <div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;font-size:.85rem;">
                                    <strong><?php echo $r[0]; ?></strong><span><?php echo $r[1]; ?>%</span>
                                </div>
                                <div class="progress-track"><div class="progress-fill" style="width:<?php echo $r[1]; ?>%;background:<?php echo $r[2]; ?>;"></div></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="panel reveal reveal-delay-2">
                <div class="panel-header"><h5><i class="fa-solid fa-chart-column" style="color:var(--secondary);"></i> Monthly Revenue</h5></div>
                <div class="panel-body">
                    <div class="chart-box" id="dashChart">
                        <div class="bar chart-bar" style="height:50%"></div>
                        <div class="bar chart-bar" style="height:70%"></div>
                        <div class="bar chart-bar" style="height:55%"></div>
                        <div class="bar chart-bar" style="height:80%"></div>
                        <div class="bar chart-bar" style="height:65%"></div>
                        <div class="bar chart-bar" style="height:95%"></div>
                        <div class="bar chart-bar" style="height:75%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel reveal">
            <div class="panel-header">
                <h5><i class="fa-solid fa-file-arrow-down" style="color:var(--primary);"></i> Download Reports</h5>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-file-export"></i> Generate Report</button>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr>
                            <th>Report</th>
                            <th>Period</th>
                            <th>Generated</th>
                            <th>Format</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>Monthly Revenue Report</strong></td><td>Dec 2024</td><td>Jan 01, 2025</td><td><span class="badge badge-success">PDF</span></td><td><div class="table-actions"><button class="view-btn" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td></tr>
                        <tr><td><strong>User Growth Report</strong></td><td>Q4 2024</td><td>Jan 01, 2025</td><td><span class="badge badge-success">PDF</span></td><td><div class="table-actions"><button class="view-btn" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td></tr>
                        <tr><td><strong>Course Performance Report</strong></td><td>Dec 2024</td><td>Dec 31, 2024</td><td><span class="badge badge-primary">CSV</span></td><td><div class="table-actions"><button class="view-btn" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td></tr>
                        <tr><td><strong>Mentor Payout Report</strong></td><td>Nov 2024</td><td>Dec 01, 2024</td><td><span class="badge badge-success">PDF</span></td><td><div class="table-actions"><button class="view-btn" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/navbar.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/animation.js"></script>
</body>

</html>
