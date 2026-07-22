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
          <h1>Interview Preparation</h1>
          <p>Practice common interview questions and get mentor feedback.</p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card p-4">
          <h3>Common Questions</h3>
          <p class="text-light-emphasis small">Browse frequently asked interview questions by field.</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-4">
          <h3>Mock Sessions</h3>
          <p class="text-light-emphasis small">Book mock interview sessions with mentors.</p>
          <a class="btn btn--outline btn-sm mt-2" href="../mentorship/book.php">Book Session</a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
