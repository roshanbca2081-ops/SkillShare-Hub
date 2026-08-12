<?php
session_start();
$sidebar_role = 'mentor';
$sidebar_active = 'Sessions';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions | Mentor - SkillShare Hub</title>
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

        <div class="panel reveal">
            <div class="panel-header"><h5><i class="fa-solid fa-video" style="color:var(--primary);"></i> My Sessions</h5><button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Schedule Session</button></div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Session</th><th>Students</th><th>Date</th><th>Time</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $mysessions = [
                            ['Data Science Q&A', 12, 'Jan 14', '10:00 AM', '60 min', 'Scheduled'],
                            ['ML Workshop', 18, 'Jan 15', '02:00 PM', '90 min', 'Scheduled'],
                            ['Web Dev Bootcamp', 25, 'Jan 13', '11:00 AM', '120 min', 'Ongoing'],
                            ['Python Crash Course', 30, 'Jan 12', '04:00 PM', '60 min', 'Completed'],
                            ['Statistics Review', 15, 'Jan 11', '09:00 AM', '45 min', 'Cancelled'],
                        ];
                        foreach ($mysessions as $s): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><strong><?php echo $s[0]; ?></strong></td>
                                <td><?php echo $s[1]; ?></td>
                                <td><?php echo $s[2]; ?></td>
                                <td><?php echo $s[3]; ?></td>
                                <td><?php echo $s[4]; ?></td>
                                <td><span class="status-pill <?php echo strtolower($s[5]) === 'completed' ? 'approved' : (strtolower($s[5]) === 'scheduled' || strtolower($s[5]) === 'ongoing' ? 'pending' : 'rejected'); ?>"><?php echo $s[5]; ?></span></td>
                                <td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Join"><i class="fa-solid fa-video"></i></button><button class="delete-btn" aria-label="Delete"><i class="fa-solid fa-trash"></i></button></div></td>
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
