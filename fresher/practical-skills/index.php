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
          <h1>Practical Skills</h1>
          <p>Browse practical skills and find mentors who can teach them.</p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-code"></i></div>
          <h3>Web Development</h3>
          <p class="text-light-emphasis small">HTML, CSS, JavaScript, PHP</p>
          <span class="tag tag--green">Available</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-database"></i></div>
          <h3>Database Design</h3>
          <p class="text-light-emphasis small">MySQL, SQL, ERD Design</p>
          <span class="tag tag--green">Available</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-mobile"></i></div>
          <h3>Mobile Development</h3>
          <p class="text-light-emphasis small">Flutter, React Native</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
