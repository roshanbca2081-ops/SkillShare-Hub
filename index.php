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
    <section class="hero">
      <div class="container">
        <div class="hero__grid">
          <div>
            <span class="badge">Premium mentorship • practical learning • career growth</span>
            <h1 class="section-title" style="margin-top:var(--s-3)">Bridge the gap between learning and opportunity.</h1>
            <p class="lead" style="margin-top:var(--s-2)">ShareSkill Hub brings graduates, freshers, mentors, and learners into one modern platform for skill-building, assignments, mentorship, and placement readiness.</p>
            <div class="hero__cta">
              <a href="register.php" class="btn btn--primary">Join as Fresher</a>
              <a href="register.php" class="btn btn--outline">Become a Mentor</a>
            </div>
          </div>

          <div class="card animate" style="padding:var(--s-4)">
            <h2 class="section-title" style="margin-bottom:var(--s-3)">Platform Highlights</h2>
            <ul style="display:grid;gap:12px;margin:0;padding:0;">
              <li>Live mentorship sessions</li>
              <li>Practical assignments & feedback</li>
              <li>Interview and placement prep</li>
              <li>Secure modern dashboard experience</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container stats">
        <div class="stats-grid">
          <div class="card stat-card text-center animate">
            <h3 data-count="500" style="margin:0 0 6px 0">0</h3>
            <p>Graduates</p>
          </div>
          <div class="card stat-card text-center animate">
            <h3 data-count="3000" style="margin:0 0 6px 0">0</h3>
            <p>Freshers</p>
          </div>
          <div class="card stat-card text-center animate">
            <h3 data-count="200" style="margin:0 0 6px 0">0</h3>
            <p>Projects</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="features-grid">
          <div class="card feature-card animate">
            <div class="feature-icon" aria-hidden="true">★</div>
            <h5 style="margin:0 0 8px 0">Mentorship</h5>
            <p class="small-muted" style="margin:0">Book sessions with graduates who can guide you through practical skills and interview preparation.</p>
          </div>
          <div class="card feature-card animate">
            <div class="feature-icon" aria-hidden="true">✓</div>
            <h5 style="margin:0 0 8px 0">Assignments</h5>
            <p class="small-muted" style="margin:0">Submit work, receive feedback, and track your progress with structured learning paths.</p>
          </div>
          <div class="card feature-card animate">
            <div class="feature-icon" aria-hidden="true">⌁</div>
            <h5 style="margin:0 0 8px 0">Placement Prep</h5>
            <p class="small-muted" style="margin:0">Access curated research, placement guides, and practice materials for real opportunities.</p>
          </div>
        </div>
      </div>
    </section>
  </main>


  <?php include 'includes/footer.php'; ?>
</body>
</html>


