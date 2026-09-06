<?php
$page_title = 'Certificates';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT c.*, cr.title as course_title, u.full_name as student_name, u.email
                       FROM certificates c 
                       JOIN courses cr ON c.course_id = cr.id 
                       JOIN users u ON c.fresher_id = u.id 
                       WHERE cr.mentor_id = ? 
                       ORDER BY c.issued_at DESC");
$stmt->execute([$user_id]);
$certificates = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM certificates c JOIN courses cr ON c.course_id = cr.id WHERE cr.mentor_id = ?");
$stmt->execute([$user_id]);
$total_count = $stmt->fetchColumn();
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
                <h1 class="h2">Certificates</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-primary me-2">
                        <i class="fas fa-certificate"></i> <?php echo $total_count; ?> Issued
                    </span>
                    <a href="issue.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Issue Certificate
                    </a>
                </div>
            </div>
            
            <?php if (!empty($certificates)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Certificate Code</th>
                                        <th>Issued</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($certificates as $cert): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cert['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($cert['course_title']); ?></td>
                                        <td><code><?php echo $cert['certificate_code']; ?></code></td>
                                        <td><?php echo formatDate($cert['issued_at']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $cert['is_valid'] ? 'success' : 'danger'; ?>">
                                                <?php echo $cert['is_valid'] ? 'Valid' : 'Expired'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="view.php?id=<?php echo $cert['id']; ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="download.php?id=<?php echo $cert['id']; ?>" class="btn btn-outline-secondary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <?php if ($cert['is_valid']): ?>
                                                    <a href="revoke.php?id=<?php echo $cert['id']; ?>" class="btn btn-outline-danger" 
                                                       onclick="return confirm('Revoke this certificate?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                    <h5>No certificates issued</h5>
                    <p class="text-muted">Issue certificates to students who complete your courses.</p>
                    <a href="issue.php" class="btn btn-primary">Issue Certificate</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>