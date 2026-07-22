<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
ensure_database_schema();
require_login('graduate');
$assignments = get_assignments();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container py-5">
    <div class="card p-4 mb-4">
      <div class="page-title">
        <div>
          <h1>My Assignments</h1>
          <p>Manage assignments you've created for freshers.</p>
        </div>
      </div>
      <a class="btn btn--primary" href="create.php">Create Assignment</a>
    </div>
    <div class="row g-4">
      <?php foreach ($assignments as $a): ?>
        <div class="col-md-6">
          <div class="card p-4">
            <h5><?php echo e($a['title']); ?></h5>
            <p class="small text-light-emphasis">Deadline: <?php echo e($a['deadline'] ?: 'N/A'); ?></p>
            <p class="small"><?php echo e($a['description']); ?></p>
            <a class="btn btn--outline btn-sm" href="submissions.php?assignment_id=<?php echo (int)$a['id']; ?>">View Submissions</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
