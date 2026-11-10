<?php 
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';
global $pdo;
$currentScript = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isPublicMentorDirectory = strpos($currentScript, '/public/mentor.php') !== false;
$isLandingPage = in_array($currentScript, [appUrl('index.php'), appUrl('about.php'), appUrl('contact.php')], true);
$landingActivePage = basename($currentScript, '.php');
$isAccountSectionPage = strpos($currentScript, '/fresher/') !== false || strpos($currentScript, '/mentor/') !== false;
$showSectionBack = isLoggedIn() && ($isAccountSectionPage || $isPublicMentorDirectory);
$showFresherMenuToggle = isLoggedIn() && getUserRole() === 'fresher' && strpos($currentScript, '/fresher/') !== false;
$hasFixedRoleSidebar = isLoggedIn() && $isAccountSectionPage;
$isMentorSection = isLoggedIn() && getUserRole() === 'mentor' && strpos($currentScript, '/mentor/') !== false;
$dashboardPath = getUserRole() === 'mentor'
    ? 'mentor/dashboard.php'
    : (getUserRole() === 'admin' ? 'admin/dashboard.php' : 'fresher/dashboard.php');
$navbarAvatarUrl = getAvatar([]);
if (isLoggedIn() && isset($pdo) && $pdo instanceof PDO) {
    $avatarStmt = $pdo->prepare('SELECT avatar FROM users WHERE id = ? LIMIT 1');
    $avatarStmt->execute([getUserId()]);
    $navbarAvatarUrl = getAvatar($avatarStmt->fetch() ?: []);
}

// Fresher screens use the dedicated app shell and sidebar, not the public navbar.
if ($showFresherMenuToggle): ?>
    <button type="button" class="fresher-mobile-menu-toggle" id="fresherSidebarToggle" aria-controls="fresherSidebar" aria-expanded="false" aria-label="Open student navigation">
        <i class="fas fa-bars" aria-hidden="true"></i><span class="visually-hidden">Menu</span>
    </button>
    <button type="button" class="fresher-sidebar-backdrop" data-fresher-sidebar-close aria-label="Close student navigation"></button>
    <?php return; ?>
<?php endif; ?>
<?php if ($isLandingPage): ?>
<nav class="navbar navbar-expand-lg navbar-glass landing-navbar" aria-label="Main navigation">
    <div class="container">
        <a class="navbar-brand landing-brand" href="<?php echo appUrl('index.php'); ?>" aria-label="SkillShare Hub home">
            <span class="landing-brand-icon"><img src="<?php echo appUrl('assets/images/logo/skillshare-hub-mark.svg'); ?>" alt=""></span>
            <span class="landing-brand-copy"><strong>SkillShare <em>Hub</em></strong><small>Learn · Connect · Grow</small></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbarContent" aria-controls="landingNavbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNavbarContent">
            <ul class="navbar-nav landing-main-links">
                <li class="nav-item"><a class="nav-link <?php echo $landingActivePage === 'index' ? 'active' : ''; ?>" <?php echo $landingActivePage === 'index' ? 'aria-current="page"' : ''; ?> href="<?php echo appUrl('index.php'); ?>"><i class="fas fa-house" aria-hidden="true"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $landingActivePage === 'about' ? 'active' : ''; ?>" <?php echo $landingActivePage === 'about' ? 'aria-current="page"' : ''; ?> href="<?php echo appUrl('about.php'); ?>"><i class="fas fa-circle-info" aria-hidden="true"></i> About</a></li>
                <li class="nav-item"><a class="nav-link <?php echo $landingActivePage === 'contact' ? 'active' : ''; ?>" <?php echo $landingActivePage === 'contact' ? 'aria-current="page"' : ''; ?> href="<?php echo appUrl('contact.php'); ?>"><i class="fas fa-envelope" aria-hidden="true"></i> Contact</a></li>
            </ul>
            <div class="landing-nav-actions">
                <?php if (!isLoggedIn()): ?><div class="theme-picker dropdown">
                    <button class="theme-toggle-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Theme switcher">
                        <i class="fas fa-circle-half-stroke" aria-hidden="true"></i>
                        <span class="theme-label">System</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end theme-menu">
                        <li><button type="button" class="dropdown-item theme-option" data-theme-option="light"><i class="fas fa-sun"></i> Light</button></li>
                        <li><button type="button" class="dropdown-item theme-option" data-theme-option="dark"><i class="fas fa-moon"></i> Dark</button></li>
                        <li><button type="button" class="dropdown-item theme-option" data-theme-option="system"><i class="fas fa-desktop"></i> System</button></li>
                    </ul>
                </div><?php endif; ?>
                <?php if (isLoggedIn()): ?>
                    <?php $landingDashboard = isMentor() ? 'mentor/dashboard.php' : (isAdmin() ? 'admin/dashboard.php' : 'fresher/dashboard.php'); ?>
                    <a class="landing-login-button" href="<?php echo appUrl($landingDashboard); ?>"><img src="<?php echo $navbarAvatarUrl; ?>" alt="" width="24" height="24" style="border-radius:50%;object-fit:cover;"> Dashboard</a>
                    <a class="landing-register-button" href="<?php echo appUrl('logout.php'); ?>"><i class="fas fa-right-from-bracket" aria-hidden="true"></i> Logout</a>
                <?php else: ?>
                    <a class="landing-login-button" href="<?php echo appUrl('login.php'); ?>"><i class="fas fa-user" aria-hidden="true"></i> Login</a>
                    <a class="landing-register-button" href="<?php echo appUrl('register.php'); ?>"><i class="fas fa-user-group" aria-hidden="true"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<?php return; ?>
<?php endif; ?>
<nav class="navbar navbar-expand-lg navbar-glass sticky-top<?php echo $hasFixedRoleSidebar ? ' role-section-navbar' : ''; ?>">
    <div class="container">
        <a class="navbar-brand" href="<?php echo appUrl('index.php'); ?>">
            <img class="site-brand-mark" src="<?php echo appUrl('assets/images/logo/skillshare-hub-mark.svg'); ?>" alt=""> SkillShare Hub
        </a>
        <?php if ($showFresherMenuToggle): ?>
            <button type="button" class="btn btn-outline-primary fresher-mobile-menu-toggle" id="fresherSidebarToggle" aria-controls="fresherSidebar" aria-expanded="false" aria-label="Open student navigation">
                <i class="fas fa-bars" aria-hidden="true"></i><span class="visually-hidden">Menu</span>
            </button>
        <?php endif; ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="<?php echo appUrl('index.php'); ?>"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo appUrl('about.php'); ?>"><i class="fas fa-circle-info"></i> About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo appUrl('contact.php'); ?>"><i class="fas fa-envelope"></i> Contact</a></li>
                <?php if (!isLoggedIn()): ?><li class="nav-item theme-picker theme-picker-inline dropdown">
                    <button class="theme-toggle-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Theme switcher">
                        <i class="fas fa-circle-half-stroke" aria-hidden="true"></i>
                        <span class="theme-label">System</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end theme-menu">
                        <li><button type="button" class="dropdown-item theme-option" data-theme-option="light"><i class="fas fa-sun"></i> Light</button></li>
                        <li><button type="button" class="dropdown-item theme-option" data-theme-option="dark"><i class="fas fa-moon"></i> Dark</button></li>
                        <li><button type="button" class="dropdown-item theme-option" data-theme-option="system"><i class="fas fa-desktop"></i> System</button></li>
                    </ul>
                </li><?php endif; ?>
                <?php if ($isMentorSection): ?>
                    <li class="nav-item role-nav-back-item">
                        <a class="role-topbar-back" href="<?php echo appUrl('mentor/dashboard.php'); ?>" onclick="if (window.history.length > 1) { window.history.back(); return false; }" aria-label="Go to previous page"><i class="fas fa-arrow-left" aria-hidden="true"></i><span>Back</span></a>
                    </li>
                <?php endif; ?>
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="<?php echo $navbarAvatarUrl; ?>" alt="" width="28" height="28" style="border-radius:50%;object-fit:cover;vertical-align:middle;"> <?php echo htmlspecialchars(getUserName()); ?>
                            <?php 
                            $unread = getUnreadNotifications($pdo, $_SESSION['user_id']);
                            if ($unread > 0): ?>
                                <span class="badge bg-danger"><?php echo $unread; ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (getUserRole() === 'fresher'): ?>
                                <li><a class="dropdown-item" href="<?php echo appUrl('fresher/dashboard.php'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo appUrl('fresher/learning/my-course.php'); ?>"><i class="fas fa-book-open"></i> My Courses</a></li>
                                <li><a class="dropdown-item" href="<?php echo appUrl('fresher/sessions/my-session.php'); ?>"><i class="fas fa-calendar-alt"></i> My Sessions</a></li>
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
<?php if ($showFresherMenuToggle): ?>
    <button type="button" class="fresher-sidebar-backdrop" data-fresher-sidebar-close aria-label="Close student navigation"></button>
<?php endif; ?>
<?php if ($showSectionBack && !$isMentorSection): ?>
    <a class="btn btn-outline-primary btn-sm" href="<?php echo appUrl($dashboardPath); ?>"
       onclick="if (window.history.length > 1) { window.history.back(); return false; }"
       aria-label="Go to the previous page" title="Go back"
       style="position:fixed;top:110px;right:24px;z-index:1040;box-shadow:0 4px 14px rgba(31,41,75,.12);">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
<?php endif; ?>