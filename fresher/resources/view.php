<?php
$page_title = 'Resource Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$resource_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$resource_id) {
    redirect('index.php');
}

// Get resource details with access check
$stmt = $pdo->prepare("SELECT r.*, c.id as course_id, c.title as course_title, c.thumbnail,
                       u.full_name as mentor_name, u.id as mentor_id, u.avatar,
                       (SELECT COUNT(*) FROM resources WHERE course_id = r.course_id) as total_resources,
                       (SELECT COUNT(*) FROM resources WHERE course_id = r.course_id AND created_at > r.created_at) as newer_resources,
                       (SELECT COUNT(*) FROM resources WHERE course_id = r.course_id AND created_at < r.created_at) as older_resources
                       FROM resources r 
                       JOIN courses c ON r.course_id = c.id 
                       JOIN users u ON r.mentor_id = u.id 
                       JOIN enrollments e ON e.course_id = c.id 
                       WHERE r.id = ? AND e.fresher_id = ? AND e.status != 'dropped'");
$stmt->execute([$resource_id, $user_id]);
$resource = $stmt->fetch();

if (!$resource) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'icon' => 'exclamation-circle',
        'message' => 'Resource not found or you do not have access.'
    ];
    redirect('index.php');
}

// Get related resources from same course
$stmt = $pdo->prepare("SELECT id, title, file_type, created_at, download_count 
                       FROM resources 
                       WHERE course_id = ? AND id != ? 
                       ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$resource['course_id'], $resource_id]);
$related_resources = $stmt->fetchAll();

// Determine file icon
$file_icon = 'file';
if (strpos($resource['file_type'], 'pdf') !== false) $file_icon = 'file-pdf';
elseif (strpos($resource['file_type'], 'doc') !== false) $file_icon = 'file-word';
elseif (strpos($resource['file_type'], 'ppt') !== false) $file_icon = 'file-powerpoint';
elseif (strpos($resource['file_type'], 'video') !== false) $file_icon = 'file-video';
elseif (strpos($resource['file_type'], 'audio') !== false) $file_icon = 'file-audio';
elseif (strpos($resource['file_type'], 'zip') !== false) $file_icon = 'file-archive';
elseif (in_array($resource['file_type'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $file_icon = 'file-image';

// Get file size
$file_size = '';
$file_path = '../../' . $resource['file_url'];
if (file_exists($file_path)) {
    $bytes = filesize($file_path);
    if ($bytes >= 1073741824) {
        $file_size = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $file_size = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $file_size = number_format($bytes / 1024, 2) . ' KB';
    } else {
        $file_size = $bytes . ' B';
    }
}

// Handle download
if (isset($_GET['download'])) {
    // Update download count
    $stmt = $pdo->prepare("UPDATE resources SET download_count = download_count + 1 WHERE id = ?");
    $stmt->execute([$resource_id]);
    
    // Log download
    $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, description, data) 
                           VALUES (?, 'resource_download', 'Downloaded resource', ?)");
    $stmt->execute([$user_id, json_encode(['resource_id' => $resource_id, 'resource_title' => $resource['title']])]);
    
    // Redirect to file
    if (file_exists($file_path)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($resource['file_url']) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit();
    }
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
                <h1 class="h2">Resource Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> All Resources
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Resource Main -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="resource-icon me-3" style="font-size: 3rem; color: #6C63FF;">
                                    <i class="fas fa-<?php echo $file_icon; ?>"></i>
                                </div>
                                <div>
                                    <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                    <p class="text-muted">
                                        <i class="fas fa-book"></i> 
                                        <?php echo htmlspecialchars($resource['course_title']); ?>
                                    </p>
                                </div>
                            </div>
                            
                            <?php if ($resource['description']): ?>
                            <div class="resource-description mb-4">
                                <h5>Description</h5>
                                <p><?php echo nl2br(htmlspecialchars($resource['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <div class="resource-info row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">File Type</small>
                                        <p class="mb-0"><i class="fas fa-<?php echo $file_icon; ?>"></i> <?php echo strtoupper($resource['file_type'] ?? 'Unknown'); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">File Size</small>
                                        <p class="mb-0"><i class="fas fa-weight"></i> <?php echo $file_size ?: 'Unknown'; ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Downloads</small>
                                        <p class="mb-0"><i class="fas fa-download"></i> <?php echo $resource['download_count']; ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- File Preview -->
                            <?php if (in_array($resource['file_type'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                <div class="resource-preview mb-4">
                                    <h5>Preview</h5>
                                    <div class="text-center">
                                        <img src="../../<?php echo $resource['file_url']; ?>" 
                                             class="img-fluid rounded" 
                                             alt="<?php echo htmlspecialchars($resource['title']); ?>"
                                             style="max-height: 400px;">
                                    </div>
                                </div>
                            <?php elseif ($resource['file_type'] === 'pdf'): ?>
                                <div class="resource-preview mb-4">
                                    <h5>Preview</h5>
                                    <div class="ratio ratio-16x9">
                                        <iframe src="../../<?php echo $resource['file_url']; ?>" class="rounded"></iframe>
                                    </div>
                                </div>
                            <?php elseif (in_array($resource['file_type'], ['mp4', 'webm', 'ogg'])): ?>
                                <div class="resource-preview mb-4">
                                    <h5>Preview</h5>
                                    <div class="ratio ratio-16x9">
                                        <video controls>
                                            <source src="../../<?php echo $resource['file_url']; ?>" type="video/<?php echo $resource['file_type']; ?>">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                </div>
                            <?php elseif (in_array($resource['file_type'], ['mp3', 'wav', 'ogg'])): ?>
                                <div class="resource-preview mb-4">
                                    <h5>Preview</h5>
                                    <div class="text-center py-4">
                                        <i class="fas fa-music fa-3x text-primary mb-3"></i>
                                        <audio controls class="w-100">
                                            <source src="../../<?php echo $resource['file_url']; ?>" type="audio/<?php echo $resource['file_type']; ?>">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Mentor Info -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>Uploaded by</h5>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo getAvatar($resource); ?>" 
                                     class="rounded-circle me-3" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($resource['mentor_name']); ?></h6>
                                    <small class="text-muted">
                                        Uploaded on <?php echo formatDateTime($resource['created_at']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Actions -->
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h5>Actions</h5>
                            
                            <div class="d-grid gap-2">
                                <a href="?download=1" class="btn btn-primary btn-lg">
                                    <i class="fas fa-download"></i> Download Resource
                                </a>
                                
                                <a href="../learning/course.php?id=<?php echo $resource['course_id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-book-open"></i> View Course
                                </a>
                                
                                <a href="../mentors/details.php?id=<?php echo $resource['mentor_id']; ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-user"></i> View Mentor Profile
                                </a>
                            </div>
                            
                            <hr>
                            
                            <!-- Resource Stats -->
                            <div class="resource-stats">
                                <h6>Resource Statistics</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <span class="badge bg-primary d-block py-2 mb-1">
                                            <?php echo $resource['download_count']; ?>
                                        </span>
                                        <small class="text-muted">Downloads</small>
                                    </div>
                                    <div class="col-4">
                                        <span class="badge bg-success d-block py-2 mb-1">
                                            <?php echo $resource['total_resources']; ?>
                                        </span>
                                        <small class="text-muted">Course Resources</small>
                                    </div>
                                    <div class="col-4">
                                        <span class="badge bg-info d-block py-2 mb-1">
                                            <?php echo $resource['newer_resources']; ?>
                                        </span>
                                        <small class="text-muted">Newer Resources</small>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Related Resources -->
                            <?php if (!empty($related_resources)): ?>
                            <h6>Other Resources in this Course</h6>
                            <ul class="list-unstyled small">
                                <?php foreach ($related_resources as $related): ?>
                                <li class="mb-2">
                                    <a href="view.php?id=<?php echo $related['id']; ?>" class="text-decoration-none">
                                        <i class="fas fa-file text-primary me-1"></i>
                                        <?php echo htmlspecialchars(substr($related['title'], 0, 30)); ?>
                                        <?php if (strlen($related['title']) > 30): ?>...<?php endif; ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo $related['download_count']; ?> downloads
                                        </small>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="index.php?course=<?php echo $resource['course_id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-list"></i> View All Resources
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>