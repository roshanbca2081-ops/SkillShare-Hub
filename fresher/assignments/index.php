<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
ensure_database_schema();
require_login('fresher');
$assignments = get_assignments();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container py-5">
    <div class="card p-4 mb-4">
      <div class="page-title">
        <div>
          <h1>Assignments</h1>
          <p>Open assigned tasks and submit your work.</p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($assignments as $assignment): ?>
        <div class="col-md-6">
          <div class="card p-4">
            <h5><?php echo e($assignment['title']); ?></h5>
            <p class="text-light-emphasis"><?php echo e($assignment['description']); ?></p>
            <p class="small mb-2">Deadline: <?php echo e($assignment['deadline'] ?: 'No deadline'); ?></p>
            <p class="small mb-3">Posted by: <?php echo e($assignment['created_by_name'] ?: 'Admin'); ?></p>
            <div class="d-flex gap-2">
              <a class="btn btn--primary" href="submit.php?assignment_id=<?php echo (int)$assignment['id']; ?>">Submit work</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (!$assignments): ?>
        <div class="col-12"><div class="alert alert-info">No assignments available yet.</div></div>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
