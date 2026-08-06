<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Dashboard';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresher Dashboard | SkillShare Hub</title>
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
            <div class="stat-card reveal"><div class="stat-icon primary"><i class="fa-solid fa-book-open"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="6">0</h4><p>Enrolled Courses</p></div></div>
            <div class="stat-card reveal reveal-delay-1"><div class="stat-icon secondary"><i class="fa-solid fa-circle-check"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="12">0</h4><p>Completed Sessions</p></div></div>
            <div class="stat-card reveal reveal-delay-2"><div class="stat-icon accent"><i class="fa-solid fa-award"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="3">0</h4><p>Certificates</p></div></div>
            <div class="stat-card reveal reveal-delay-3"><div class="stat-icon warning-bg"><i class="fa-solid fa-gauge-high"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="68">0</h4><p>Overall Progress (%)</p></div></div>
        </div>

        <div class="dash-grid-2" style="margin-bottom:var(--spacing-5);">
            <div class="panel reveal">
                <div class="panel-header"><h5><i class="fa-solid fa-chart-line" style="color:var(--primary);"></i> Learning Progress</h5></div>
                <div class="panel-body">
                    <div class="chart-box" id="dashChart">
                        <div class="bar chart-bar" style="height:35%"></div>
                        <div class="bar chart-bar" style="height:55%"></div>
                        <div class="bar chart-bar" style="height:42%"></div>
                        <div class="bar chart-bar" style="height:65%"></div>
                        <div class="bar chart-bar" style="height:50%"></div>
                        <div class="bar chart-bar" style="height:78%"></div>
                        <div class="bar chart-bar" style="height:68%"></div>
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
                            <div><h6>Mock Interview</h6><p>Jan 15, 2025 · 02:00 PM</p></div><span class="activity-time">Tomorrow</span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon" style="background:var(--accent-soft);color:var(--accent);"><i class="fa-solid fa-chalkboard-user"></i></div>
                            <div><h6>Python Workshop</h6><p>Jan 16, 2025 · 11:00 AM</p></div><span class="activity-time">Wed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel reveal">
            <div class="panel-header"><h5><i class="fa-solid fa-book-open" style="color:var(--primary);"></i> My Enrolled Courses</h5><a href="courses.php" class="btn btn-sm btn-primary">Browse Courses</a></div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Course</th><th>Mentor</th><th>Progress</th><th>Grade</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <tr><td><input type="checkbox" class="row-check"></td><td><div class="user-cell"><img src="../../assets/images/course/intro-datascience.svg" alt=""><strong>Data Science Fundamentals</strong></div></td><td>Dr. Aisha Khan</td><td><div style="display:flex;align-items:center;gap:0.6rem;"><div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:75%;"></div></div><span style="font-size:.8rem;font-weight:600;">75%</span></div></td><td>A</td><td><span class="status-pill approved">In Progress</span></td><td><div class="table-actions"><button class="view-btn" aria-label="Continue"><i class="fa-solid fa-play"></i></button></div></td></tr>
                        <tr><td><input type="checkbox" class="row-check"></td><td><div class="user-cell"><img src="../../assets/images/course/python-basics.svg" alt=""><strong>Python for Beginners</strong></div></td><td>Prof. James Carter</td><td><div style="display:flex;align-items:center;gap:0.6rem;"><div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:100%;"></div></div><span style="font-size:.8rem;font-weight:600;">100%</span></div></td><td>A+</td><td><span class="status-pill approved">Completed</span></td><td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Certificate"><i class="fa-solid fa-award"></i></button></div></td></tr>
                        <tr><td><input type="checkbox" class="row-check"></td><td><div class="user-cell"><img src="../../assets/images/course/web-dev.svg" alt=""><strong>Full-Stack Web Development</strong></div></td><td>Robert Garcia</td><td><div style="display:flex;align-items:center;gap:0.6rem;"><div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:40%;"></div></div><span style="font-size:.8rem;font-weight:600;">40%</span></div></td><td>B</td><td><span class="status-pill pending">In Progress</span></td><td><div class="table-actions"><button class="view-btn" aria-label="Continue"><i class="fa-solid fa-play"></i></button></div></td></tr>
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
