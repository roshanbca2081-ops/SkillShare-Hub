<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
ensure_database_schema();
require_login('graduate');
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container py-5">
    <div class="card p-4 mb-4">
      <div class="page-title">
        <div>
          <h1>Video Sessions</h1>
          <p>Manage your mentorship video sessions.</p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card p-4">
          <h3>Upcoming Sessions</h3>
          <p class="text-light-emphasis small">View and join scheduled sessions with your students.</p>
          <span class="tag">No upcoming sessions</span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-4">
          <h3>Session History</h3>
          <p class="text-light-emphasis small">Review past sessions and student attendance.</p>
          <a class="btn btn--outline btn-sm" href="history.php">View History</a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
