<?php
$adminScriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$adminPath = strpos($adminScriptPath, '/admin/') !== false
    ? ltrim(substr($adminScriptPath, strpos($adminScriptPath, '/admin/') + strlen('/admin/')), '/')
    : '';
$adminSection = strtok($adminPath, '/') ?: 'dashboard.php';
?>
<aside class="admin-sidebar">
    <a class="admin-brand" href="<?php echo appUrl('admin/dashboard.php'); ?>"><span class="mentor-brand-icon"><img src="<?php echo appUrl('assets/images/logo/skillshare-hub-mark.svg'); ?>" alt=""></span><span class="admin-brand-copy"><strong>SkillShare <em>Hub</em></strong><small>Learn · Connect · Grow</small></span></a>
    <p class="admin-label role-sidebar-label">ADMIN WORKSPACE</p>
    <a class="admin-link <?php echo $adminSection === 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/dashboard.php'); ?>" <?php echo $adminSection === 'dashboard.php' ? 'aria-current="page"' : ''; ?>><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
    <a class="admin-link <?php echo $adminSection === 'users' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/users/index.php'); ?>" <?php echo $adminSection === 'users' ? 'aria-current="page"' : ''; ?>><i class="fas fa-users"></i><span>Users</span></a>
    <a class="admin-link <?php echo $adminSection === 'academic-field' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/academic-field/index.php'); ?>" <?php echo $adminSection === 'academic-field' ? 'aria-current="page"' : ''; ?>><i class="fas fa-layer-group"></i><span>Academic Fields</span></a>
    <a class="admin-link <?php echo $adminSection === 'courses' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/courses/index.php'); ?>" <?php echo $adminSection === 'courses' ? 'aria-current="page"' : ''; ?>><i class="fas fa-book-open"></i><span>Courses</span></a>
    <a class="admin-link <?php echo $adminSection === 'session' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/session/index.php'); ?>" <?php echo $adminSection === 'session' ? 'aria-current="page"' : ''; ?>><i class="fas fa-calendar-days"></i><span>Sessions</span></a>
    <a class="admin-link <?php echo $adminSection === 'booking' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/booking/index.php'); ?>" <?php echo $adminSection === 'booking' ? 'aria-current="page"' : ''; ?>><i class="fas fa-ticket"></i><span>Bookings</span></a>
    <a class="admin-link <?php echo $adminSection === 'payment' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/payment/index.php'); ?>" <?php echo $adminSection === 'payment' ? 'aria-current="page"' : ''; ?>><i class="fas fa-credit-card"></i><span>Payments</span></a>
    <a class="admin-link <?php echo $adminSection === 'notification' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/notification/index.php'); ?>" <?php echo $adminSection === 'notification' ? 'aria-current="page"' : ''; ?>><i class="fas fa-bell"></i><span>Notifications</span></a>
    <a class="admin-link <?php echo $adminSection === 'reports' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/reports/index.php'); ?>" <?php echo $adminSection === 'reports' ? 'aria-current="page"' : ''; ?>><i class="fas fa-chart-pie"></i><span>Reports</span></a>
    <a class="admin-link <?php echo $adminSection === 'setting' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/setting/index.php'); ?>" <?php echo $adminSection === 'setting' ? 'aria-current="page"' : ''; ?>><i class="fas fa-gear"></i><span>Platform Settings</span></a>
    <p class="admin-label role-sidebar-label">ACCOUNT</p>
    <a class="admin-link <?php echo $adminSection === 'account' ? 'active' : ''; ?>" href="<?php echo appUrl('admin/account/settings.php'); ?>" <?php echo $adminSection === 'account' ? 'aria-current="page"' : ''; ?>><i class="fas fa-user-shield"></i><span>Account Settings</span></a>
    <a class="admin-link" href="<?php echo appUrl('index.php'); ?>"><i class="fas fa-arrow-up-right-from-square"></i><span>View site</span></a>
    <a class="admin-link admin-link-danger" href="<?php echo appUrl('logout.php'); ?>"><i class="fas fa-right-from-bracket"></i><span>Logout</span></a>
</aside>