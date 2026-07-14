<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container page-section">
    <div class="card card--padded animate" style="max-width:840px;margin:auto;">
      <div class="page-title">
        <div>
          <h1>About ShareSkill Hub</h1>
          <p>ShareSkill Hub is a modern learning and mentorship platform that connects learners and mentors through practical education, assignments, and career preparation.</p>
        </div>
      </div>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="soft-card">
            <h3>Our mission</h3>
            <p>Make skill transformation visible, structured, and accessible for every learner.</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="soft-card">
            <h3>What we offer</h3>
            <p>Courses, mentorship sessions, assignments, placement coaching, and certification pathways tailored for freshers and graduates.</p>
          </div>
        </div>
      </div>
      <div class="mt-4">
        <a href="register.php" class="btn btn--primary">Join Now</a>
        <a href="contact.php" class="btn btn--outline">Contact Support</a>
      </div>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
