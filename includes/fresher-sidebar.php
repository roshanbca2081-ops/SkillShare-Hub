<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';

$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$user_id = getUserId();

global $pdo;

// Get unread counts
$unread_notifications = getUnreadNotifications($pdo, $user_id);
$unread_messages = getUnreadMessages($pdo, $user_id);
?>
<div class="sidebar bg-dark text-white min-vh-100 p-3">
    <h6 class="text-uppercase text-muted small px-3">Navigation</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/dashboard.php'); ?>">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'academic' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/academic/fiels.php'); ?>">
                <i class="fas fa-book me-2"></i> Academic Fields
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'mentor' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/mentor.php'); ?>">
                <i class="fas fa-chalkboard-teacher me-2"></i> Mentors
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'sessions' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/sessions/index.php'); ?>">
                <i class="fas fa-calendar-alt me-2"></i> Sessions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'learning' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/learning/my-course.php'); ?>">
                <i class="fas fa-book-open me-2"></i> My Courses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'booking' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/booking/index.php'); ?>">
                <i class="fas fa-calendar-check me-2"></i> My Bookings
                <?php 
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE fresher_id = ? AND status = 'pending'");
                $stmt->execute([$user_id]);
                $pending = $stmt->fetchColumn();
                if ($pending > 0): ?>
                    <span class="badge bg-warning"><?php echo $pending; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'assignment' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/assignment/index.php'); ?>">
                <i class="fas fa-tasks me-2"></i> Assignments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'resources' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/resources/index.php'); ?>">
                <i class="fas fa-folder-open me-2"></i> Resources
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'research' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/research/index.php'); ?>">
                <i class="fas fa-flask me-2"></i> Research
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'interview' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/interview/index.php'); ?>">
                <i class="fas fa-question-circle me-2"></i> Interview Prep
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'certificate' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/certificate/index.php'); ?>">
                <i class="fas fa-certificate me-2"></i> Certificates
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'message' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/message/index.php'); ?>">
                <i class="fas fa-envelope me-2"></i> Messages
                <?php if ($unread_messages > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'notification' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/notification/index.php'); ?>">
                <i class="fas fa-bell me-2"></i> Notifications
                <?php if ($unread_notifications > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_notifications; ?></span>
                <?php endif; ?>
            </a>
        </li>
        
        <li><hr class="bg-secondary"></li>
        
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'account' && $current_page == 'profile.php' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/account/profile.php'); ?>">
                <i class="fas fa-user me-2"></i> My Profile
            </a>
        </li>
        <li class="nav-item">
             <a class="nav-link text-white <?php echo $current_dir == 'setting' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/account/setting.php'); ?>">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'account' && $current_page == 'security.php' ? 'active' : ''; ?>" href="<?php echo appUrl('fresher/account/security.php'); ?>">
                <i class="fas fa-shield-alt me-2"></i> Security
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="<?php echo appUrl('logout.php'); ?>">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>
