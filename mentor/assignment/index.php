<?php
$page_title = 'Assignments';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT a.*, c.title as course_title,
                       (SELECT COUNT(*) FROM assignment_submissions WHERE assignment_id = a.id) as submissions_count,
                       (SELECT COUNT(*) FROM assignment_submissions WHERE assignment_id = a.id AND status = 'graded') as graded_count
                       FROM assignments a 
                       JOIN courses c ON a.course_id = c.id 
                       WHERE a.mentor_id = ? 
                       ORDER BY a.due_date ASC");
$stmt->execute([$user_id]);
$assignments = $stmt->fetchAll();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Assignments</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Assignment
                    </a>
                </div>
            </div>
            
            <?php if (!empty($assignments)): ?>
                <div class="row g-4">
                    <?php foreach ($assignments as $assignment): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5><?php echo htmlspecialchars($assignment['title']); ?></h5>
                                    <span class="badge bg-<?php 
                                        echo $assignment['is_published'] ? 'success' : 'secondary'; 
                                    ?>">
                                        <?php echo $assignment['is_published'] ? 'Published' : 'Draft'; ?>
                                    </span>
                                </div>
                                <p class="text-muted small"><?php echo htmlspecialchars($assignment['course_title']); ?></p>
                                
                                <div class="mb-2">
                                    <span class="badge bg-info">Due: <?php echo formatDate($assignment['due_date']); ?></span>
                                    <span class="badge bg-secondary">Max: <?php echo $assignment['max_score']; ?></span>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-file-upload"></i> <?php echo $assignment['submissions_count']; ?> submissions
                                    </span>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> <?php echo $assignment['graded_count']; ?> graded
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="view.php?id=<?php echo $assignment['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="edit.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-warning flex-grow-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="grade.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-success flex-grow-1">
                                            <i class="fas fa-graduation-cap"></i> Grade
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <h5>No assignments created</h5>
                    <p class="text-muted">Create your first assignment for your students.</p>
                    <a href="create.php" class="btn btn-primary">Create Assignment</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>