<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
ensure_database_schema();
require_login('graduate');
$bookings = get_mentorship_bookings(null, $_SESSION['user_id']);
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container py-5">
    <div class="card p-4 mb-4">
      <div class="page-title">
        <div>
          <h1>Earnings</h1>
          <p>View your mentorship session earnings and payment history.</p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-wallet"></i></div>
          <h3>Total Earnings</h3>
          <h2 class="price">Rs. 24,000</h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-clock"></i></div>
          <h3>Sessions Completed</h3>
          <h2 class="price">12</h2>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center">
          <div class="stat-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-star"></i></div>
          <h3>Rating</h3>
          <h2 class="price">4.8</h2>
        </div>
      </div>
    </div>
    <div class="card p-4 mt-4">
      <h3>Recent Sessions</h3>
      <div class="list-group">
        <?php foreach (array_slice($bookings, 0, 5) as $b): ?>
          <div class="list-group-item d-flex justify-content-between">
            <span><strong><?php echo e($b['topic']); ?></strong> with <?php echo e($b['student_name']); ?></span>
            <span class="tag">Rs. 800</span>
          </div>
        <?php endforeach; ?>
        <?php if (!$bookings): ?>
          <div class="list-group-item text-light-emphasis">No session history yet.</div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
