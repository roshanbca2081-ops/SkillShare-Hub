<?php
$page_title = 'My Submissions';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT s.*, a.title as assignment_title, a.max_score, c.title as course_title,
                       a.due_date, u.full_name as mentor_name
                       FROM assignment_submissions s 
                       JOIN assignments a ON s.assignment_id = a.id 
                       JOIN courses c ON a.course_id = c.id 
                       JOIN users u ON a.mentor_id = u.id 
                       WHERE s.fresher_id = ? 
                       ORDER BY s.submitted_at DESC");
$stmt->execute([$user_id]);
$submissions = $stmt->fetchAll();
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
                <h1 class="h2">My Submissions</h1>
            </div>
            
            <?php if (!empty($submissions)): ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Assignment</th>
                                <th>Course</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($submission['assignment_title']); ?></td>
                                <td><?php echo htmlspecialchars($submission['course_title']); ?></td>
                                <td><?php echo formatDateTime($submission['submitted_at']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $submission['status'] === 'graded' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($submission['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($submission['score'] !== null): ?>
                                        <?php echo $submission['score']; ?>/<?php echo $submission['max_score']; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="details.php?id=<?php echo $submission['assignment_id']; ?>" class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5>No submissions</h5>
                    <p class="text-muted">You haven't submitted any assignments yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>