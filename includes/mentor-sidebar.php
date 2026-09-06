<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';

$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
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
<div class="sidebar bg-dark text-white min-vh-100 p-3">
    <h6 class="text-uppercase text-muted small px-3">Mentor Panel</h6>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="../dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'my-courses' ? 'active' : ''; ?>" href="../my-courses/index.php">
                <i class="fas fa-book-open me-2"></i> My Courses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_page == 'students.php' ? 'active' : ''; ?>" href="../students.php">
                <i class="fas fa-users me-2"></i> Students
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'bookings' ? 'active' : ''; ?>" href="../bookings/index.php">
                <i class="fas fa-calendar-check me-2"></i> Bookings
                <?php if ($pending_bookings > 0): ?>
                    <span class="badge bg-warning"><?php echo $pending_bookings; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'sessions' ? 'active' : ''; ?>" href="../sessions/index.php">
                <i class="fas fa-calendar-alt me-2"></i> Sessions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'assignments' ? 'active' : ''; ?>" href="../assignments/index.php">
                <i class="fas fa-tasks me-2"></i> Assignments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'resources' ? 'active' : ''; ?>" href="../resources/index.php">
                <i class="fas fa-folder-open me-2"></i> Resources
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'research' ? 'active' : ''; ?>" href="../research/index.php">
                <i class="fas fa-flask me-2"></i> Research
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'interview' ? 'active' : ''; ?>" href="../interview/index.php">
                <i class="fas fa-question-circle me-2"></i> Interview Questions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'certificates' ? 'active' : ''; ?>" href="../certificates/index.php">
                <i class="fas fa-certificate me-2"></i> Certificates
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'messages' ? 'active' : ''; ?>" href="../messages/index.php">
                <i class="fas fa-envelope me-2"></i> Messages
                <?php if ($unread_messages > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_messages; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_page == 'notifications.php' ? 'active' : ''; ?>" href="../notifications.php">
                <i class="fas fa-bell me-2"></i> Notifications
                <?php if ($unread_notifications > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_notifications; ?></span>
                <?php endif; ?>
            </a>
        </li>
        
        <li><hr class="bg-secondary"></li>
        
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'account' && $current_page == 'profile.php' ? 'active' : ''; ?>" href="../account/profile.php">
                <i class="fas fa-user me-2"></i> Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'account' && $current_page == 'settings.php' ? 'active' : ''; ?>" href="../account/settings.php">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $current_dir == 'account' && $current_page == 'security.php' ? 'active' : ''; ?>" href="../account/security.php">
                <i class="fas fa-shield-alt me-2"></i> Security
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="../../logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>