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
          <h1>Messages</h1>
          <p>Chat with mentors and research group members.</p>
        </div>
      </div>
    </div>
    <div class="card p-4 text-center">
      <p class="text-light-emphasis">Messaging system coming soon. You will be able to chat with mentors and group members.</p>
      <span class="tag">Coming Soon</span>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
