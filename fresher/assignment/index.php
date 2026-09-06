<?php
$page_title = 'Assignments';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

// Get all assignments for enrolled courses
$stmt = $pdo->prepare("SELECT a.*, c.title as course_title, u.full_name as mentor_name,
                       (SELECT id FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as submission_id,
                       (SELECT score FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as score,
                       (SELECT status FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as submission_status
                       FROM assignments a 
                       JOIN courses c ON a.course_id = c.id 
                       JOIN users u ON a.mentor_id = u.id 
                       JOIN enrollments e ON e.course_id = c.id 
                       WHERE e.fresher_id = ? AND e.status != 'dropped' AND a.is_published = 1
                       ORDER BY a.due_date");
$stmt->execute([$user_id, $user_id, $user_id, $user_id]);
$assignments = $stmt->fetchAll();
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
                <h1 class="h2">Assignments</h1>
            </div>
            
            <?php if (!empty($assignments)): ?>
                <div class="row g-4">
                    <?php foreach ($assignments as $assignment): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5><?php echo htmlspecialchars($assignment['title']); ?></h5>
                                    <?php if ($assignment['submission_id']): ?>
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Submitted</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted small"><?php echo htmlspecialchars($assignment['course_title']); ?></p>
                                <p class="small"><?php echo substr($assignment['description'], 0, 100); ?>...</p>
                                
                                <div class="mb-2">
                                    <span class="badge bg-info">Due: <?php echo formatDate($assignment['due_date']); ?></span>
                                    <span class="badge bg-secondary">Max Score: <?php echo $assignment['max_score']; ?></span>
                                </div>
                                
                                <?php if ($assignment['submission_id']): ?>
                                    <div class="mb-2">
                                        <span class="badge bg-<?php echo $assignment['submission_status'] === 'graded' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($assignment['submission_status'] ?? 'submitted'); ?>
                                        </span>
                                        <?php if ($assignment['score'] !== null): ?>
                                            <span class="badge bg-primary">Score: <?php echo $assignment['score']; ?>/<?php echo $assignment['max_score']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="details.php?id=<?php echo $assignment['id']; ?>" class="btn btn-primary w-100">
                                    <?php echo $assignment['submission_id'] ? 'View Submission' : 'Submit Assignment'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <h5>No assignments</h5>
                    <p class="text-muted">You don't have any assignments for your enrolled courses.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>