<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php';
ensure_database_schema();
require_login('graduate');
$assignmentId = (int)($_GET['assignment_id'] ?? 0);
$submissions = get_assignment_submissions($assignmentId);
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<main class="page-shell">
  <section class="container py-5">
    <div class="card p-4 mb-4">
      <div class="page-title">
        <div>
          <h1>Submissions</h1>
          <p>Review student submissions for assignment #<?php echo $assignmentId; ?></p>
        </div>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($submissions as $s): ?>
        <div class="col-md-6">
          <div class="card p-4">
            <h5><?php echo e($s['student_name']); ?></h5>
            <p class="small text-light-emphasis"><?php echo e($s['notes']); ?></p>
            <?php if ($s['file_path']): ?>
              <a class="btn btn--outline btn-sm" href="../../<?php echo e($s['file_path']); ?>" download>Download</a>
            <?php endif; ?>
            <span class="tag mt-2">Status: <?php echo e($s['status']); ?></span>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (!$submissions): ?>
        <div class="col-12"><div class="alert alert-info">No submissions yet.</div></div>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php include '../../includes/footer.php'; ?>
