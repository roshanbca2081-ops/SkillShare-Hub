<?php
$page_title = 'Create Course';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_course'])) {
    $title = sanitize($_POST['title']);
    $slug = createSlug($title);
    $description = sanitize($_POST['description']);
    $field_id = (int)$_POST['field_id'];
    $level = sanitize($_POST['level']);
    $price = (float)$_POST['price'];
    $duration = (int)$_POST['duration'];
    $requirements = sanitize($_POST['requirements']);
    $objectives = sanitize($_POST['objectives']);
    $status = 'draft';
    
    // Validate
    $errors = [];
    if (strlen($title) < 5) $errors[] = 'Title must be at least 5 characters.';
    if (strlen($description) < 20) $errors[] = 'Description must be at least 20 characters.';
    if (!$field_id) $errors[] = 'Please select an academic field.';
    if ($duration < 1) $errors[] = 'Duration must be at least 1 hour.';
    
    if (empty($errors)) {
        // Check if slug exists
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug = $slug . '-' . time();
        }
        
        $stmt = $pdo->prepare("INSERT INTO courses (mentor_id, field_id, title, slug, description, level, price, duration, requirements, learning_outcomes, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $field_id, $title, $slug, $description, $level, $price, $duration, $requirements, $objectives, $status]);
        $course_id = $pdo->lastInsertId();
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Course created successfully! Now you can add modules and lessons.'
        ];
        redirect('edit.php?id=' . $course_id);
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => implode('<br>', $errors)
        ];
    }
}

function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
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
                <h1 class="h2">Create New Course</h1>
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
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Course Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" 
                                           placeholder="Enter course title" 
                                           value="<?php echo $_POST['title'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Academic Field <span class="text-danger">*</span></label>
                                    <select name="field_id" class="form-select" required>
                                        <option value="">Select a field</option>
                                        <?php foreach ($fields as $field): ?>
                                        <option value="<?php echo $field['id']; ?>" <?php echo ($_POST['field_id'] ?? 0) == $field['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($field['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="4" 
                                              placeholder="Describe what students will learn" required><?php echo $_POST['description'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Level <span class="text-danger">*</span></label>
                                        <select name="level" class="form-select" required>
                                            <option value="beginner" <?php echo ($_POST['level'] ?? '') == 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                            <option value="intermediate" <?php echo ($_POST['level'] ?? '') == 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                            <option value="advanced" <?php echo ($_POST['level'] ?? '') == 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Duration (hours) <span class="text-danger">*</span></label>
                                        <input type="number" name="duration" class="form-control" 
                                               value="<?php echo $_POST['duration'] ?? ''; ?>" 
                                               min="1" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Price ($)</label>
                                    <input type="number" name="price" class="form-control" 
                                           value="<?php echo $_POST['price'] ?? '0.00'; ?>" 
                                           step="0.01" min="0">
                                    <small class="text-muted">Set to 0 for free courses</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Requirements / Prerequisites</label>
                                    <textarea name="requirements" class="form-control" rows="2" 
                                              placeholder="What do students need to know before starting?"><?php echo $_POST['requirements'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Learning Objectives</label>
                                    <textarea name="objectives" class="form-control" rows="3" 
                                              placeholder="What will students learn? List key objectives..."><?php echo $_POST['objectives'] ?? ''; ?></textarea>
                                </div>
                                
                                <button type="submit" name="create_course" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Course
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
                                    Use a clear, descriptive title
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Write detailed learning objectives
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Specify prerequisites clearly
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Set appropriate price for your content
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-1"></i>
                                    Add modules and lessons after creation
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>