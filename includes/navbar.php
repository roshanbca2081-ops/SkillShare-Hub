<?php
$basePath = '';
if (!file_exists('index.php') && file_exists('../index.php')) {
    $basePath = '../';
} elseif (!file_exists('index.php') && file_exists('../../index.php')) {
    $basePath = '../../';
}
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<nav class="navbar" data-mobile-open="false">
  <div class="navbar__inner">
    <a class="brand" href="<?php echo $basePath; ?>index.php" aria-label="ShareSkill Hub">
      <span class="site-logo" aria-hidden="true"></span>
    </a>

    <form class="search" action="<?php echo $basePath; ?>course.php" method="get" aria-label="Site search">
      <input type="search" name="q" placeholder="Search courses, skills..." aria-label="Search" />
    </form>

    <div class="nav" role="navigation" aria-label="Primary">
      <a class="nav__link<?php echo $currentPage === 'index.php' ? ' nav__link--active' : ''; ?>" href="<?php echo $basePath; ?>index.php"><i class="fa-solid fa-house"></i> Home</a>
      <a class="nav__link" href="<?php echo $basePath; ?>index.php#academic-fields"><i class="fa-solid fa-layer-group"></i> Fields</a>
      <a class="nav__link<?php echo $currentPage === 'course.php' ? ' nav__link--active' : ''; ?>" href="<?php echo $basePath; ?>course.php"><i class="fa-solid fa-book-open-reader"></i> Courses</a>
      <a class="nav__link<?php echo $currentPage === 'mentor.php' ? ' nav__link--active' : ''; ?>" href="<?php echo $basePath; ?>mentor.php"><i class="fa-solid fa-user-tie"></i> Mentors</a>
      <a class="nav__link<?php echo $currentPage === 'book.php' ? ' nav__link--active' : ''; ?>" href="<?php echo $basePath; ?>fresher/mentorship/book.php"><i class="fa-solid fa-video"></i> Sessions</a>
      <a class="nav__link" href="<?php echo $basePath; ?>fresher/assignments/index.php"><i class="fa-solid fa-file-pen"></i> Assignments</a>
      <a class="nav__link" href="<?php echo $basePath; ?>fresher/research/index.php"><i class="fa-solid fa-microscope"></i> Research</a>
      <a class="nav__link" href="<?php echo $basePath; ?>fresher/placement/index.php"><i class="fa-solid fa-briefcase"></i> Placement</a>
      <a class="nav__link" href="<?php echo $basePath; ?>fresher/certificates/index.php"><i class="fa-solid fa-award"></i> Certificates</a>
      <a class="nav__link<?php echo $currentPage === 'about.php' ? ' nav__link--active' : ''; ?>" href="<?php echo $basePath; ?>about.php"><i class="fa-solid fa-circle-info"></i> About</a>
      <a class="nav__link<?php echo $currentPage === 'contact.php' ? ' nav__link--active' : ''; ?>" href="<?php echo $basePath; ?>contact.php"><i class="fa-solid fa-envelope"></i> Contact</a>
      <a class="nav__link<?php echo $currentPage === 'notifications.php' ? ' nav__link--active' : ''; ?>" href="<?php echo $basePath; ?>notifications.php"><i class="fa-solid fa-bell"></i></a>
    </div>

    <div class="navbar__right">
      <button class="btn btn--ghost btn-sm" type="button" data-theme-toggle aria-label="Toggle dark mode"><i class="fa-solid fa-moon"></i></button>
      <?php if (is_logged_in()): ?>
        <div class="profile" data-profile>
          <button class="avatar-btn" type="button" data-profile-trigger aria-label="Open profile menu">
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U',0,1)); ?></div>
          </button>
          <div class="dropdown" data-open="false">
            <a href="<?php echo $basePath; ?>dashboard.php">Dashboard</a>
            <a href="<?php echo $basePath; ?>settings.php">Settings</a>
            <hr />
            <a href="<?php echo $basePath; ?>logout.php" style="color:var(--danger);">Sign out</a>
          </div>
        </div>
      <?php else: ?>
        <a class="btn btn--outline btn-sm" href="<?php echo $basePath; ?>login.php">Login</a>
        <a class="btn btn--primary btn-sm" href="<?php echo $basePath; ?>register.php">Register</a>
      <?php endif; ?>
      <button class="hamburger" type="button" aria-label="Open menu" data-hamburger>
        <div class="hamburger__lines" aria-hidden="true"><span></span><span></span><span></span></div>
      </button>
    </div>
  </div>

  <div class="mobile-panel" aria-hidden="true">
    <div class="mobile-panel__grid">
      <a class="mobile-panel__link<?php echo $currentPage === 'index.php' ? ' mobile-panel__link--active' : ''; ?>" href="<?php echo $basePath; ?>index.php"><i class="fa-solid fa-house"></i> Home</a>
      <a class="mobile-panel__link<?php echo $currentPage === 'course.php' ? ' mobile-panel__link--active' : ''; ?>" href="<?php echo $basePath; ?>course.php"><i class="fa-solid fa-book-open-reader"></i> Courses</a>
      <a class="mobile-panel__link<?php echo $currentPage === 'mentor.php' ? ' mobile-panel__link--active' : ''; ?>" href="<?php echo $basePath; ?>mentor.php"><i class="fa-solid fa-user-tie"></i> Mentors</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>index.php#academic-fields"><i class="fa-solid fa-layer-group"></i> Academic Fields</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>fresher/mentorship/book.php"><i class="fa-solid fa-video"></i> Video Sessions</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>fresher/assignments/index.php"><i class="fa-solid fa-file-pen"></i> Assignments</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>fresher/research/index.php"><i class="fa-solid fa-microscope"></i> Research</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>fresher/placement/index.php"><i class="fa-solid fa-briefcase"></i> Placement</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>fresher/certificates/index.php"><i class="fa-solid fa-award"></i> Certificates</a>
      <a class="mobile-panel__link<?php echo $currentPage === 'about.php' ? ' mobile-panel__link--active' : ''; ?>" href="<?php echo $basePath; ?>about.php"><i class="fa-solid fa-circle-info"></i> About</a>
      <a class="mobile-panel__link<?php echo $currentPage === 'contact.php' ? ' mobile-panel__link--active' : ''; ?>" href="<?php echo $basePath; ?>contact.php"><i class="fa-solid fa-envelope"></i> Contact</a>
      <?php if (is_logged_in()): ?>
        <a class="mobile-panel__link<?php echo $currentPage === 'dashboard.php' ? ' mobile-panel__link--active' : ''; ?>" href="<?php echo $basePath; ?>dashboard.php">Dashboard</a>
        <a class="mobile-panel__link" href="<?php echo $basePath; ?>logout.php">Logout</a>
      <?php else: ?>
        <a class="mobile-panel__link<?php echo $currentPage === 'login.php' ? ' mobile-panel__link--active' : ''; ?>" href="<?php echo $basePath; ?>login.php">Login</a>
        <a class="mobile-panel__link<?php echo $currentPage === 'register.php' ? ' mobile-panel__link--active' : ''; ?>" href="<?php echo $basePath; ?>register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
