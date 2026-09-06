<?php
$page_title = 'Research Projects';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

// Get research projects
$stmt = $pdo->prepare("SELECT p.*, u.full_name as mentor_name, u.id as mentor_id,
                       (SELECT COUNT(*) FROM research_applications WHERE project_id = p.id) as total_applications,
                       (SELECT status FROM research_applications WHERE project_id = p.id AND fresher_id = ?) as application_status
                       FROM research_projects p 
                       JOIN users u ON p.mentor_id = u.id 
                       WHERE p.status IN ('open', 'in_progress')
                       ORDER BY p.created_at DESC");
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll();

// Get my applications
$stmt = $pdo->prepare("SELECT p.*, u.full_name as mentor_name 
                       FROM research_applications a 
                       JOIN research_projects p ON a.project_id = p.id 
                       JOIN users u ON p.mentor_id = u.id 
                       WHERE a.fresher_id = ? 
                       ORDER BY a.applied_at DESC");
$stmt->execute([$user_id]);
$my_applications = $stmt->fetchAll();

// Handle application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $project_id = (int)$_POST['project_id'];
    $cover_letter = sanitize($_POST['cover_letter'] ?? '');
    
    $stmt = $pdo->prepare("INSERT INTO research_applications (project_id, fresher_id, cover_letter, status) 
                           VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$project_id, $user_id, $cover_letter]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Application submitted successfully!'
    ];
    redirect('index.php');
}
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
                <h1 class="h2">Research Projects</h1>
            </div>
            
            <!-- My Applications -->
            <?php if (!empty($my_applications)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-file-alt"></i> My Applications
                </div>
                <div class="card-body">
                    <?php foreach ($my_applications as $app): ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <h6><?php echo htmlspecialchars($app['title']); ?></h6>
                            <small class="text-muted">with <?php echo htmlspecialchars($app['mentor_name']); ?></small>
                        </div>
                        <span class="badge bg-<?php echo $app['status'] === 'approved' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                            <?php echo ucfirst($app['status']); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Available Projects -->
            <h5 class="mb-3">Available Projects</h5>
            <div class="row g-4">
                <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5><?php echo htmlspecialchars($project['title']); ?></h5>
                                <span class="badge bg-<?php echo $project['status'] === 'open' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                                </span>
                            </div>
                            <p class="text-muted small">by <?php echo htmlspecialchars($project['mentor_name']); ?></p>
                            <p class="small"><?php echo substr($project['description'], 0, 120); ?>...</p>
                            
                            <div class="mb-2">
                                <span class="badge bg-info"><i class="fas fa-users"></i> <?php echo $project['total_applications']; ?> applicants</span>
                                <span class="badge bg-secondary"><i class="fas fa-user"></i> <?php echo $project['max_applicants']; ?> max</span>
                            </div>
                            
                            <?php if ($project['application_status']): ?>
                                <div class="alert alert-info mt-2 mb-0">
                                    <i class="fas fa-info-circle"></i> Application <?php echo $project['application_status']; ?>
                                </div>
                            <?php else: ?>
                                <button class="btn btn-primary w-100" onclick="showApplyModal(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($projects)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                    <h5>No research projects available</h5>
                    <p class="text-muted">Check back later for new opportunities.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Application Modal -->
<div class="modal fade" id="applyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply for Research Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="project_id" id="applyProjectId">
                    <p>Applying for: <strong id="applyProjectTitle"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Cover Letter</label>
                        <textarea name="cover_letter" class="form-control" rows="5" 
                                  placeholder="Why are you interested in this project? What skills do you bring?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="apply" class="btn btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApplyModal(projectId, projectTitle) {
    document.getElementById('applyProjectId').value = projectId;
    document.getElementById('applyProjectTitle').textContent = projectTitle;
    new bootstrap.Modal(document.getElementById('applyModal')).show();
}
</script>

<?php include '../../includes/footer.php'; ?>