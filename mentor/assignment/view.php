<?php
$page_title = 'Assignment Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$assignment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT a.*, c.title as course_title,
                       (SELECT COUNT(*) FROM assignment_submissions WHERE assignment_id = a.id) as total_submissions,
                       (SELECT COUNT(*) FROM assignment_submissions WHERE assignment_id = a.id AND status = 'graded') as graded_submissions,
                       (SELECT AVG(score) FROM assignment_submissions WHERE assignment_id = a.id AND status = 'graded') as avg_score
                       FROM assignments a 
                       JOIN courses c ON a.course_id = c.id 
                       WHERE a.id = ? AND a.mentor_id = ?");
$stmt->execute([$assignment_id, $user_id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    redirect('index.php');
}

// Get submissions
$stmt = $pdo->prepare("SELECT s.*, u.full_name, u.email, u.avatar 
                       FROM assignment_submissions s 
                       JOIN users u ON s.fresher_id = u.id 
                       WHERE s.assignment_id = ? 
                       ORDER BY s.submitted_at DESC");
$stmt->execute([$assignment_id]);
$submissions = $stmt->fetchAll();
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
                <h1 class="h2">Assignment Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="edit.php?id=<?php echo $assignment_id; ?>" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="grade.php?id=<?php echo $assignment_id; ?>" class="btn btn-success me-2">
                        <i class="fas fa-graduation-cap"></i> Grade
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                            <p class="text-muted"><?php echo htmlspecialchars($assignment['course_title']); ?></p>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Due Date</small>
                                        <p class="mb-0">
                                            <i class="fas fa-calendar text-primary"></i> 
                                            <?php echo formatDateTime($assignment['due_date']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Max Score</small>
                                        <p class="mb-0">
                                            <i class="fas fa-star text-primary"></i> 
                                            <?php echo $assignment['max_score']; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Weight</small>
                                        <p class="mb-0">
                                            <i class="fas fa-weight text-primary"></i> 
                                            <?php echo $assignment['weight']; ?>%
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($assignment['description']): ?>
                            <div class="mt-3">
                                <h6>Description</h6>
                                <p><?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($assignment['instructions']): ?>
                            <div class="mt-3">
                                <h6>Instructions</h6>
                                <p><?php echo nl2br(htmlspecialchars($assignment['instructions'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Submissions -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">
                                <i class="fas fa-file-upload"></i> Submissions 
                                <span class="badge bg-secondary"><?php echo count($submissions); ?> total</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($submissions)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Submitted</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($submission['full_name']); ?></td>
                                            <td><?php echo formatDateTime($submission['submitted_at']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $submission['status'] === 'graded' ? 'success' : 'warning'; 
                                                ?>">
                                                    <?php echo ucfirst($submission['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($submission['score'] !== null): ?>
                                                    <?php echo $submission['score']; ?> / <?php echo $assignment['max_score']; ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="view-submission.php?id=<?php echo $submission['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No submissions yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h6>Statistics</h6>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Total Submissions</span>
                                <strong><?php echo $assignment['total_submissions']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Graded</span>
                                <strong><?php echo $assignment['graded_submissions']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Pending</span>
                                <strong><?php echo $assignment['total_submissions'] - $assignment['graded_submissions']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2">
                                <span class="text-muted">Average Score</span>
                                <strong><?php echo number_format($assignment['avg_score'] ?? 0, 1); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>