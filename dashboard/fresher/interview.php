<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Interview';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Interview | Fresher - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/session.css">
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

        <div class="dash-grid-2" style="align-items:flex-start;">
            <div class="panel reveal">
                <div class="panel-header"><h5><i class="fa-solid fa-people-group" style="color:var(--primary);"></i> Book Mock Interview</h5></div>
                <div class="panel-body">
                    <form>
                        <div class="dash-form-grid">
                            <div class="form-group"><label class="form-label">Select Mentor</label>
                                <select class="form-control"><option>Dr. Aisha Khan</option><option>Prof. James Carter</option><option>Robert Garcia</option><option>Dr. Emily Chen</option></select>
                            </div>
                            <div class="form-group"><label class="form-label">Interview Type</label>
                                <select class="form-control"><option>Technical</option><option>Behavioral</option><option>HR Round</option><option>Full Loop</option></select>
                            </div>
                            <div class="form-group"><label class="form-label">Date</label><input type="date" class="form-control"></div>
                            <div class="form-group"><label class="form-label">Time</label>
                                <select class="form-control"><option>09:00 AM</option><option>10:00 AM</option><option>11:00 AM</option><option>02:00 PM</option><option>04:00 PM</option></select>
                            </div>
                            <div class="form-group" style="grid-column:1/-1;"><label class="form-label">Preferred Role</label><input type="text" class="form-control" placeholder="e.g. Data Analyst, Full-Stack Dev"></div>
                        </div>
                        <button class="btn btn-primary"><i class="fa-solid fa-calendar-check"></i> Request Booking</button>
                    </form>
                </div>
            </div>
            <div class="panel reveal reveal-delay-1">
                <div class="panel-header"><h5><i class="fa-solid fa-clock-rotate-left" style="color:var(--secondary);"></i> Interview History</h5></div>
                <div class="activity-list">
                    <div class="activity-item"><div class="activity-icon" style="background:var(--success);color:#fff;"><i class="fa-solid fa-check"></i></div><div><h6>Technical Round - Passed</h6><p>Prof. James Carter · Jan 05</p></div><span class="activity-time">4.8/5</span></div>
                    <div class="activity-item"><div class="activity-icon" style="background:var(--primary-soft);color:var(--primary);"><i class="fa-solid fa-star"></i></div><div><h6>Full Loop - Completed</h6><p>Dr. Emily Chen · Dec 20</p></div><span class="activity-time">4.5/5</span></div>
                    <div class="activity-item"><div class="activity-icon" style="background:var(--accent-soft);color:var(--accent);"><i class="fa-solid fa-hourglass-half"></i></div><div><h6>Behavioral - Pending</h6><p>Robert Garcia · Jan 15</p></div><span class="activity-time">Upcoming</span></div>
                </div>
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
