<nav class="navbar" data-mobile-open="false">
  <div class="navbar__inner">
    <a class="brand" href="index.php" aria-label="ShareSkill Hub">
      <div class="brand__mark">SS</div>
      <div class="brand__text">
        <div class="brand__name">ShareSkill Hub</div>
        <div class="brand__tag">Learn • Mentor • Grow</div>
      </div>
    </a>

    <div class="nav" role="navigation" aria-label="Primary">
      <a class="nav__link" data-nav="home" href="index.php">Home</a>
      <a class="nav__link" data-nav="courses" href="course.php">Courses</a>
      <a class="nav__link" data-nav="mentorship" href="mentor.php">Mentorship</a>
      <a class="nav__link" data-nav="placement" href="about.php">Placement</a>
      <a class="nav__link" data-nav="research" href="about.php">Research</a>
      <a class="nav__link" data-nav="interview" href="about.php">Interview</a>
      <a class="nav__link" data-nav="about" href="about.php">About</a>
      <a class="nav__link" data-nav="contact" href="contact.php">Contact</a>
    </div>

    <div class="navbar__right">
      <form class="search" action="index.php" method="get" aria-label="Site search">
        <input type="search" name="q" placeholder="Search courses, mentors, skills..." aria-label="Search">
      </form>

      <?php if (is_logged_in()): ?>
        <a class="nav__link" href="dashboard.php">Dashboard</a>
        <a class="nav__link" href="notifications.php">Notifications</a>

        <div class="profile" data-profile>
          <button class="avatar-btn" type="button" data-profile-trigger>
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U',0,1)); ?></div>
          </button>
          <div class="dropdown" data-open="false">
            <a href="dashboard.php">My dashboard</a>
            <a href="settings.php">Settings</a>
            <hr />
            <a href="logout.php" style="color:color-mix(in srgb, var(--danger) 85%, white);">Sign out</a>
          </div>
        </div>
      <?php else: ?>
        <a class="btn btn--primary" href="login.php">Login</a>
        <a class="btn btn--outline" href="register.php">Register</a>
      <?php endif; ?>

      <button class="hamburger" type="button" aria-label="Open menu" data-hamburger>
        <div class="hamburger__lines" aria-hidden="true">
          <span></span><span></span><span></span>
        </div>
      </button>
    </div>
  </div>

  <div class="mobile-panel" aria-hidden="true">
    <div class="mobile-panel__grid">
      <a class="mobile-panel__link" href="index.php">Home</a>
      <a class="mobile-panel__link" href="course.php">Courses</a>
      <a class="mobile-panel__link" href="mentor.php">Mentorship</a>
      <a class="mobile-panel__link" href="about.php">Placement</a>
      <a class="mobile-panel__link" href="about.php">Research</a>
      <a class="mobile-panel__link" href="about.php">Interview</a>
      <a class="mobile-panel__link" href="about.php">About</a>
      <a class="mobile-panel__link" href="contact.php">Contact</a>

      <?php if (is_logged_in()): ?>
        <a class="mobile-panel__link" href="dashboard.php">Dashboard</a>
        <a class="mobile-panel__link" href="notifications.php">Notifications</a>
        <a class="mobile-panel__link" href="settings.php">Settings</a>
        <a class="mobile-panel__link" href="logout.php" style="color:color-mix(in srgb, var(--danger) 85%, white);">Sign out</a>
      <?php else: ?>
        <a class="mobile-panel__link" href="login.php">Login</a>
        <a class="mobile-panel__link" href="register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

