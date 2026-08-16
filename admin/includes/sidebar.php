<?php
/**
 * Admin Sidebar Include
 */
$sidebar_items = admin_sidebar_items();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-brand">
        <img src="<?php echo BASE_URL; ?>frontend/assets/images/logo/skillshare hub.png" alt="SkillShare Hub Logo" style="height:32px; width:auto; object-fit:contain;">
        <h4>SkillShare <span>Hub</span></h4>
    </div>
    <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
    <nav class="admin-sidebar-nav">
        <?php foreach ($sidebar_items as $group => $items): ?>
            <p class="menu-label"><?php echo htmlspecialchars($group); ?></p>
            <?php foreach ($items as $item): ?>
                <?php 
                $isActive = false;
                if ($item['label'] === 'Dashboard' && $currentPage === 'index.php') $isActive = true;
                elseif ($item['label'] === 'Users' && $currentPage === 'users.php') $isActive = true;
                elseif ($item['label'] === 'Mentors' && $currentPage === 'mentors.php') $isActive = true;
                elseif ($item['label'] === 'Freshers' && $currentPage === 'freshers.php') $isActive = true;
                elseif ($item['label'] === 'Academic Fields' && $currentPage === 'academic-fields.php') $isActive = true;
                elseif ($item['label'] === 'Courses' && $currentPage === 'courses.php') $isActive = true;
                elseif ($item['label'] === 'Bookings' && $currentPage === 'bookings.php') $isActive = true;
                elseif ($item['label'] === 'Sessions' && $currentPage === 'sessions.php') $isActive = true;
                elseif ($item['label'] === 'Assignments' && $currentPage === 'assignments.php') $isActive = true;
                elseif ($item['label'] === 'Research' && $currentPage === 'research.php') $isActive = true;
                elseif ($item['label'] === 'Payments' && $currentPage === 'payments.php') $isActive = true;
                elseif ($item['label'] === 'Notifications' && $currentPage === 'notifications.php') $isActive = true;
                elseif ($item['label'] === 'Messages' && $currentPage === 'messages.php') $isActive = true;
                elseif ($item['label'] === 'Certificates' && $currentPage === 'certificates.php') $isActive = true;
                elseif ($item['label'] === 'Interview Prep' && $currentPage === 'interview.php') $isActive = true;
                elseif ($item['label'] === 'Feedback' && $currentPage === 'feedback.php') $isActive = true;
                elseif ($item['label'] === 'Reports' && $currentPage === 'reports.php') $isActive = true;
                elseif ($item['label'] === 'Profile' && $currentPage === 'profile.php') $isActive = true;
                elseif ($item['label'] === 'Settings' && $currentPage === 'settings.php') $isActive = true;
                elseif ($item['label'] === 'Logout' && $currentPage === 'logout.php') $isActive = true;
                ?>
                <a href="<?php echo $item['link']; ?>" class="admin-nav-item <?php echo $isActive ? 'active' : ''; ?>">
                    <i class="fas <?php echo $item['icon']; ?>"></i>
                    <?php echo htmlspecialchars($item['label']); ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>
</aside>
