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
          <h1>Contact ShareSkill Hub</h1>
          <p>Questions about the platform, onboarding, or mentorship? Reach out and we’ll respond shortly.</p>
        </div>
      </div>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="soft-card">
            <h3>Support</h3>
            <p>Email us at <a href="mailto:hello@shareskillhub.dev">hello@shareskillhub.dev</a> for account, course or technical help.</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="soft-card">
            <h3>Partners</h3>
            <p>Interested in collaboration or placement partnerships? Let’s build learner success together.</p>
          </div>
        </div>
      </div>
      <div class="mt-4">
        <a href="mailto:hello@shareskillhub.dev" class="btn btn--primary">Email Support</a>
        <a href="index.php" class="btn btn--outline">Return Home</a>
      </div>
    </div>
  </section>
</main>
<?php include 'includes/footer.php'; ?>
