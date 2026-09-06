<?php
$page_title = 'Research Project Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT p.*, u.full_name as mentor_name, u.id as mentor_id, u.avatar,
                       (SELECT COUNT(*) FROM research_applications WHERE project_id = p.id) as total_applications,
                       (SELECT status FROM research_applications WHERE project_id = p.id AND fresher_id = ?) as application_status
                       FROM research_projects p 
                       JOIN users u ON p.mentor_id = u.id 
                       WHERE p.id = ?");
$stmt->execute([$user_id, $project_id]);
$project = $stmt->fetch();

if (!$project) {
    redirect('index.php');
}
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
                <h1 class="h2">Project Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                                <span class="badge bg-<?php echo $project['status'] === 'open' ? 'success' : 'warning'; ?> fs-6">
                                    <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                                </span>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?php echo getAvatar($project); ?>" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                <div>
                                    <small class="text-muted">Project Lead</small>
                                    <h6><?php echo htmlspecialchars($project['mentor_name']); ?></h6>
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Status</small>
                                        <p class="mb-0"><?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Applicants</small>
                                        <p class="mb-0"><?php echo $project['total_applications']; ?> / <?php echo $project['max_applicants']; ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Timeline</small>
                                        <p class="mb-0"><?php echo $project['start_date'] ? formatDate($project['start_date']) : 'TBD'; ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <h5>Description</h5>
                            <p><?php echo nl2br(htmlspecialchars($project['description'] ?? '')); ?></p>
                            
                            <?php if ($project['objectives']): ?>
                            <h5>Objectives</h5>
                            <p><?php echo nl2br(htmlspecialchars($project['objectives'])); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($project['required_skills']): ?>
                            <h5>Required Skills</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (explode(',', $project['required_skills']) as $skill): ?>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h5>Apply for this Project</h5>
                            
                            <?php if ($project['application_status']): ?>
                                <div class="alert alert-<?php echo $project['application_status'] === 'approved' ? 'success' : ($project['application_status'] === 'rejected' ? 'danger' : 'info'); ?>">
                                    <i class="fas fa-info-circle"></i> 
                                    Application <?php echo ucfirst($project['application_status']); ?>
                                    <?php if ($project['application_status'] === 'pending'): ?>
                                        <br><small>Waiting for mentor approval</small>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($project['status'] === 'open' && $project['total_applications'] < $project['max_applicants']): ?>
                                <button class="btn btn-primary w-100" onclick="showApplyModal(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </button>
                            <?php elseif ($project['status'] === 'open'): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-users"></i> Maximum applicants reached
                                </div>
                            <?php else: ?>
                                <div class="alert alert-secondary">
                                    <i class="fas fa-lock"></i> This project is not accepting applications
                                </div>
                            <?php endif; ?>
                            
                            <hr>
                            <div class="small text-muted">
                                <p><i class="fas fa-calendar-alt"></i> Posted: <?php echo formatDate($project['created_at']); ?></p>
                                <?php if ($project['end_date']): ?>
                                <p><i class="fas fa-clock"></i> Expected end: <?php echo formatDate($project['end_date']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
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
            <form method="POST" action="index.php">
                <div class="modal-body">
                    <input type="hidden" name="project_id" id="applyProjectId">
                    <p>Applying for: <strong id="applyProjectTitle"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Cover Letter</label>
                        <textarea name="cover_letter" class="form-control" rows="5" 
                                  placeholder="Why are you interested in this project? What skills do you bring?" required></textarea>
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