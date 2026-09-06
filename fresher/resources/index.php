<?php
$page_title = 'Learning Resources';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';

// Get enrolled courses for filter
$stmt = $pdo->prepare("SELECT c.id, c.title FROM courses c 
                       JOIN enrollments e ON c.id = e.course_id 
                       WHERE e.fresher_id = ? AND e.status != 'dropped' 
                       ORDER BY c.title");
$stmt->execute([$user_id]);
$enrolled_courses = $stmt->fetchAll();

// Build query for resources
$query = "SELECT r.*, c.id as course_id, c.title as course_title, c.thumbnail,
          u.full_name as mentor_name, u.id as mentor_id, u.avatar,
          (SELECT COUNT(*) FROM resources WHERE course_id = r.course_id) as total_resources,
          CASE 
              WHEN r.file_type = 'pdf' THEN 'file-pdf'
              WHEN r.file_type IN ('doc', 'docx') THEN 'file-word'
              WHEN r.file_type IN ('ppt', 'pptx') THEN 'file-powerpoint'
              WHEN r.file_type = 'video' THEN 'file-video'
              WHEN r.file_type = 'audio' THEN 'file-audio'
              WHEN r.file_type = 'zip' THEN 'file-archive'
              WHEN r.file_type IN ('jpg', 'jpeg', 'png', 'gif', 'webp') THEN 'file-image'
              ELSE 'file'
          END as file_icon,
          CASE 
              WHEN r.file_type IN ('jpg', 'jpeg', 'png', 'gif', 'webp') THEN 'image'
              WHEN r.file_type = 'pdf' THEN 'pdf'
              WHEN r.file_type IN ('doc', 'docx') THEN 'document'
              WHEN r.file_type IN ('ppt', 'pptx') THEN 'presentation'
              WHEN r.file_type = 'video' THEN 'video'
              WHEN r.file_type = 'audio' THEN 'audio'
              WHEN r.file_type = 'zip' THEN 'archive'
              ELSE 'other'
          END as file_category
          FROM resources r 
          JOIN courses c ON r.course_id = c.id 
          JOIN users u ON r.mentor_id = u.id 
          JOIN enrollments e ON e.course_id = c.id 
          WHERE e.fresher_id = ? AND e.status != 'dropped'";

$params = [$user_id];

if ($course_id) {
    $query .= " AND r.course_id = ?";
    $params[] = $course_id;
}

if ($search) {
    $query .= " AND (r.title LIKE ? OR r.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($type) {
    $query .= " AND r.file_type = ?";
    $params[] = $type;
}

$query .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$resources = $stmt->fetchAll();

// Get resource statistics
$total_resources = count($resources);
$resource_types = [];

foreach ($resources as $r) {
    $type_key = $r['file_type'] ?? 'other';
    if (!isset($resource_types[$type_key])) {
        $resource_types[$type_key] = 0;
    }
    $resource_types[$type_key]++;
}

// Get all resource types for filter
$stmt = $pdo->query("SELECT DISTINCT file_type FROM resources WHERE file_type IS NOT NULL ORDER BY file_type");
$all_types = $stmt->fetchAll();

// Handle download
if (isset($_GET['download']) && isset($_GET['id'])) {
    $resource_id = (int)$_GET['id'];
    
    // Check if user has access
    $stmt = $pdo->prepare("SELECT r.*, e.fresher_id 
                           FROM resources r 
                           JOIN courses c ON r.course_id = c.id 
                           JOIN enrollments e ON e.course_id = c.id 
                           WHERE r.id = ? AND e.fresher_id = ? AND e.status != 'dropped'");
    $stmt->execute([$resource_id, $user_id]);
    $resource = $stmt->fetch();
    
    if ($resource) {
        // Update download count
        $stmt = $pdo->prepare("UPDATE resources SET download_count = download_count + 1 WHERE id = ?");
        $stmt->execute([$resource_id]);
        
        // Log download
        $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, description, data) 
                               VALUES (?, 'resource_download', 'Downloaded resource', ?)");
        $stmt->execute([$user_id, json_encode(['resource_id' => $resource_id, 'resource_title' => $resource['title']])]);
        
        // Redirect to file
        $file_path = '../../' . $resource['file_url'];
        if (file_exists($file_path)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($resource['file_url']) . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit();
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'message' => 'File not found.'
            ];
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'You do not have access to this resource.'
        ];
    }
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
                <h1 class="h2">Learning Resources</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-primary me-2">
                        <i class="fas fa-file"></i> <?php echo $total_resources; ?> Resources
                    </span>
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search Resources</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by title or description..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Course</label>
                            <select name="course" class="form-select" onchange="this.form.submit()">
                                <option value="">All Courses</option>
                                <?php foreach ($enrolled_courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo $course_id == $course['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">File Type</label>
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <?php foreach ($all_types as $t): ?>
                                <option value="<?php echo htmlspecialchars($t['file_type']); ?>" <?php echo $type == $t['file_type'] ? 'selected' : ''; ?>>
                                    <?php echo strtoupper(htmlspecialchars($t['file_type'])); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <?php if ($search || $course_id || $type): ?>
                                <a href="index.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Resource Stats -->
            <?php if (!empty($resource_types)): ?>
            <div class="row g-2 mb-4">
                <?php foreach ($resource_types as $type_key => $count): ?>
                <div class="col-auto">
                    <span class="badge bg-light text-dark border p-2">
                        <?php 
                        $icon = 'file';
                        if (strpos($type_key, 'pdf') !== false) $icon = 'file-pdf';
                        elseif (strpos($type_key, 'doc') !== false) $icon = 'file-word';
                        elseif (strpos($type_key, 'ppt') !== false) $icon = 'file-powerpoint';
                        elseif (strpos($type_key, 'video') !== false) $icon = 'file-video';
                        elseif (strpos($type_key, 'audio') !== false) $icon = 'file-audio';
                        elseif (strpos($type_key, 'zip') !== false) $icon = 'file-archive';
                        ?>
                        <i class="fas fa-<?php echo $icon; ?>"></i>
                        <?php echo strtoupper(htmlspecialchars($type_key)); ?>: <?php echo $count; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Resources Grid -->
            <?php if (!empty($resources)): ?>
                <div class="row g-4">
                    <?php foreach ($resources as $resource): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="resource-icon me-3" style="font-size: 2.5rem; color: #6C63FF;">
                                        <i class="fas fa-<?php echo $resource['file_icon']; ?>"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1"><?php echo htmlspecialchars($resource['title']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($resource['course_title']); ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <?php if ($resource['description']): ?>
                                <p class="card-text small text-muted">
                                    <?php echo htmlspecialchars(substr($resource['description'], 0, 100)); ?>
                                    <?php if (strlen($resource['description']) > 100): ?>...<?php endif; ?>
                                </p>
                                <?php endif; ?>
                                
                                <div class="resource-meta">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="<?php echo getAvatar($resource); ?>" 
                                             class="rounded-circle me-2" 
                                             style="width: 24px; height: 24px; object-fit: cover;">
                                        <small class="text-muted">by <?php echo htmlspecialchars($resource['mentor_name']); ?></small>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-<?php echo $resource['file_icon']; ?>"></i>
                                            <?php echo strtoupper($resource['file_type'] ?? 'File'); ?>
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo formatDate($resource['created_at']); ?>
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-download"></i>
                                            <?php echo $resource['download_count']; ?> downloads
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="?download=1&id=<?php echo $resource['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="previewResource(<?php echo $resource['id']; ?>)">
                                        <i class="fas fa-eye"></i> Quick Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-4"></i>
                    <h5>No resources available</h5>
                    <p class="text-muted">
                        <?php if ($search || $course_id || $type): ?>
                            No resources match your filters. Try adjusting your search.
                            <br><a href="index.php" class="btn btn-sm btn-primary mt-2">Clear Filters</a>
                        <?php else: ?>
                            Resources from your enrolled courses will appear here.
                            <br><a href="../learning/my-courses.php" class="btn btn-sm btn-primary mt-2">Browse Courses</a>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Resource Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-3x text-primary spinner"></i>
                    <p class="mt-2">Loading preview...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="previewDownloadBtn" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function previewResource(resourceId) {
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    const content = document.getElementById('previewContent');
    const title = document.getElementById('previewTitle');
    
    // Show loading
    content.innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-3x text-primary spinner"></i>
            <p class="mt-2">Loading preview...</p>
        </div>
    `;
    
    // Fetch resource details
    fetch('ajax/resource-details.php?id=' + resourceId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                title.textContent = data.title;
                
                // Set download link
                document.getElementById('previewDownloadBtn').href = '?download=1&id=' + resourceId;
                
                // Show preview based on file type
                let previewHtml = '';
                const fileExt = data.file_type || '';
                
                if (fileExt.match(/jpg|jpeg|png|gif|webp/)) {
                    // Image preview
                    previewHtml = `
                        <div class="text-center">
                            <img src="../../${data.file_url}" class="img-fluid rounded" alt="${data.title}" style="max-height: 500px;">
                        </div>
                    `;
                } else if (fileExt === 'pdf') {
                    // PDF preview
                    previewHtml = `
                        <div class="ratio ratio-16x9">
                            <iframe src="../../${data.file_url}" class="rounded"></iframe>
                        </div>
                    `;
                } else if (fileExt.match(/doc|docx|ppt|pptx/)) {
                    // Document preview - show info
                    previewHtml = `
                        <div class="text-center py-4">
                            <i class="fas fa-${data.file_icon || 'file'} fa-4x text-primary mb-3"></i>
                            <h6>${data.title}</h6>
                            <p class="text-muted">This file type cannot be previewed in the browser.</p>
                            <p class="text-muted small">File: ${data.file_type || 'Unknown'} • ${data.file_size || 'Unknown size'}</p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Download the file to view its contents.
                            </div>
                        </div>
                    `;
                } else if (fileExt.match(/mp4|webm|ogg/)) {
                    // Video preview
                    previewHtml = `
                        <div class="ratio ratio-16x9">
                            <video controls>
                                <source src="../../${data.file_url}" type="video/${fileExt}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    `;
                } else if (fileExt.match(/mp3|wav|ogg/)) {
                    // Audio preview
                    previewHtml = `
                        <div class="text-center py-4">
                            <i class="fas fa-music fa-4x text-primary mb-3"></i>
                            <h6>${data.title}</h6>
                            <audio controls class="w-100 mt-3">
                                <source src="../../${data.file_url}" type="audio/${fileExt}">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    `;
                } else {
                    // Unknown file type
                    previewHtml = `
                        <div class="text-center py-4">
                            <i class="fas fa-${data.file_icon || 'file'} fa-4x text-primary mb-3"></i>
                            <h6>${data.title}</h6>
                            <p class="text-muted">This file type cannot be previewed in the browser.</p>
                            <p class="text-muted small">File: ${data.file_type || 'Unknown'}</p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Download the file to view its contents.
                            </div>
                        </div>
                    `;
                }
                
                content.innerHTML = previewHtml;
            } else {
                content.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                        <p>${data.message || 'Unable to load resource preview.'}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                    <p>An error occurred while loading the preview.</p>
                </div>
            `;
        });
    
    modal.show();
}

// Auto-submit form on course/type change
document.querySelectorAll('select[name="course"], select[name="type"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>

<style>
.resource-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(108, 99, 255, 0.1);
    border-radius: 12px;
}

.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1) !important;
}

.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<?php include '../../includes/footer.php'; ?>