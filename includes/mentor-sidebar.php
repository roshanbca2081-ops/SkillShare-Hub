<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';

$mentorScriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$mentorPath = strpos($mentorScriptPath, '/mentor/') !== false
    ? ltrim(substr($mentorScriptPath, strpos($mentorScriptPath, '/mentor/') + strlen('/mentor/')), '/')
    : '';
$current_page = basename($mentorPath);
$current_dir = basename(dirname($mentorPath));
$mentorSection = 'dashboard';
if (strpos($mentorPath, 'my-course/') === 0) $mentorSection = 'courses';
elseif ($mentorPath === 'students.php') $mentorSection = 'students';
elseif (strpos($mentorPath, 'booking/') === 0) $mentorSection = 'bookings';
elseif (strpos($mentorPath, 'session/') === 0) $mentorSection = 'sessions';
elseif (strpos($mentorPath, 'assignment/') === 0) $mentorSection = 'assignments';
elseif (strpos($mentorPath, 'resources/') === 0) $mentorSection = 'resources';
elseif (strpos($mentorPath, 'research/') === 0) $mentorSection = 'research';
elseif (strpos($mentorPath, 'interview/') === 0) $mentorSection = 'interview';
elseif (strpos($mentorPath, 'certificate/') === 0) $mentorSection = 'certificates';
elseif (strpos($mentorPath, 'message/') === 0) $mentorSection = 'messages';
elseif ($mentorPath === 'notification.php') $mentorSection = 'notifications';
elseif (strpos($mentorPath, 'account/') === 0) $mentorSection = strpos($mentorPath, 'security') !== false || strpos($mentorPath, 'setting') !== false ? 'settings' : 'profile';
$mentor_id = getUserId();

global $pdo;

// Get unread counts
$unread_notifications = getUnreadNotifications($pdo, $mentor_id);
$unread_messages = getUnreadMessages($pdo, $mentor_id);

// Get pending bookings count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings b JOIN sessions s ON b.session_id = s.id WHERE s.mentor_id = ? AND b.status = 'pending'");
$stmt->execute([$mentor_id]);
$pending_bookings = $stmt->fetchColumn();
?>
<div class="sidebar role-section-sidebar mentor-role-sidebar bg-dark text-white p-3">
    <a class="mentor-sidebar-brand" href="<?php echo appUrl('mentor/dashboard.php'); ?>">
        <span class="mentor-brand-icon"><img src="<?php echo appUrl('assets/images/logo/skillshare-hub-mark.svg'); ?>" alt=""></span>
        <span><strong>SkillShare <em>Hub</em></strong><small>Learn · Connect · Grow</small></span>
    </a>
    <h6 class="role-sidebar-label">MENTOR WORKSPACE</h6>
    <ul class="nav flex-column role-sidebar-links">
        <li class="mentor-nav-group-label">Workspace</li>
        <li class="nav-item">
            <a class="nav-link text-white" href="<?php echo appUrl('index.php'); ?>">
                <i class="fas fa-house me-2"></i> Main Page
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'dashboard' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/dashboard.php'); ?>" <?php echo $mentorSection === 'dashboard' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="mentor-nav-group-label">Learning</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'courses' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/my-course/index.php'); ?>" <?php echo $mentorSection === 'courses' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-book-open me-2"></i> My Courses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'students' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/students.php'); ?>" <?php echo $mentorSection === 'students' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-users me-2"></i> Students
            </a>
        </li>
        <li class="mentor-nav-group-label">Sessions &amp; Work</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'sessions' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/session/index.php'); ?>" <?php echo $mentorSection === 'sessions' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-calendar-days me-2"></i> Sessions &amp; Bookings
            </a>
        </li>
        <li class="nav-item mentor-nav-subitem">
            <a class="nav-link text-white <?php echo $mentorSection === 'bookings' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/booking/index.php'); ?>" <?php echo $mentorSection === 'bookings' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-calendar-check me-2"></i> Bookings
                <?php if ($pending_bookings > 0): ?>
                    <span class="badge bg-warning"><?php echo $pending_bookings; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'assignments' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/assignment/index.php'); ?>" <?php echo $mentorSection === 'assignments' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-tasks me-2"></i> Assignments
            </a>
        </li>
        <li class="mentor-nav-group-label">Resources</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'resources' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/resources/index.php'); ?>" <?php echo $mentorSection === 'resources' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-folder-open me-2"></i> Resources
            </a>
        </li>
        <li class="nav-item mentor-nav-subitem">
            <a class="nav-link text-white <?php echo $mentorSection === 'research' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/research/index.php'); ?>" <?php echo $mentorSection === 'research' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-flask me-2"></i> Research
            </a>
        </li>
        <li class="nav-item mentor-nav-subitem">
            <a class="nav-link text-white <?php echo $mentorSection === 'interview' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/interview/index.php'); ?>" <?php echo $mentorSection === 'interview' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-question-circle me-2"></i> Interview Questions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'certificates' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/certificate/index.php'); ?>" <?php echo $mentorSection === 'certificates' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-certificate me-2"></i> Certificates
            </a>
        </li>
        <li class="mentor-nav-group-label">Communication</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'messages' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/message/index.php'); ?>" <?php echo $mentorSection === 'messages' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-envelope me-2"></i> Messages
                <?php if ($unread_messages > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'notifications' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/notification.php'); ?>" <?php echo $mentorSection === 'notifications' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-bell me-2"></i> Notifications
                <?php if ($unread_notifications > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_notifications; ?></span>
                <?php endif; ?>
            </a>
        </li>
        
        <li class="mentor-nav-group-label">Account</li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'profile' ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/account/profile.php'); ?>" <?php echo $mentorSection === 'profile' ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-user-graduate me-2"></i> Academic Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $mentorSection === 'settings' && strpos($mentorPath, 'security') === false ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/account/setting.php'); ?>" <?php echo $mentorSection === 'settings' && strpos($mentorPath, 'security') === false ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </li>
        <li class="nav-item mentor-nav-subitem">
            <a class="nav-link text-white <?php echo $mentorSection === 'settings' && strpos($mentorPath, 'security') !== false ? 'active' : ''; ?>" href="<?php echo appUrl('mentor/account/security.php'); ?>" <?php echo $mentorSection === 'settings' && strpos($mentorPath, 'security') !== false ? 'aria-current="page"' : ''; ?>>
                <i class="fas fa-shield-alt me-2"></i> Security
            </a>
        </li>
    </ul>
    <div class="role-sidebar-footer">
        <a class="nav-link text-white" href="<?php echo appUrl('logout.php'); ?>"><i class="fas fa-right-from-bracket" aria-hidden="true"></i><span>Logout</span></a>
    </div>
</div>