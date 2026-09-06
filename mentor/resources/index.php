<?php
$page_title = 'Resources';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;

$query = "SELECT r.*, c.title as course_title,
          (SELECT COUNT(*) FROM resources WHERE course_id = r.course_id) as total_resources
          FROM resources r 
          JOIN courses c ON r.course_id = c.id 
          WHERE r.mentor_id = ?";

$params = [$user_id];

if ($course_id) {
    $query .= " AND r.course_id = ?";
    $params[] = $course_id;
}

$query .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$resources = $stmt->fetchAll();

// Get courses for filter
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE mentor_id = ? AND status IN ('active', 'pending')");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();
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
                <h1 class="h2">Resources</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="upload.php" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Resource
                    </a>
                </div>
            </div>
            
            <!-- Filter -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <select name="course" class="form-select" onchange="this.form.submit()">
                                <option value="">All Courses</option>
                                <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo $course_id == $course['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <a href="index.php" class="btn btn-outline-secondary w-100">Clear Filter</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if (!empty($resources)): ?>
                <div class="row g-4">
                    <?php foreach ($resources as $resource): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="resource-icon me-3" style="font-size: 2rem; color: #6C63FF;">
                                        <i class="fas fa-<?php 
                                            $icon = 'file';
                                            if (strpos($resource['file_type'], 'pdf') !== false) $icon = 'file-pdf';
                                            elseif (strpos($resource['file_type'], 'doc') !== false) $icon = 'file-word';
                                            elseif (strpos($resource['file_type'], 'ppt') !== false) $icon = 'file-powerpoint';
                                            elseif (strpos($resource['file_type'], 'video') !== false) $icon = 'file-video';
                                            elseif (strpos($resource['file_type'], 'audio') !== false) $icon = 'file-audio';
                                            elseif (strpos($resource['file_type'], 'zip') !== false) $icon = 'file-archive';
                                            echo $icon;
                                        ?>"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($resource['title']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($resource['course_title']); ?></small>
                                    </div>
                                </div>
                                
                                <?php if ($resource['description']): ?>
                                <p class="small text-muted"><?php echo substr($resource['description'], 0, 100); ?>...</p>
                                <?php endif; ?>
                                
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-file"></i> <?php echo strtoupper($resource['file_type'] ?? 'File'); ?>
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-download"></i> <?php echo $resource['download_count']; ?> downloads
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-calendar"></i> <?php echo formatDate($resource['created_at']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="../../uploads/resources/<?php echo basename($resource['file_url']); ?>" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="edit.php?id=<?php echo $resource['id']; ?>" class="btn btn-sm btn-warning flex-grow-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete.php?id=<?php echo $resource['id']; ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Delete this resource?')">
                                            <i class="fas fa-trash"></i>
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
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5>No resources uploaded</h5>
                    <p class="text-muted">Upload resources to share with your students.</p>
                    <a href="upload.php" class="btn btn-primary">Upload Resource</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>