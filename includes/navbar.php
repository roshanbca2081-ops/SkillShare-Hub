<?php 
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';
global $pdo;
?>
<nav class="navbar navbar-expand-lg navbar-glass sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo appUrl('index.php'); ?>">
            <i class="fas fa-graduation-cap"></i> SkillShare Hub
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo appUrl('index.php'); ?>"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo appUrl('about.php'); ?>"><i class="fas fa-circle-info"></i> About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo appUrl('contact.php'); ?>"><i class="fas fa-envelope"></i> Contact</a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars(getUserName()); ?>
                            <?php 
                            $unread = getUnreadNotifications($pdo, $_SESSION['user_id']);
                            if ($unread > 0): ?>
                                <span class="badge bg-danger"><?php echo $unread; ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (getUserRole() === 'fresher'): ?>
                                <li><a class="dropdown-item" href="<?php echo appUrl('fresher/dashboard.php'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo appUrl('fresher/learning/my-courses.php'); ?>"><i class="fas fa-book-open"></i> My Courses</a></li>
                                <li><a class="dropdown-item" href="<?php echo appUrl('fresher/sessions/my-sessions.php'); ?>"><i class="fas fa-calendar-alt"></i> My Sessions</a></li>
                                <li><a class="dropdown-item" href="<?php echo appUrl('fresher/message/index.php'); ?>"><i class="fas fa-envelope"></i> Messages</a></li>
                            <?php elseif (getUserRole() === 'mentor'): ?>
                                <li><a class="dropdown-item" href="<?php echo appUrl('mentor/dashboard.php'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo appUrl('mentor/my-course/index.php'); ?>"><i class="fas fa-book-open"></i> My Courses</a></li>
                                <li><a class="dropdown-item" href="<?php echo appUrl('mentor/session/index.php'); ?>"><i class="fas fa-calendar-alt"></i> Sessions</a></li>
                                <li><a class="dropdown-item" href="<?php echo appUrl('mentor/students.php'); ?>"><i class="fas fa-users"></i> Students</a></li>
                            <?php elseif (getUserRole() === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?php echo appUrl('admin/dashboard.php'); ?>"><i class="fas fa-tachometer-alt"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo appUrl('logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo appUrl('login.php'); ?>"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white" href="<?php echo appUrl('register.php'); ?>"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>