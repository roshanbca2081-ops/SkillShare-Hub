<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Mentors';
$sidebar_items = [
    'Main' => [
        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'link' => 'index.php'],
        ['label' => 'Users', 'icon' => 'fa-users', 'link' => 'users.php'],
        ['label' => 'Mentors', 'icon' => 'fa-user-tie', 'link' => 'mentors.php'],
        ['label' => 'Freshers', 'icon' => 'fa-user-graduate', 'link' => 'freshers.php'],
    ],
    'Management' => [
        ['label' => 'Courses', 'icon' => 'fa-book-open', 'link' => 'courses.php'],
        ['label' => 'Academic Fields', 'icon' => 'fa-layer-group', 'link' => 'academic-fields.php'],
        ['label' => 'Bookings', 'icon' => 'fa-calendar-check', 'link' => 'bookings.php', 'badge' => '5'],
        ['label' => 'Sessions', 'icon' => 'fa-video', 'link' => 'sessions.php'],
        ['label' => 'Assignments', 'icon' => 'fa-file-pen', 'link' => 'assignments.php'],
        ['label' => 'Research', 'icon' => 'fa-flask', 'link' => 'research.php'],
        ['label' => 'Certificates', 'icon' => 'fa-award', 'link' => 'certificates.php'],
    ],
    'Finance' => [
        ['label' => 'Payments', 'icon' => 'fa-credit-card', 'link' => 'payments.php'],
        ['label' => 'Reports', 'icon' => 'fa-chart-pie', 'link' => 'reports.php'],
    ],
    'System' => [
        ['label' => 'Notifications', 'icon' => 'fa-bell', 'link' => 'notifications.php', 'badge' => '3'],
        ['label' => 'Profile', 'icon' => 'fa-user', 'link' => 'profile.php'],
        ['label' => 'Settings', 'icon' => 'fa-gear', 'link' => 'settings.php'],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentors | Admin - SkillShare Hub</title>
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
        <div class="dash-topbar">
            <div class="dt-left">
                <button class="hamburger sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar"><span></span><span></span><span></span></button>
                <div class="dt-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="tableSearch" placeholder="Search mentors..."></div>
            </div>
            <div class="dt-right">
                <button class="dt-icon-btn" aria-label="Notifications"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></button>
                <button class="dt-avatar-btn"><img src="../../assets/images/profile/avatar-1.svg" alt="Admin"><span>Admin</span><i class="fa-solid fa-chevron-down" style="font-size:.7rem;color:var(--gray-500);"></i></button>
            </div>
        </div>

        <div class="dash-grid-4" style="margin-bottom:var(--spacing-5);">
            <div class="stat-card reveal"><div class="stat-icon primary"><i class="fa-solid fa-user-tie"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="85">0</h4><p>Total Mentors</p></div></div>
            <div class="stat-card reveal reveal-delay-1"><div class="stat-icon secondary"><i class="fa-solid fa-circle-check"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="72">0</h4><p>Approved</p></div></div>
            <div class="stat-card reveal reveal-delay-2"><div class="stat-icon accent"><i class="fa-solid fa-clock"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="9">0</h4><p>Pending</p></div></div>
            <div class="stat-card reveal reveal-delay-3"><div class="stat-icon warning-bg"><i class="fa-solid fa-ban"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="4">0</h4><p>Suspended</p></div></div>
        </div>

        <div class="panel reveal">
            <div class="panel-header">
                <h5><i class="fa-solid fa-user-tie" style="color:var(--primary);"></i> Mentor Applications</h5>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Add Mentor</button>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Mentor</th>
                            <th>Specialization</th>
                            <th>Courses</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $mentors = [
                            ['Dr. Aisha Khan', 'Data Science', 12, '4.9', 'Approved', 'avatar-1.svg'],
                            ['Prof. James Carter', 'Engineering', 8, '4.8', 'Approved', 'avatar-2.svg'],
                            ['Robert Garcia', 'Web Development', 15, '4.7', 'Approved', 'avatar-3.svg'],
                            ['Dr. Emily Chen', 'Medicine', 6, '4.9', 'Pending', 'avatar-4.svg'],
                            ['David Brown', 'Business', 10, '4.5', 'Approved', 'avatar-5.svg'],
                            ['Lisa Anderson', 'Design', 7, '4.6', 'Suspended', 'avatar-6.svg'],
                        ];
                        foreach ($mentors as $m): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><div class="user-cell"><img src="../../assets/images/profile/<?php echo $m[6]; ?>" alt=""><strong><?php echo $m[0]; ?></strong></div></td>
                                <td><?php echo $m[1]; ?></td>
                                <td><?php echo $m[2]; ?></td>
                                <td><i class="fa-solid fa-star" style="color:var(--warning);"></i> <?php echo $m[3]; ?></td>
                                <td><span class="status-pill <?php echo strtolower($m[4]) === 'approved' ? 'approved' : (strtolower($m[4]) === 'pending' ? 'pending' : 'rejected'); ?>"><?php echo $m[4]; ?></span></td>
                                <td>
                                    <div class="table-actions">
                                        <button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button>
                                        <button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button>
                                        <button class="delete-btn" aria-label="Delete"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
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
