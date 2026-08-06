<?php
session_start();
$sidebar_role = 'mentor';
$sidebar_active = 'Interview';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Prep | Mentor - SkillShare Hub</title>
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
            <div class="stat-card reveal"><div class="stat-icon primary"><i class="fa-solid fa-people-group"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="15">0</h4><p>Interview Preps</p></div></div>
            <div class="stat-card reveal reveal-delay-1"><div class="stat-icon secondary"><i class="fa-solid fa-circle-check"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="11">0</h4><p>Completed</p></div></div>
            <div class="stat-card reveal reveal-delay-2"><div class="stat-icon accent"><i class="fa-solid fa-clock"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="4">0</h4><p>Upcoming</p></div></div>
        </div>

        <div class="panel reveal">
            <div class="panel-header"><h5><i class="fa-solid fa-people-group" style="color:var(--primary);"></i> Interview Preparation Sessions</h5><button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Schedule Mock</button></div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Student</th><th>Role/Field</th><th>Date</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $interviews = [
                            ['Sarah Johnson', 'Data Scientist', 'Jan 14', 'Mock Interview', 'Scheduled'],
                            ['Michael Chen', 'ML Engineer', 'Jan 15', 'Resume Review', 'Scheduled'],
                            ['Emily Davis', 'Web Developer', 'Jan 13', 'Mock Interview', 'Completed'],
                            ['James Wilson', 'Data Analyst', 'Jan 12', 'Technical Round', 'Completed'],
                            ['Olivia Martinez', 'Statistician', 'Jan 11', 'Mock Interview', 'Cancelled'],
                        ];
                        foreach ($interviews as $iv): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><div class="user-cell"><img src="../../assets/images/profile/avatar-1.svg" alt=""><strong><?php echo $iv[0]; ?></strong></div></td>
                                <td><?php echo $iv[1]; ?></td>
                                <td><?php echo $iv[2]; ?></td>
                                <td><span class="badge badge-secondary"><?php echo $iv[3]; ?></span></td>
                                <td><span class="status-pill <?php echo strtolower($iv[4]) === 'completed' ? 'approved' : (strtolower($iv[4]) === 'scheduled' ? 'pending' : 'rejected'); ?>"><?php echo $iv[4]; ?></span></td>
                                <td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Join"><i class="fa-solid fa-video"></i></button></div></td>
                            </tr>
                        <?php endforeach; ?>
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
