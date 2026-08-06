<?php
session_start();
$sidebar_role = 'mentor';
$sidebar_active = 'Dashboard';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Dashboard | SkillShare Hub</title>
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

        <div class="dash-grid-4" style="margin-bottom:var(--spacing-5);">
            <div class="stat-card reveal"><div class="stat-icon primary"><i class="fa-solid fa-book-open"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="12">0</h4><p>My Courses</p></div></div>
            <div class="stat-card reveal reveal-delay-1"><div class="stat-icon secondary"><i class="fa-solid fa-user-graduate"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="240">0</h4><p>Total Students</p></div></div>
            <div class="stat-card reveal reveal-delay-2"><div class="stat-icon accent"><i class="fa-solid fa-calendar-check"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="18">0</h4><p>Upcoming Sessions</p></div></div>
            <div class="stat-card reveal reveal-delay-3"><div class="stat-icon warning-bg"><i class="fa-solid fa-circle-dollar"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="8700">0</h4><p>Earnings ($)</p></div></div>
        </div>

        <div class="dash-grid-2" style="margin-bottom:var(--spacing-5);">
            <div class="panel reveal">
                <div class="panel-header"><h5><i class="fa-solid fa-chart-line" style="color:var(--primary);"></i> Earnings Overview</h5></div>
                <div class="panel-body">
                    <div class="chart-box" id="dashChart">
                        <div class="bar chart-bar" style="height:40%"></div>
                        <div class="bar chart-bar" style="height:55%"></div>
                        <div class="bar chart-bar" style="height:48%"></div>
                        <div class="bar chart-bar" style="height:70%"></div>
                        <div class="bar chart-bar" style="height:62%"></div>
                        <div class="bar chart-bar" style="height:85%"></div>
                        <div class="bar chart-bar" style="height:78%"></div>
                    </div>
                </div>
            </div>
            <div class="panel reveal reveal-delay-2">
                <div class="panel-header"><h5><i class="fa-solid fa-calendar-day" style="color:var(--secondary);"></i> Upcoming Sessions</h5><a href="sessions.php" class="btn btn-sm btn-outline">View All</a></div>
                <div class="panel-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon" style="background:var(--primary-soft);color:var(--primary);"><i class="fa-solid fa-video"></i></div>
                            <div><h6>Data Science Q&A</h6><p>Jan 14, 2025 · 10:00 AM</p></div><span class="activity-time">Today</span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon" style="background:var(--secondary-soft);color:var(--secondary-dark);"><i class="fa-solid fa-people-group"></i></div>
                            <div><h6>ML Workshop</h6><p>Jan 15, 2025 · 02:00 PM</p></div><span class="activity-time">Tomorrow</span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon" style="background:var(--accent-soft);color:var(--accent);"><i class="fa-solid fa-chalkboard-user"></i></div>
                            <div><h6>Web Dev Bootcamp</h6><p>Jan 16, 2025 · 11:00 AM</p></div><span class="activity-time">Wed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel reveal">
            <div class="panel-header"><h5><i class="fa-solid fa-user-graduate" style="color:var(--primary);"></i> Recent Students</h5><a href="students.php" class="btn btn-sm btn-primary">View All</a></div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Student</th><th>Course</th><th>Progress</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <tr><td><input type="checkbox" class="row-check"></td><td><div class="user-cell"><img src="../../assets/images/profile/avatar-1.svg" alt=""><strong>Sarah Johnson</strong></div></td><td>Data Science</td><td><div style="display:flex;align-items:center;gap:0.6rem;"><div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:75%;"></div></div><span style="font-size:.8rem;font-weight:600;">75%</span></div></td><td><span class="status-pill approved">Active</span></td><td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Message"><i class="fa-solid fa-comment"></i></button></div></td></tr>
                        <tr><td><input type="checkbox" class="row-check"></td><td><div class="user-cell"><img src="../../assets/images/profile/avatar-3.svg" alt=""><strong>Emily Davis</strong></div></td><td>Web Dev</td><td><div style="display:flex;align-items:center;gap:0.6rem;"><div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:50%;"></div></div><span style="font-size:.8rem;font-weight:600;">50%</span></div></td><td><span class="status-pill pending">In Progress</span></td><td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Message"><i class="fa-solid fa-comment"></i></button></div></td></tr>
                        <tr><td><input type="checkbox" class="row-check"></td><td><div class="user-cell"><img src="../../assets/images/profile/avatar-4.svg" alt=""><strong>James Wilson</strong></div></td><td>Machine Learning</td><td><div style="display:flex;align-items:center;gap:0.6rem;"><div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:90%;"></div></div><span style="font-size:.8rem;font-weight:600;">90%</span></div></td><td><span class="status-pill approved">Active</span></td><td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Message"><i class="fa-solid fa-comment"></i></button></div></td></tr>
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
