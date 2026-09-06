<?php
$page_title = 'Edit Course';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get course
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND mentor_id = ?");
$stmt->execute([$course_id, $user_id]);
$course = $stmt->fetch();

if (!$course) {
    redirect('index.php');
}

$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name")->fetchAll();

// Get modules
$stmt = $pdo->prepare("SELECT * FROM course_modules WHERE course_id = ? ORDER BY order_number");
$stmt->execute([$course_id]);
$modules = $stmt->fetchAll();

// Get lessons for each module
foreach ($modules as &$module) {
    $stmt = $pdo->prepare("SELECT * FROM course_lessons WHERE module_id = ? ORDER BY order_number");
    $stmt->execute([$module['id']]);
    $module['lessons'] = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $field_id = (int)$_POST['field_id'];
    $level = sanitize($_POST['level']);
    $price = (float)$_POST['price'];
    $duration = (int)$_POST['duration'];
    $requirements = sanitize($_POST['requirements']);
    $objectives = sanitize($_POST['objectives']);
    $status = sanitize($_POST['status']);
    
    $stmt = $pdo->prepare("UPDATE courses SET 
                           title = ?, description = ?, field_id = ?, level = ?, 
                           price = ?, duration = ?, requirements = ?, learning_outcomes = ?, status = ? 
                           WHERE id = ? AND mentor_id = ?");
    $stmt->execute([$title, $description, $field_id, $level, $price, $duration, $requirements, $objectives, $status, $course_id, $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Course updated successfully!'
    ];
    redirect('edit.php?id=' . $course_id);
}

// Add module
if (isset($_POST['add_module'])) {
    $module_title = sanitize($_POST['module_title']);
    $order_number = (int)$_POST['order_number'];
    
    $stmt = $pdo->prepare("INSERT INTO course_modules (course_id, title, order_number) VALUES (?, ?, ?)");
    $stmt->execute([$course_id, $module_title, $order_number]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Module added successfully!'
    ];
    redirect('edit.php?id=' . $course_id);
}

// Delete module
if (isset($_GET['delete_module']) && isset($_GET['module_id'])) {
    $module_id = (int)$_GET['module_id'];
    $stmt = $pdo->prepare("DELETE FROM course_modules WHERE id = ? AND course_id = ?");
    $stmt->execute([$module_id, $course_id]);
    
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Module deleted successfully.'
    ];
    redirect('edit.php?id=' . $course_id);
}

// Add lesson
if (isset($_POST['add_lesson'])) {
    $module_id = (int)$_POST['module_id'];
    $lesson_title = sanitize($_POST['lesson_title']);
    $order_number = (int)$_POST['lesson_order'];
    $video_url = sanitize($_POST['video_url']);
    $content = sanitize($_POST['lesson_content']);
    $duration = (int)$_POST['lesson_duration'];
    $is_free = isset($_POST['is_free']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO course_lessons (module_id, title, video_url, content, duration, order_number, is_free) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$module_id, $lesson_title, $video_url, $content, $duration, $order_number, $is_free]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Lesson added successfully!'
    ];
    redirect('edit.php?id=' . $course_id);
}

// Delete lesson
if (isset($_GET['delete_lesson']) && isset($_GET['lesson_id'])) {
    $lesson_id = (int)$_GET['lesson_id'];
    $stmt = $pdo->prepare("DELETE FROM course_lessons WHERE id = ?");
    $stmt->execute([$lesson_id]);
    
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Lesson deleted successfully.'
    ];
    redirect('edit.php?id=' . $course_id);
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
                <h1 class="h2">Edit Course</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <ul class="nav nav-tabs mb-4" id="courseTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#basic">Basic Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#modules">Modules & Lessons</a>
                </li>
            </ul>
            
            <div class="tab-content">
                <!-- Basic Info Tab -->
                <div class="tab-pane fade show active" id="basic">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Course Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" 
                                                   value="<?php echo htmlspecialchars($course['title']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Academic Field <span class="text-danger">*</span></label>
                                            <select name="field_id" class="form-select" required>
                                                <option value="">Select a field</option>
                                                <?php foreach ($fields as $field): ?>
                                                <option value="<?php echo $field['id']; ?>" <?php echo $course['field_id'] == $field['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($field['name']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Description <span class="text-danger">*</span></label>
                                            <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Level <span class="text-danger">*</span></label>
                                                <select name="level" class="form-select" required>
                                                    <option value="beginner" <?php echo $course['level'] === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                                    <option value="intermediate" <?php echo $course['level'] === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                                    <option value="advanced" <?php echo $course['level'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Duration (hours) <span class="text-danger">*</span></label>
                                                <input type="number" name="duration" class="form-control" 
                                                       value="<?php echo $course['duration']; ?>" min="1" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Price ($)</label>
                                            <input type="number" name="price" class="form-control" 
                                                   value="<?php echo $course['price']; ?>" step="0.01" min="0">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Requirements / Prerequisites</label>
                                            <textarea name="requirements" class="form-control" rows="2"><?php echo htmlspecialchars($course['requirements']); ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Learning Objectives</label>
                                            <textarea name="objectives" class="form-control" rows="3"><?php echo htmlspecialchars($course['learning_outcomes']); ?></textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="draft" <?php echo $course['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                <option value="pending" <?php echo $course['status'] === 'pending' ? 'selected' : ''; ?>>Pending Review</option>
                                                <option value="active" <?php echo $course['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="archived" <?php echo $course['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                            </select>
                                            <small class="text-muted">Set to "Pending Review" to submit for admin approval</small>
                                        </div>
                                        
                                        <button type="submit" name="update_course" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Course
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modules Tab -->
                <div class="tab-pane fade" id="modules">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0">Add New Module</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" class="row g-3">
                                        <div class="col-md-6">
                                            <input type="text" name="module_title" class="form-control" placeholder="Module Title" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="order_number" class="form-control" placeholder="Order" value="<?php echo count($modules) + 1; ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" name="add_module" class="btn btn-primary w-100">
                                                <i class="fas fa-plus"></i> Add Module
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <?php foreach ($modules as $module): ?>
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        Module <?php echo $module['order_number']; ?>: <?php echo htmlspecialchars($module['title']); ?>
                                    </h6>
                                    <div>
                                        <span class="badge bg-secondary"><?php echo count($module['lessons']); ?> lessons</span>
                                        <a href="?delete_module=1&module_id=<?php echo $module['id']; ?>&id=<?php echo $course_id; ?>" 
                                           class="btn btn-sm btn-danger" onclick="return confirm('Delete this module?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Add Lesson Form -->
                                    <form method="POST" class="row g-2 mb-3">
                                        <input type="hidden" name="module_id" value="<?php echo $module['id']; ?>">
                                        <div class="col-md-4">
                                            <input type="text" name="lesson_title" class="form-control form-control-sm" placeholder="Lesson Title" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" name="lesson_duration" class="form-control form-control-sm" placeholder="Duration (min)" value="10">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="lesson_order" class="form-control form-control-sm" placeholder="Order" value="<?php echo count($module['lessons']) + 1; ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_free" id="isFree<?php echo $module['id']; ?>">
                                                <label class="form-check-label" for="isFree<?php echo $module['id']; ?>">Free</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="text" name="video_url" class="form-control form-control-sm" placeholder="Video URL (optional)">
                                        </div>
                                        <div class="col-md-12">
                                            <textarea name="lesson_content" class="form-control form-control-sm" rows="2" placeholder="Lesson content..."></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" name="add_lesson" class="btn btn-sm btn-success">
                                                <i class="fas fa-plus"></i> Add Lesson
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Lessons List -->
                                    <?php if (!empty($module['lessons'])): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($module['lessons'] as $lesson): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-secondary me-2"><?php echo $lesson['order_number']; ?></span>
                                                <?php echo htmlspecialchars($lesson['title']); ?>
                                                <?php if ($lesson['is_free']): ?>
                                                    <span class="badge bg-success">Free</span>
                                                <?php endif; ?>
                                                <small class="text-muted ms-2"><?php echo $lesson['duration']; ?> min</small>
                                            </div>
                                            <div>
                                                <a href="?delete_lesson=1&lesson_id=<?php echo $lesson['id']; ?>&id=<?php echo $course_id; ?>" 
                                                   class="btn btn-sm btn-danger" onclick="return confirm('Delete this lesson?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                    <p class="text-muted small mb-0">No lessons in this module.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($modules)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-layer-group fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No modules yet. Add your first module above.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>