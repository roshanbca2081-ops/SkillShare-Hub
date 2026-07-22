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
          <h1>Placement Preparation</h1>
          <p>Prepare your resume, practice interviews, and get placement-ready.</p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-file-pen"></i></div>
          <h3>Resume Builder</h3>
          <p class="text-light-emphasis small">Create and polish your professional resume.</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-microphone"></i></div>
          <h3>Mock Interviews</h3>
          <p class="text-light-emphasis small">Practice with common interview questions.</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-briefcase"></i></div>
          <h3>Job Placements</h3>
          <p class="text-light-emphasis small">Track placement drives and opportunities.</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
