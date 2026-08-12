<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Sessions';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions | Fresher - SkillShare Hub</title>
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
            <div class="panel-header"><h5><i class="fa-solid fa-video" style="color:var(--primary);"></i> Session History</h5></div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Session</th><th>Mentor</th><th>Date</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $fresher_sessions = [
                            ['Data Science Q&A', 'Dr. Aisha Khan', 'Jan 14', '60 min', 'Upcoming'],
                            ['Mock Interview', 'Prof. James Carter', 'Jan 15', '45 min', 'Scheduled'],
                            ['Python Workshop', 'Robert Garcia', 'Jan 16', '90 min', 'Upcoming'],
                            ['Career Guidance', 'Dr. Emily Chen', 'Jan 10', '30 min', 'Completed'],
                            ['Resume Review', 'David Brown', 'Jan 08', '45 min', 'Completed'],
                        ];
                        foreach ($fresher_sessions as $s): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><strong><?php echo $s[0]; ?></strong></td>
                                <td><?php echo $s[1]; ?></td>
                                <td><?php echo $s[2]; ?></td>
                                <td><?php echo $s[3]; ?></td>
                                <td><span class="status-pill <?php echo $s[4] === 'Completed' ? 'approved' : ($s[4] === 'Scheduled' ? 'pending' : ''); ?>"><?php echo $s[4]; ?></span></td>
                                <td><div class="table-actions"><button class="view-btn" aria-label="Join"><i class="fa-solid fa-video"></i></button><button class="edit-btn" aria-label="Info"><i class="fa-solid fa-circle-info"></i></button></div></td>
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
