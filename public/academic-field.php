<?php
$page_title = 'Academic Fields';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$stmt = $pdo->query("SELECT * FROM academic_fields ORDER BY name");
$fields = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <h1 class="display-4 fw-bold text-center mb-3">Academic Fields</h1>
    <p class="text-center text-muted mb-5">Explore courses and mentors in your field of interest</p>
    
    <div class="row g-4">
        <?php foreach ($fields as $field): ?>
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm hover-shadow text-center p-4">
                <div class="field-icon mb-3" style="font-size: 3rem; color: #667eea;">
                    <i class="fas fa-<?php echo $field['icon'] ?? 'book'; ?>"></i>
                </div>
                <h5><?php echo htmlspecialchars($field['name']); ?></h5>
                <p class="text-muted small"><?php echo htmlspecialchars($field['description'] ?? ''); ?></p>
                <a href="courses.php?field=<?php echo $field['id']; ?>" class="btn btn-outline-primary btn-sm">View Courses</a>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($fields)): ?>
        <div class="col-12 text-center py-5">
            <p class="text-muted">No academic fields available.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>