<?php
// Auto-detect asset and root paths
if (!isset($assetBase) || !isset($rootPrefix)) {
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    $scriptDir = dirname($scriptPath);
    $relativeParts = explode('/', trim($scriptDir, '/'));
    $depthCount = 0;
    foreach ($relativeParts as $part) {
        if ($part !== '' && $part !== 'SkillShare Hub' && $part !== 'xampp' && $part !== 'htdocs') {
            $depthCount++;
        }
    }
    $assetBase = $depthCount === 0 ? 'assets/' : str_repeat('../', $depthCount) . 'assets/';
    $rootPrefix = $depthCount > 0 ? str_repeat('../', $depthCount) : './';
}

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$currentDir = basename(dirname($_SERVER['SCRIPT_NAME']));
$isLoggedIn = is_logged_in();
$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? '';
$notifCount = 0;

if ($isLoggedIn && isset($_SESSION['user_id'])) {
    global $conn;
    if ($conn) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        if ($stmt) {
            $uid = $_SESSION['user_id'];
            $stmt->bind_param('i', $uid);
            $stmt->execute();
            $stmt->bind_result($notifCount);
            $stmt->fetch();
            $stmt->close();
        }
    }
}
?>
<nav class="navbar" data-mobile-open="false">
  <div class="navbar__inner">
    <a class="brand" href="<?php echo $rootPrefix; ?>index.php" aria-label="ShareSkill Hub">
      <span class="site-logo" aria-hidden="true"></span>
    </a>
    <form class="search" action="<?php echo $rootPrefix; ?>course.php" method="get" aria-label="Site search">
      <div class="input-icon">
        <i class="fa-solid fa-search"></i>
        <input type="search" name="q" placeholder="Search courses, skills..." aria-label="Search" class="form-control" />
      </div>
    </form>
    <div class="nav" role="navigation" aria-label="Primary">
      <a class="nav__link<?php echo ($currentPage==='index.php'&&$currentDir==='.')?' nav__link--active':''; ?>" href="<?php echo $rootPrefix; ?>index.php"><i class="fa-solid fa-house"></i> Home</a>
      <a class="nav__link" href="<?php echo $rootPrefix; ?>index.php#academic-fields"><i class="fa-solid fa-layer-group"></i> Fields</a>
      <a class="nav__link<?php echo $currentPage==='course.php'?' nav__link--active':''; ?>" href="<?php echo $rootPrefix; ?>course.php"><i class="fa-solid fa-book-open-reader"></i> Courses</a>
      <a class="nav__link<?php echo $currentPage==='mentor.php'?' nav__link--active':''; ?>" href="<?php echo $rootPrefix; ?>mentor.php"><i class="fa-solid fa-user-tie"></i> Mentors</a>
      <a class="nav__link" href="<?php echo $rootPrefix; ?>fresher/mentorship/book.php"><i class="fa-solid fa-video"></i> Sessions</a>
      <a class="nav__link" href="<?php echo $rootPrefix; ?>fresher/assignments/index.php"><i class="fa-solid fa-file-pen"></i> Assignments</a>
      <a class="nav__link" href="<?php echo $rootPrefix; ?>fresher/research/index.php"><i class="fa-solid fa-microscope"></i> Research</a>
      <a class="nav__link" href="<?php echo $rootPrefix; ?>fresher/placement/index.php"><i class="fa-solid fa-briefcase"></i> Placement</a>
      <a class="nav__link" href="<?php echo $rootPrefix; ?>fresher/certificates/index.php"><i class="fa-solid fa-award"></i> Certificates</a>
      <a class="nav__link<?php echo $currentPage==='about.php'?' nav__link--active':''; ?>" href="<?php echo $rootPrefix; ?>about.php"><i class="fa-solid fa-circle-info"></i> About</a>
      <a class="nav__link<?php echo $currentPage==='contact.php'?' nav__link--active':''; ?>" href="<?php echo $rootPrefix; ?>contact.php"><i class="fa-solid fa-envelope"></i> Contact</a>
      <a class="nav__link nav__link--notif<?php echo $currentPage==='notifications.php'?' nav__link--active':''; ?>" href="<?php echo $rootPrefix; ?>notifications.php">
        <i class="fa-solid fa-bell"></i>
        <?php if ($notifCount > 0): ?><span class="notif-badge"><?php echo $notifCount > 99 ? '99+' : $notifCount; ?></span><?php endif; ?>
      </a>
    </div>
    <div class="navbar__right">
      <button class="btn btn--ghost btn-sm" type="button" data-theme-toggle aria-label="Toggle dark mode"><i class="fa-solid fa-moon"></i></button>
      <?php if ($isLoggedIn): ?>
        <div class="profile" data-profile>
          <button class="avatar-btn" type="button" data-profile-trigger aria-label="Open profile menu">
            <div class="avatar"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
          </button>
          <div class="dropdown" data-open="false">
            <div class="dropdown-header"><strong><?php echo e($userName); ?></strong><span class="dropdown-role"><?php echo e(ucfirst($userRole)); ?></span></div>
            <a href="<?php echo $rootPrefix; ?>dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            <a href="<?php echo $rootPrefix; ?>settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
            <hr />
            <a href="<?php echo $rootPrefix; ?>logout.php" style="color:var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Sign out</a>
          </div>
      <?php else: ?>
        <a class="btn btn--outline btn-sm" href="<?php echo $rootPrefix; ?>login.php"><i class="fa-regular fa-right-to-bracket"></i> Login</a>
        <a class="btn btn--primary btn-sm" href="<?php echo $rootPrefix; ?>register.php"><i class="fa-regular fa-user-plus"></i> Register</a>
      <?php endif; ?>
      <button class="hamburger" type="button" aria-label="Open menu" data-hamburger>
        <div class="hamburger__lines" aria-hidden="true"><span></span><span></span><span></span></div>
      </button>
    </div>
  <div class="mobile-panel" aria-hidden="true">
    <div class="mobile-panel__search">
      <form action="<?php echo $rootPrefix; ?>course.php" method="get">
        <div class="input-icon">
          <i class="fa-solid fa-search"></i>
          <input type="search" name="q" placeholder="Search courses, skills..." class="form-control" />
        </div>
      </form>
    </div>
    <div class="mobile-panel__grid">
      <a class="mobile-panel__link<?php echo $currentPage==='index.php'?' mobile-panel__link--active':''; ?>" href="<?php echo $rootPrefix; ?>index.php"><i class="fa-solid fa-house"></i> Home</a>
      <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>index.php#academic-fields"><i class="fa-solid fa-layer-group"></i> Fields</a>
      <a class="mobile-panel__link<?php echo $currentPage==='course.php'?' mobile-panel__link--active':''; ?>" href="<?php echo $rootPrefix; ?>course.php"><i class="fa-solid fa-book-open-reader"></i> Courses</a>
      <a class="mobile-panel__link<?php echo $currentPage==='mentor.php'?' mobile-panel__link--active':''; ?>" href="<?php echo $rootPrefix; ?>mentor.php"><i class="fa-solid fa-user-tie"></i> Mentors</a>
      <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>fresher/mentorship/book.php"><i class="fa-solid fa-video"></i> Sessions</a>
      <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>fresher/assignments/index.php"><i class="fa-solid fa-file-pen"></i> Assignments</a>
      <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>fresher/research/index.php"><i class="fa-solid fa-microscope"></i> Research</a>
      <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>fresher/placement/index.php"><i class="fa-solid fa-briefcase"></i> Placement</a>
      <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>fresher/certificates/index.php"><i class="fa-solid fa-award"></i> Certificates</a>
      <a class="mobile-panel__link<?php echo $currentPage==='about.php'?' mobile-panel__link--active':''; ?>" href="<?php echo $rootPrefix; ?>about.php"><i class="fa-solid fa-circle-info"></i> About</a>
      <a class="mobile-panel__link<?php echo $currentPage==='contact.php'?' mobile-panel__link--active':''; ?>" href="<?php echo $rootPrefix; ?>contact.php"><i class="fa-solid fa-envelope"></i> Contact</a>
      <a class="mobile-panel__link<?php echo $currentPage==='notifications.php'?' mobile-panel__link--active':''; ?>" href="<?php echo $rootPrefix; ?>notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a>
    </div>
    <div class="mobile-panel__auth">
      <?php if ($isLoggedIn): ?>
        <hr>
        <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
        <hr>
        <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      <?php else: ?>
        <hr>
        <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>login.php"><i class="fa-regular fa-right-to-bracket"></i> Login</a>
        <a class="mobile-panel__link" href="<?php echo $rootPrefix; ?>register.php"><i class="fa-regular fa-user-plus"></i> Register</a>
      <?php endif; ?>
    </div>
</nav>
