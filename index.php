<?php include 'config/config.php'; include 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ShareSkill Hub | Learn, Mentor, Grow</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php"><i class="fa-solid fa-graduation-cap me-2"></i>ShareSkill Hub</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto gap-2">
          <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
          <li class="nav-item"><a class="btn btn-primary ms-2" href="login.php">Get Started</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="hero">
    <div class="container py-5">
      <div class="row align-items-center g-4">
        <div class="col-lg-7 animate">
          <span class="badge mb-3">Premium mentorship • practical learning • career growth</span>
          <h1 class="display-4 fw-bold mb-3">Bridge the gap between learning and opportunity.</h1>
          <p class="lead text-light-emphasis mb-4">ShareSkill Hub brings graduates, freshers, mentors, and learners into one modern platform for skill-building, assignments, mentorship, and placement readiness.</p>
          <div class="d-flex flex-wrap gap-3">
            <a href="register.php" class="btn btn-primary btn-lg">Join as Fresher</a>
            <a href="register.php" class="btn btn-outline-light btn-lg">Become a Mentor</a>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="card p-4 animate">
            <h4 class="mb-3"><i class="fa-solid fa-chart-line me-2"></i>Platform Highlights</h4>
            <ul class="list-unstyled mb-0">
              <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-2"></i>Live mentorship sessions</li>
              <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-2"></i>Practical assignments & feedback</li>
              <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-2"></i>Interview and placement prep</li>
              <li class="mb-2"><i class="fa-solid fa-check-circle text-success me-2"></i>Secure modern dashboard experience</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container stats">
      <div class="row g-4">
        <div class="col-md-4"><div class="card stat-card text-center animate"><h3 data-count="1200">0</h3><p>Active Learners</p></div></div>
        <div class="col-md-4"><div class="card stat-card text-center animate"><h3 data-count="300">0</h3><p>Mentor Sessions</p></div></div>
        <div class="col-md-4"><div class="card stat-card text-center animate"><h3 data-count="98">0</h3><p>Placement Success</p></div></div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card p-4 animate">
            <div class="feature-icon"><i class="fa-solid fa-users"></i></div>
            <h5>Mentorship</h5>
            <p>Book sessions with graduates who can guide you through practical skills and interview preparation.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-4 animate">
            <div class="feature-icon"><i class="fa-solid fa-file-lines"></i></div>
            <h5>Assignments</h5>
            <p>Submit work, receive feedback, and track your progress with structured learning paths.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-4 animate">
            <div class="feature-icon"><i class="fa-solid fa-briefcase"></i></div>
            <h5>Placement Prep</h5>
            <p>Access curated research, placement guides, and practice materials for real opportunities.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container text-center">
      <p class="mb-0">© 2026 ShareSkill Hub. Built for modern learning and mentorship.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
