<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Dashboard';
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
    <title>Admin Dashboard | SkillShare Hub</title>

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
                <button class="hamburger sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <span></span><span></span><span></span>
                </button>
                <div class="dt-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="tableSearch" placeholder="Search dashboard...">
                </div>
            </div>
            <div class="dt-right">
                <button class="dt-icon-btn" aria-label="Notifications"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></button>
                <button class="dt-icon-btn" aria-label="Messages"><i class="fa-solid fa-envelope"></i></button>
                <button class="dt-avatar-btn">
                    <img src="../../assets/images/profile/avatar-1.svg" alt="Admin">
                    <span>Admin</span>
                    <i class="fa-solid fa-chevron-down" style="font-size:.7rem;color:var(--gray-500);"></i>
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="dash-grid-4" style="margin-bottom:var(--spacing-5);">
            <div class="stat-card reveal">
                <div class="stat-icon primary"><i class="fa-solid fa-users"></i></div>
                <div class="stat-info">
                    <h4 class="dash-counter" data-target="1250">0</h4>
                    <p>Total Users</p>
                </div>
                <span class="stat-trend up"><i class="fa-solid fa-arrow-up"></i> 12%</span>
            </div>
            <div class="stat-card reveal reveal-delay-1">
                <div class="stat-icon secondary"><i class="fa-solid fa-graduation-cap"></i></div>
                <div class="stat-info">
                    <h4 class="dash-counter" data-target="320">0</h4>
                    <p>Active Courses</p>
                </div>
                <span class="stat-trend up"><i class="fa-solid fa-arrow-up"></i> 8%</span>
            </div>
            <div class="stat-card reveal reveal-delay-2">
                <div class="stat-icon accent"><i class="fa-solid fa-user-tie"></i></div>
                <div class="stat-info">
                    <h4 class="dash-counter" data-target="85">0</h4>
                    <p>Mentors</p>
                </div>
                <span class="stat-trend up"><i class="fa-solid fa-arrow-up"></i> 5%</span>
            </div>
            <div class="stat-card reveal reveal-delay-3">
                <div class="stat-icon warning-bg"><i class="fa-solid fa-circle-dollar"></i></div>
                <div class="stat-info">
                    <h4 class="dash-counter" data-target="45000">0</h4>
                    <p>Revenue ($)</p>
                </div>
                <span class="stat-trend down"><i class="fa-solid fa-arrow-down"></i> 3%</span>
            </div>
        </div>

        <div class="dash-grid-2" style="margin-bottom:var(--spacing-5);">
            <!-- Chart panel -->
            <div class="panel reveal">
                <div class="panel-header">
                    <h5><i class="fa-solid fa-chart-line" style="color:var(--primary);"></i> Revenue Overview</h5>
                    <select class="form-control" style="width:130px;padding:.4rem .8rem;font-size:.8rem;">
                        <option>Last 12 months</option>
                        <option>Last 6 months</option>
                        <option>Last 30 days</option>
                    </select>
                </div>
                <div class="panel-body">
                    <div class="chart-box" id="dashChart">
                        <div class="bar chart-bar" style="height:42%"></div>
                        <div class="bar chart-bar" style="height:58%"></div>
                        <div class="bar chart-bar" style="height:65%"></div>
                        <div class="bar chart-bar" style="height:48%"></div>
                        <div class="bar chart-bar" style="height:75%"></div>
                        <div class="bar chart-bar" style="height:82%"></div>
                        <div class="bar chart-bar" style="height:60%"></div>
                        <div class="bar chart-bar" style="height:90%"></div>
                        <div class="bar chart-bar" style="height:72%"></div>
                        <div class="bar chart-bar" style="height:68%"></div>
                        <div class="bar chart-bar" style="height:85%"></div>
                        <div class="bar chart-bar" style="height:95%"></div>
                    </div>
                </div>
            </div>

            <!-- Recent activity -->
            <div class="panel reveal reveal-delay-2">
                <div class="panel-header">
                    <h5><i class="fa-solid fa-clock-rotate-left" style="color:var(--secondary);"></i> Recent Activity</h5>
                    <a href="#" class="btn btn-sm btn-outline">View All</a>
                </div>
                <div class="panel-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon" style="background:var(--primary-soft);color:var(--primary);"><i class="fa-solid fa-user-plus"></i></div>
                            <div>
                                <h6>New fresher registered</h6>
                                <p>Sarah Johnson joined the platform</p>
                            </div>
                            <span class="activity-time">2m ago</span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon" style="background:var(--secondary-soft);color:var(--secondary-dark);"><i class="fa-solid fa-cart-shopping"></i></div>
                            <div>
                                <h6>New course purchase</h6>
                                <p>Advanced Data Science was purchased</p>
                            </div>
                            <span class="activity-time">18m ago</span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon" style="background:var(--accent-soft);color:var(--accent);"><i class="fa-solid fa-calendar-check"></i></div>
                            <div>
                                <h6>Session booked</h6>
                                <p>Mentor session scheduled for tomorrow</p>
                            </div>
                            <span class="activity-time">1h ago</span>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon" style="background:#fff3d6;color:#d1910a;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div>
                                <h6>Payment pending</h6>
                                <p>Course payment awaiting confirmation</p>
                            </div>
                            <span class="activity-time">3h ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent bookings table -->
        <div class="panel reveal">
            <div class="panel-header">
                <h5><i class="fa-solid fa-table-list" style="color:var(--primary);"></i> Recent Bookings</h5>
                <a href="bookings.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Mentor</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox" class="row-check"></td>
                            <td><div class="user-cell"><img src="../../assets/images/profile/avatar-1.svg" alt=""><strong>Sarah Johnson</strong></div></td>
                            <td>Data Science</td>
                            <td>Dr. Aisha Khan</td>
                            <td>Jan 12, 2025</td>
                            <td><span class="status-pill approved">Approved</span></td>
                            <td>
                                <div class="table-actions">
                                    <button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button>
                                    <button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="delete-btn" aria-label="Delete"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="row-check"></td>
                            <td><div class="user-cell"><img src="../../assets/images/profile/avatar-2.svg" alt=""><strong>Michael Chen</strong></div></td>
                            <td>Machine Learning</td>
                            <td>Prof. James Carter</td>
                            <td>Jan 11, 2025</td>
                            <td><span class="status-pill pending">Pending</span></td>
                            <td>
                                <div class="table-actions">
                                    <button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button>
                                    <button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="delete-btn" aria-label="Delete"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="row-check"></td>
                            <td><div class="user-cell"><img src="../../assets/images/profile/avatar-3.svg" alt=""><strong>Emily Davis</strong></div></td>
                            <td>Web Development</td>
                            <td>Robert Garcia</td>
                            <td>Jan 10, 2025</td>
                            <td><span class="status-pill completed">Completed</span></td>
                            <td>
                                <div class="table-actions">
                                    <button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button>
                                    <button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button>
                                    <button class="delete-btn" aria-label="Delete"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
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
