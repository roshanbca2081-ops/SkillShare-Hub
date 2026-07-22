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
          <h1>Research Hub</h1>
          <p>Access curated research and study materials.</p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card p-4">
          <h3>Research Groups</h3>
          <p class="text-light-emphasis">Join research groups by field and course to collaborate on projects.</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-4">
          <h3>Research Documents</h3>
          <p class="text-light-emphasis">Share and access research papers, project documents, and guides.</p>
          <span class="tag">Coming Soon</span>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
