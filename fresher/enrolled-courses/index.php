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
          <h1>My Enrolled Courses</h1>
          <p>Track your enrolled courses and continue learning.</p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card p-4">
          <h3>Full Stack Web Development</h3>
          <div class="progress-bar mt-3" style="height:8px;"><span style="width:60%;"></span></div>
          <p class="small text-light-emphasis mt-2">60% complete</p>
          <a class="btn btn--outline btn-sm mt-2" href="../../course.php">Continue</a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-4">
          <h3>Mobile App Development</h3>
          <div class="progress-bar mt-3" style="height:8px;"><span style="width:30%;"></span></div>
          <p class="small text-light-emphasis mt-2">30% complete</p>
          <a class="btn btn--outline btn-sm mt-2" href="../../course.php">Continue</a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
