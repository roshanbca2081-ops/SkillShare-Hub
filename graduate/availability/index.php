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
    <div class="card p-4" style="max-width:700px;margin:0 auto;">
      <div class="page-title">
        <div>
          <h1>Set Availability</h1>
          <p>Manage your available time slots for mentorship sessions.</p>
        </div>
      </div>
      <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <?php
          global $conn;
          $availableTime = trim($_POST['available_time'] ?? '');
          $stmt = $conn->prepare("INSERT INTO mentor_availability (mentor_id, available_time) VALUES (?, ?)");
          $stmt->bind_param('is', $_SESSION['user_id'], $availableTime);
          $stmt->execute();
          echo '<div class="alert alert-success">Availability updated!</div>';
        ?>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Available Time Slots</label>
          <textarea name="available_time" class="form-control" rows="4" placeholder="e.g., Monday 7-9 PM, Wednesday 6-8 PM, Friday 8-10 AM"></textarea>
        </div>
        <button class="btn btn--primary" type="submit">Save Availability</button>
        <a class="btn btn--outline" href="../dashboard.php">Back</a>
      </form>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
