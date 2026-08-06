<?php
session_start();
$sidebar_role = 'mentor';
$sidebar_active = 'Bookings';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings | Mentor - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/booking.css">
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
            <div class="stat-card reveal"><div class="stat-icon primary"><i class="fa-solid fa-calendar-check"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="28">0</h4><p>Total Bookings</p></div></div>
            <div class="stat-card reveal reveal-delay-1"><div class="stat-icon secondary"><i class="fa-solid fa-circle-check"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="22">0</h4><p>Approved</p></div></div>
            <div class="stat-card reveal reveal-delay-2"><div class="stat-icon accent"><i class="fa-solid fa-clock"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="4">0</h4><p>Pending</p></div></div>
            <div class="stat-card reveal reveal-delay-3"><div class="stat-icon warning-bg"><i class="fa-solid fa-ban"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="2">0</h4><p>Cancelled</p></div></div>
        </div>
        <div class="panel reveal">
            <div class="panel-header"><h5><i class="fa-solid fa-calendar-check" style="color:var(--primary);"></i> My Bookings</h5><button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Set Availability</button></div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Student</th><th>Course</th><th>Date</th><th>Time</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $mybookings = [
                            ['Sarah Johnson', 'Data Science', 'Jan 12', '10:00 AM', 'Approved'],
                            ['Michael Chen', 'Machine Learning', 'Jan 11', '02:00 PM', 'Pending'],
                            ['Emily Davis', 'Web Dev', 'Jan 10', '11:30 AM', 'Approved'],
                            ['James Wilson', 'Python', 'Jan 09', '04:00 PM', 'Cancelled'],
                            ['Olivia Martinez', 'Statistics', 'Jan 08', '09:00 AM', 'Approved'],
                        ];
                        foreach ($mybookings as $b): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><div class="user-cell"><img src="../../assets/images/profile/avatar-1.svg" alt=""><strong><?php echo $b[0]; ?></strong></div></td>
                                <td><?php echo $b[1]; ?></td>
                                <td><?php echo $b[2]; ?></td>
                                <td><?php echo $b[3]; ?></td>
                                <td><span class="status-pill <?php echo strtolower($b[4]) === 'approved' ? 'approved' : (strtolower($b[4]) === 'pending' ? 'pending' : 'rejected'); ?>"><?php echo $b[4]; ?></span></td>
                                <td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button><button class="delete-btn" aria-label="Cancel"><i class="fa-solid fa-ban"></i></button></div></td>
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
    <script src="../../assets/js/booking.js"></script>
    <script src="../../assets/js/animation.js"></script>
</body>

</html>
