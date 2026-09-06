<?php
$page_title = 'Research Projects';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';

$query = "SELECT r.*, 
          (SELECT COUNT(*) FROM research_applications WHERE project_id = r.id) as total_applications,
          (SELECT COUNT(*) FROM research_applications WHERE project_id = r.id AND status = 'approved') as approved_applications
          FROM research_projects r 
          WHERE r.mentor_id = ?";

$params = [$user_id];

if ($filter === 'open') {
    $query .= " AND r.status = 'open'";
} elseif ($filter === 'in_progress') {
    $query .= " AND r.status = 'in_progress'";
} elseif ($filter === 'completed') {
    $query .= " AND r.status = 'completed'";
} elseif ($filter === 'closed') {
    $query .= " AND r.status = 'closed'";
}

$query .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Get counts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM research_projects WHERE mentor_id = ? AND status = 'open'");
$stmt->execute([$user_id]);
$open_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM research_projects WHERE mentor_id = ? AND status = 'in_progress'");
$stmt->execute([$user_id]);
$in_progress_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM research_projects WHERE mentor_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$completed_count = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_project'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $objectives = sanitize($_POST['objectives']);
    $required_skills = sanitize($_POST['required_skills']);
    $max_applicants = (int)$_POST['max_applicants'];
    $status = 'open';
    
    $stmt = $pdo->prepare("INSERT INTO research_projects (mentor_id, title, description, objectives, required_skills, max_applicants, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $description, $objectives, $required_skills, $max_applicants, $status]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Research project created successfully!'
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
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Research Projects</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                    <i class="fas fa-plus"></i> New Project
                </button>
            </div>
            
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-success"><?php echo $open_count; ?></h3>
                            <small class="text-muted">Open</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-primary"><?php echo $in_progress_count; ?></h3>
                            <small class="text-muted">In Progress</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-secondary"><?php echo $completed_count; ?></h3>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-info"><?php echo count($projects); ?></h3>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'all' ? 'active' : ''; ?>" href="?filter=all">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'open' ? 'active' : ''; ?>" href="?filter=open">Open</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'in_progress' ? 'active' : ''; ?>" href="?filter=in_progress">In Progress</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'completed' ? 'active' : ''; ?>" href="?filter=completed">Completed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'closed' ? 'active' : ''; ?>" href="?filter=closed">Closed</a>
                </li>
            </ul>
            
            <!-- Projects List -->
            <?php if (!empty($projects)): ?>
                <div class="row g-4">
                    <?php foreach ($projects as $project): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5><?php echo htmlspecialchars($project['title']); ?></h5>
                                    <span class="badge bg-<?php 
                                        echo $project['status'] === 'open' ? 'success' : 
                                            ($project['status'] === 'in_progress' ? 'primary' : 
                                            ($project['status'] === 'completed' ? 'secondary' : 'danger')); 
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $project['status'])); ?>
                                    </span>
                                </div>
                                <p class="text-muted small"><?php echo substr($project['description'], 0, 100); ?>...</p>
                                
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-users"></i> <?php echo $project['total_applications']; ?> applicants
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-user-plus"></i> <?php echo $project['max_applicants']; ?> max
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-check"></i> <?php echo $project['approved_applications']; ?> approved
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="review.php?id=<?php echo $project['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-users"></i> Manage Applications
                                    </a>
                                    <a href="edit.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                    <h5>No research projects</h5>
                    <p class="text-muted">Create your first research project.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Project Modal -->
<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Research Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Objectives</label>
                        <textarea name="objectives" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Required Skills</label>
                        <textarea name="required_skills" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Applicants</label>
                        <input type="number" name="max_applicants" class="form-control" value="5" min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_project" class="btn btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>