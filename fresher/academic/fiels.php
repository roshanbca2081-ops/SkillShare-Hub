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
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Academic Fields</h1>
            </div>
            
            <div class="academic-fields-grid">
                <?php foreach ($fields as $field):
                    $fieldIcon = (string)($field['icon'] ?? 'fa-book');
                    if (!preg_match('/^fa-[a-z0-9-]+$/i', $fieldIcon)) {
                        $fieldIcon = 'fa-book';
                    }
                    $fieldColor = (string)($field['color'] ?? '#4f46e5');
                    if (!preg_match('/^#[0-9a-f]{6}$/i', $fieldColor)) {
                        $fieldColor = '#4f46e5';
                    }
                ?>
                    <article class="card border-0 shadow-sm hover-shadow academic-field-card" style="--field-color: <?php echo htmlspecialchars($fieldColor, ENT_QUOTES, 'UTF-8'); ?>;">
                        <div class="academic-field-icon" aria-hidden="true"><i class="fas <?php echo htmlspecialchars($fieldIcon, ENT_QUOTES, 'UTF-8'); ?>"></i></div>
                        <h2><?php echo htmlspecialchars($field['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p class="academic-field-description"><?php echo htmlspecialchars($field['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <span class="badge bg-primary"><?php echo (int)($field['course_count'] ?? 0); ?> Courses</span>
                        <a href="course.php?field=<?php echo (int)$field['id']; ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1" aria-hidden="true"></i> View Courses
                        </a>
                    </article>
                <?php endforeach; ?>
                <?php if (empty($fields)): ?>
                <div class="text-center py-5">
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