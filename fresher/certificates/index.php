<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
ensure_database_schema();
require_login('fresher');
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container py-5">
    <div class="card p-4 mb-4">
      <div class="page-title">
        <div>
          <h1>My Certificates</h1>
          <p>View and download your course, mentorship, and assignment certificates.</p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-award"></i></div>
          <h3>Course Certificates</h3>
          <p class="text-light-emphasis small">Complete courses to unlock certificates.</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-medal"></i></div>
          <h3>Mentorship Certificates</h3>
          <p class="text-light-emphasis small">Earn after completing mentorship sessions.</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-certificate"></i></div>
          <h3>Assignment Certificates</h3>
          <p class="text-light-emphasis small">Issued after assignment submission and feedback.</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
