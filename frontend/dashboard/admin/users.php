<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Users';
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
    <title>Users | Admin - SkillShare Hub</title>
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
                <div class="dt-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="tableSearch" placeholder="Search users..."></div>
            </div>
            <div class="dt-right">
                <button class="dt-icon-btn" aria-label="Notifications"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></button>
                <button class="dt-avatar-btn"><img src="../../assets/images/profile/avatar-1.svg" alt="Admin"><span>Admin</span><i class="fa-solid fa-chevron-down" style="font-size:.7rem;color:var(--gray-500);"></i></button>
            </div>
        </div>

        <div class="panel reveal">
            <div class="panel-header">
                <h5><i class="fa-solid fa-users" style="color:var(--primary);"></i> All Users <span class="badge badge-primary">1,250</span></h5>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Add User</button>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $users = [
                            ['Sarah Johnson', 'sarah@example.com', 'Fresher', 'Active', 'Jan 5, 2025', 'avatar-1.svg'],
                            ['Michael Chen', 'michael@example.com', 'Mentor', 'Active', 'Jan 3, 2025', 'avatar-2.svg'],
                            ['Emily Davis', 'emily@example.com', 'Fresher', 'Pending', 'Dec 28, 2024', 'avatar-3.svg'],
                            ['Robert Garcia', 'robert@example.com', 'Mentor', 'Active', 'Dec 20, 2024', 'avatar-4.svg'],
                            ['Olivia Martinez', 'olivia@example.com', 'Fresher', 'Suspended', 'Dec 15, 2024', 'avatar-5.svg'],
                            ['James Wilson', 'james@example.com', 'Fresher', 'Active', 'Dec 10, 2024', 'avatar-6.svg'],
                        ];
                        foreach ($users as $user): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><div class="user-cell"><img src="../../assets/images/profile/<?php echo $user[6]; ?>" alt=""><strong><?php echo $user[0]; ?></strong></div></td>
                                <td><?php echo $user[1]; ?></td>
                                <td><span class="badge badge-<?php echo $user[2] === 'Mentor' ? 'secondary' : 'primary'; ?>"><?php echo $user[2]; ?></span></td>
                                <td><span class="status-pill <?php echo strtolower($user[3]) === 'active' ? 'approved' : (strtolower($user[3]) === 'pending' ? 'pending' : 'rejected'); ?>"><?php echo $user[3]; ?></span></td>
                                <td><?php echo $user[4]; ?></td>
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
