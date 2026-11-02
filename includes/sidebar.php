<?php
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$fresherPath = strpos($scriptPath, '/fresher/') !== false
    ? ltrim(substr($scriptPath, strpos($scriptPath, '/fresher/') + strlen('/fresher/')), '/')
    : '';
$user_id = getUserId();

$activeSection = 'dashboard';
if (strpos($fresherPath, 'academic/') === 0) {
    $activeSection = 'academic';
} elseif (strpos($fresherPath, 'mentor/') === 0) {
    $activeSection = 'mentors';
} elseif ($fresherPath === 'courses.php') {
    $activeSection = 'courses';
} elseif ($fresherPath === 'learning/progress.php') {
    $activeSection = 'progress';
} elseif (strpos($fresherPath, 'sessions/') === 0 || $fresherPath === 'my_sessions.php') {
    $activeSection = 'sessions';
} elseif (strpos($fresherPath, 'booking/') === 0) {
    $activeSection = 'bookings';
} elseif (strpos($fresherPath, 'assignment/') === 0) {
    $activeSection = 'assignments';
} elseif (strpos($fresherPath, 'resources/') === 0) {
    $activeSection = 'resources';
} elseif (strpos($fresherPath, 'research/') === 0) {
    $activeSection = 'research';
} elseif (strpos($fresherPath, 'interview/') === 0) {
    $activeSection = 'interview';
} elseif (strpos($fresherPath, 'certificate/') === 0) {
    $activeSection = 'certificates';
} elseif (strpos($fresherPath, 'message/') === 0) {
    $activeSection = 'messages';
} elseif (strpos($fresherPath, 'notification/') === 0) {
    $activeSection = 'notifications';
} elseif (strpos($fresherPath, 'account/') === 0 || strpos($fresherPath, 'profile/') === 0) {
    if (strpos($fresherPath, 'security') !== false) {
        $activeSection = 'settings';
    } elseif (strpos($fresherPath, 'setting') !== false) {
        $activeSection = 'settings';
    } else {
        $activeSection = 'profile';
    }
} elseif (strpos($fresherPath, 'setting/') === 0) {
    $activeSection = 'settings';
} elseif ($fresherPath === 'my-courses.php' || strpos($fresherPath, 'learning/') === 0 || strpos($fresherPath, 'bookmark.php/') === 0) {
    $activeSection = 'my-courses';
} elseif (strpos($fresherPath, 'reviews/') === 0) {
    $activeSection = 'my-courses';
}

global $pdo;

// Get unread counts
$unread_notifications = getUnreadNotifications($pdo, $user_id);
$unread_messages = getUnreadMessages($pdo, $user_id);

// Get pending bookings count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE fresher_id = ? AND status = 'pending'");
$stmt->execute([$user_id]);
$pending_bookings = $stmt->fetchColumn();

// Get pending assignments count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM assignments a 
                       JOIN enrollments e ON a.course_id = e.course_id 
                       WHERE e.fresher_id = ? AND a.due_date > NOW() 
                       AND NOT EXISTS (SELECT 1 FROM assignment_submissions s WHERE s.assignment_id = a.id AND s.fresher_id = ?)");
$stmt->execute([$user_id, $user_id]);
$pending_assignments = $stmt->fetchColumn();
?>
<div class="sidebar role-section-sidebar bg-dark text-white p-3" id="fresherSidebar" aria-label="Fresher navigation">
    <a class="fresher-brand" href="<?php echo appUrl('fresher/dashboard.php'); ?>" aria-label="SkillShare Hub dashboard">
        <span class="fresher-brand-mark"><img src="<?php echo appUrl('assets/images/logo/skillshare-hub-mark.svg'); ?>" alt=""></span>
        <span class="fresher-brand-copy"><strong>SkillShare <span>Hub</span></strong><small>Learn · Connect · Grow</small></span>
    </a>
    <div class="fresher-sidebar-heading"><h6 class="text-uppercase text-muted small px-3 mb-2">Student workspace</h6></div>
    <ul class="nav flex-column fresher-sidebar-links">
        <li class="fresher-nav-group-label">Workspace</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'dashboard' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/dashboard.php'); ?>" <?php echo $activeSection === 'dashboard' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="fresher-nav-group-label">Learning</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'progress' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/learning/progress.php'); ?>" <?php echo $activeSection === 'progress' ? 'aria-current="page"' : ''; ?>><i class="fas fa-book-open me-2"></i> My Learning</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'profile' && (strpos($fresherPath, 'account/') === 0) ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/account/profile.php'); ?>" <?php echo $activeSection === 'profile' && strpos($fresherPath, 'account/') === 0 ? 'aria-current="page"' : ''; ?>><i class="fas fa-user-graduate me-2"></i> Academic Profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'academic' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/academic/fiels.php'); ?>" <?php echo $activeSection === 'academic' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-layer-group me-2"></i> Academic Fields
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'courses' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/courses.php'); ?>" <?php echo $activeSection === 'courses' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-book me-2"></i> Courses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'my-courses' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/learning/my-course.php'); ?>" <?php echo $activeSection === 'my-courses' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-circle-play me-2"></i> My Courses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'assignments' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/assignment/index.php'); ?>" <?php echo $activeSection === 'assignments' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-list-check me-2"></i> Assignments
                <?php if ($pending_assignments > 0): ?><span class="badge bg-danger"><?php echo $pending_assignments; ?></span><?php endif; ?>
            </a>
        </li>
        <li class="fresher-nav-group-label">Connect</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'mentors' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/mentor/index.php'); ?>" <?php echo $activeSection === 'mentors' ? 'aria-current="page"' : ''; ?>><i class="fas fa-user-group me-2"></i> Find Mentors</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'sessions' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/sessions/index.php'); ?>" <?php echo $activeSection === 'sessions' ? 'aria-current="page"' : ''; ?>><i class="fas fa-calendar-days me-2"></i> Sessions &amp; Bookings</a>
        </li>
        <li class="nav-item fresher-nav-subitem">
            <a class="nav-link text-white <?php echo $activeSection === 'bookings' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/booking/index.php'); ?>" <?php echo $activeSection === 'bookings' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-calendar-check me-2"></i> My Bookings
                <?php if ($pending_bookings > 0): ?><span class="badge bg-warning"><?php echo $pending_bookings; ?></span><?php endif; ?>
            </a>
        </li>
        <li class="fresher-nav-group-label">Resources</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'resources' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/resources/index.php'); ?>" <?php echo $activeSection === 'resources' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-folder-open me-2"></i> All Resources
            </a>
        </li>
        <li class="nav-item fresher-nav-subitem">
            <a class="nav-link text-white <?php echo strpos($fresherPath, 'research/') === 0 ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/research/index.php'); ?>" <?php echo strpos($fresherPath, 'research/') === 0 ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-flask me-2"></i> Research
            </a>
        </li>
        <li class="nav-item fresher-nav-subitem">
            <a class="nav-link text-white <?php echo strpos($fresherPath, 'interview/') === 0 ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/interview/index.php'); ?>" <?php echo strpos($fresherPath, 'interview/') === 0 ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-clipboard-question me-2"></i> Interview Prep
            </a>
        </li>
        <li class="fresher-nav-group-label">Communication</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'certificates' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/certificate/index.php'); ?>" <?php echo $activeSection === 'certificates' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-certificate me-2"></i> Certificates
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'messages' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/message/index.php'); ?>" <?php echo $activeSection === 'messages' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-envelope me-2"></i> Messages
                <?php if ($unread_messages > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'notifications' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/notification/index.php'); ?>" <?php echo $activeSection === 'notifications' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-bell me-2"></i> Notifications
                <?php if ($unread_notifications > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_notifications; ?></span>
                <?php endif; ?>
            </a>
        </li>
        
        <li class="fresher-nav-group-label">Account</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'profile' && strpos($fresherPath, 'profile/') === 0 ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/profile/index.php'); ?>" <?php echo $activeSection === 'profile' && strpos($fresherPath, 'profile/') === 0 ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-user me-2"></i> My Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $activeSection === 'settings' && strpos($fresherPath, 'security') === false ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/setting/index.php'); ?>" <?php echo $activeSection === 'settings' && strpos($fresherPath, 'security') === false ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </li>
        <li class="nav-item fresher-nav-subitem">
            <a class="nav-link text-white <?php echo $activeSection === 'settings' && strpos($fresherPath, 'security') !== false ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/account/security.php'); ?>" <?php echo $activeSection === 'settings' && strpos($fresherPath, 'security') !== false ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-shield-alt me-2"></i> Security
            </a>
        </li>
    </ul>
    <div class="fresher-sidebar-footer">
        <a class="nav-link text-white" href="<?php echo appUrl('logout.php'); ?>">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </div>
</div>
