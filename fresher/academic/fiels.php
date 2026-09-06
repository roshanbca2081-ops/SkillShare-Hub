<?php
$page_title = 'Academic Fields';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$stmt = $pdo->query("SELECT f.*, 
                     (SELECT COUNT(*) FROM courses WHERE field_id = f.id AND status = 'active') as course_count 
                     FROM academic_fields f 
                     WHERE f.is_active = 1 
                     ORDER BY f.name");
$fields = $stmt->fetchAll();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Academic Fields</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
            
            <div class="row g-4">
                <?php foreach ($fields as $field): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow text-center p-4">
                        <div class="field-icon mb-3" style="font-size: 3rem; color: <?php echo $field['color'] ?? '#6C63FF'; ?>;">
                            <i class="fas fa-<?php echo $field['icon'] ?? 'book'; ?>"></i>
                        </div>
                        <h5><?php echo htmlspecialchars($field['name']); ?></h5>
                        <p class="text-muted small"><?php echo htmlspecialchars($field['description'] ?? ''); ?></p>
                        <div class="mb-3">
                            <span class="badge bg-primary"><?php echo $field['course_count'] ?? 0; ?> Courses</span>
                        </div>
                        <a href="courses.php?field=<?php echo $field['id']; ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i> View Courses
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($fields)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5>No academic fields available</h5>
                    <p class="text-muted">Check back later for new fields.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>