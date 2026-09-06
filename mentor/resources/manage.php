<?php
$page_title = 'Manage Resources';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();

// Get all resources
$stmt = $pdo->prepare("SELECT r.*, c.title as course_title 
                       FROM resources r 
                       JOIN courses c ON r.course_id = c.id 
                       WHERE r.mentor_id = ? 
                       ORDER BY r.created_at DESC");
$stmt->execute([$user_id]);
$resources = $stmt->fetchAll();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get file path
    $stmt = $pdo->prepare("SELECT file_url FROM resources WHERE id = ? AND mentor_id = ?");
    $stmt->execute([$id, $user_id]);
    $resource = $stmt->fetch();
    
    if ($resource) {
        // Delete file
        $file_path = '../../' . $resource['file_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM resources WHERE id = ? AND mentor_id = ?");
        $stmt->execute([$id, $user_id]);
        
        $_SESSION['alert'] = [
            'type' => 'warning',
            'icon' => 'exclamation-circle',
            'message' => 'Resource deleted successfully.'
        ];
    }
    redirect('manage.php');
}

// Handle toggle public
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE resources SET is_public = NOT is_public WHERE id = ? AND mentor_id = ?");
    $stmt->execute([$id, $user_id]);
    redirect('manage.php');
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
                <h1 class="h2">Manage Resources</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="upload.php" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload New
                    </a>
                </div>
            </div>
            
            <?php if (!empty($resources)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Type</th>
                                        <th>Downloads</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resources as $resource): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($resource['title']); ?></td>
                                        <td><?php echo htmlspecialchars($resource['course_title']); ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo strtoupper($resource['file_type'] ?? 'File'); ?></span>
                                        </td>
                                        <td><?php echo $resource['download_count']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $resource['is_public'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $resource['is_public'] ? 'Public' : 'Private'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="edit.php?id=<?php echo $resource['id']; ?>" class="btn btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?toggle=1&id=<?php echo $resource['id']; ?>" class="btn btn-info">
                                                    <i class="fas fa-<?php echo $resource['is_public'] ? 'lock' : 'unlock'; ?>"></i>
                                                </a>
                                                <a href="?delete=1&id=<?php echo $resource['id']; ?>" class="btn btn-danger" 
                                                   onclick="return confirm('Delete this resource?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5>No resources uploaded</h5>
                    <p class="text-muted">Upload resources to share with your students.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>