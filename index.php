<?php include 'config/config.php'; include 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShareSkill Hub | Learn, Mentor, Grow</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <?php include 'includes/navbar.php'; ?>

  <main>
    <section class="hero hero-landing">
      <div class="container hero-grid">
        <div class="hero-copy animate">
          <span class="pill-badge">Share knowledge. Build skills. Grow careers.</span>
          <h1>Learn, mentor, and launch your career from one beautiful hub.</h1>
          <p>Discover practical courses, real mentorship, assignment-driven learning, and placement-ready support for freshers and graduates.</p>
          <div class="hero-actions">
            <a href="register.php" class="btn btn--primary">Join as Fresher</a>
            <a href="register.php" class="btn btn--outline">Become a Mentor</a>
          </div>
          <div class="hero-stat-row">
            <div class="stat-pill"><strong>500+</strong> Graduates</div>
            <div class="stat-pill"><strong>3000+</strong> Freshers</div>
            <div class="stat-pill"><strong>120+</strong> Mentors</div>
          </div>
        </div>

        <div class="hero-panel animate">
          <div class="panel-top">
            <p class="panel-label">Platform Highlights</p>
            <div class="panel-chip">Learn • Mentor • Place</div>
          </div>
          <ul class="panel-list">
            <li><span>Live mentorship sessions</span></li>
            <li><span>Assignments with feedback</span></li>
            <li><span>Interview preparation tools</span></li>
            <li><span>Modern dashboard experience</span></li>
          </ul>
        </div>
      </div>
    </section>

    <section class="section feature-section">
      <div class="container feature-grid">
        <article class="card feature-card animate">
          <div class="feature-icon"><i class="fa-solid fa-user-graduate"></i></div>
          <h4>Mentorship</h4>
          <p>Book sessions with expert graduates and build real-world confidence fast.</p>
        </article>
        <article class="card feature-card animate">
          <div class="feature-icon"><i class="fa-solid fa-file-lines"></i></div>
          <h4>Assignments</h4>
          <p>Submit projects, get feedback, and track your learning journey in one place.</p>
        </article>
        <article class="card feature-card animate">
          <div class="feature-icon"><i class="fa-solid fa-briefcase"></i></div>
          <h4>Placement Prep</h4>
          <p>Access interview guides, resume support, and placement-focused learning paths.</p>
        </article>
      </div>
    </section>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>


