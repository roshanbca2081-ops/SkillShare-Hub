<?php
$page_title = 'Upload Resource';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();

// Get mentor's courses
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE mentor_id = ? AND status IN ('active', 'pending')");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_resource'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $course_id = (int)$_POST['course_id'];
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    
    $errors = [];
    if (strlen($title) < 3) $errors[] = 'Title must be at least 3 characters.';
    if (!$course_id) $errors[] = 'Please select a course.';
    
    if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/resources/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['resource_file']['name'], PATHINFO_EXTENSION));
        $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'mp3', 'wav', 'zip', 'rar', 'txt'];
        $max_size = 50 * 1024 * 1024; // 50MB
        
        if (!in_array($file_ext, $allowed_types)) {
            $errors[] = 'File type not allowed. Allowed: ' . implode(', ', $allowed_types);
        }
        
        if ($_FILES['resource_file']['size'] > $max_size) {
            $errors[] = 'File size must be less than 50MB.';
        }
        
        if (empty($errors)) {
            $file_name = 'resource_' . $user_id . '_' . time() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['resource_file']['tmp_name'], $file_path)) {
                $stmt = $pdo->prepare("INSERT INTO resources (mentor_id, course_id, title, description, file_url, file_type, is_public) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $course_id, $title, $description, 'uploads/resources/' . $file_name, $file_ext, $is_public]);
                
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'icon' => 'check-circle',
                    'message' => 'Resource uploaded successfully!'
                ];
                redirect('index.php');
            } else {
                $errors[] = 'Failed to upload file.';
            }
        }
    } else {
        $errors[] = 'Please select a file to upload.';
    }
    
    if (!empty($errors)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => implode('<br>', $errors)
        ];
    }
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
                <h1 class="h2">Upload Resource</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Resource Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" 
                                           placeholder="Enter resource title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Course <span class="text-danger">*</span></label>
                                    <select name="course_id" class="form-select" required>
                                        <option value="">Select a course</option>
                                        <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>">
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3" 
                                              placeholder="Describe the resource"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">File <span class="text-danger">*</span></label>
                                    <input type="file" name="resource_file" class="form-control" required>
                                    <small class="text-muted">
                                        Allowed: PDF, DOC, PPT, Images, Videos, Audio, ZIP (Max 50MB)
                                    </small>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="is_public" class="form-check-input" id="isPublic" checked>
                                    <label class="form-check-label" for="isPublic">Make this resource public</label>
                                    <br><small class="text-muted">Public resources are visible to all students in the course</small>
                                </div>
                                
                                <button type="submit" name="upload_resource" class="btn btn-primary">
                                    <i class="fas fa-upload"></i> Upload Resource
                                </button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h6><i class="fas fa-info-circle text-primary"></i> Tips</h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Use descriptive titles
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Provide clear descriptions
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Organize by course
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Keep files organized
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-1"></i>
                                    Respect copyright laws
                                </li>
                            </ul>
                            
                            <hr>
                            
                            <h6>Supported File Types</h6>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-light text-dark border">PDF</span>
                                <span class="badge bg-light text-dark border">DOC</span>
                                <span class="badge bg-light text-dark border">PPT</span>
                                <span class="badge bg-light text-dark border">XLS</span>
                                <span class="badge bg-light text-dark border">Images</span>
                                <span class="badge bg-light text-dark border">Videos</span>
                                <span class="badge bg-light text-dark border">Audio</span>
                                <span class="badge bg-light text-dark border">ZIP</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>