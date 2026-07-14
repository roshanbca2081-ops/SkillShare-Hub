<?php
$basePath = '';
if (!file_exists('index.php') && file_exists('../index.php')) {
    $basePath = '../';
} elseif (!file_exists('index.php') && file_exists('../../index.php')) {
    $basePath = '../../';
}
?>
<nav class="navbar" data-mobile-open="false">
  <div class="navbar__inner">
    <a class="brand" href="<?php echo $basePath; ?>index.php" aria-label="ShareSkill Hub">
      <div class="brand__mark">SH</div>
      <div class="brand__text">
        <div class="brand__name">SkillShare Hub</div>
        <div class="brand__tag">Share, Learn, Grow</div>
      </div>
    </a>

    <form class="search" action="<?php echo $basePath; ?>course.php" method="get" aria-label="Site search">
      <input type="search" name="q" placeholder="Search courses, skills..." aria-label="Search" />
    </form>

    <div class="nav" role="navigation" aria-label="Primary">
      <a class="nav__link" href="<?php echo $basePath; ?>index.php"><span class="nav__icon">H</span>Home</a>
      <a class="nav__link" href="<?php echo $basePath; ?>course.php"><span class="nav__icon">C</span>Course</a>
      <a class="nav__link" href="<?php echo $basePath; ?>mentor.php"><span class="nav__icon">M</span>Mentor</a>
      <a class="nav__link" href="<?php echo $basePath; ?>notifications.php"><span class="nav__icon">N</span>Notification</a>
      <a class="nav__link" href="<?php echo $basePath; ?>fresher/mentorship/book.php"><span class="nav__icon">S</span>Session</a>
    </div>

    <div class="navbar__right">
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
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>index.php">Home</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>course.php">Courses</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>mentor.php">Mentors</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>about.php">About</a>
      <a class="mobile-panel__link" href="<?php echo $basePath; ?>contact.php">Contact</a>
      <?php if (is_logged_in()): ?>
        <a class="mobile-panel__link" href="<?php echo $basePath; ?>dashboard.php">Dashboard</a>
        <a class="mobile-panel__link" href="<?php echo $basePath; ?>logout.php">Logout</a>
      <?php else: ?>
        <a class="mobile-panel__link" href="<?php echo $basePath; ?>login.php">Login</a>
        <a class="mobile-panel__link" href="<?php echo $basePath; ?>register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
