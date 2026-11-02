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
    $is_published = isset($_POST['module_is_published']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO course_modules (course_id, title, order_number, is_published) VALUES (?, ?, ?, ?)");
    $stmt->execute([$course_id, $module_title, $order_number, $is_published]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Module added successfully!'
    ];
    redirect('edit.php?id=' . $course_id);
}

// Update module details and learner visibility.
if (isset($_POST['update_module'])) {
    $module_id = (int)($_POST['module_id'] ?? 0);
    $module_title = trim(sanitize($_POST['module_title'] ?? ''));
    $order_number = max(0, (int)($_POST['module_order'] ?? 0));
    $is_published = isset($_POST['module_is_published']) ? 1 : 0;
    if ($module_id && $module_title !== '') {
        $stmt = $pdo->prepare('UPDATE course_modules SET title = ?, order_number = ?, is_published = ? WHERE id = ? AND course_id = ?');
        $stmt->execute([$module_title, $order_number, $is_published, $module_id, $course_id]);
    }
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Module updated. Published modules are visible to enrolled freshers.',
    ];
    redirect('edit.php?id=' . $course_id . '#modules');
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
    $resource_url = trim(sanitize($_POST['resource_url'] ?? ''));
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    // Never allow a mentor to add content to another mentor's course.
    $stmt = $pdo->prepare('SELECT id FROM course_modules WHERE id = ? AND course_id = ?');
    $stmt->execute([$module_id, $course_id]);
    if (!$stmt->fetchColumn()) {
        $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'Module not found for this course.'];
        redirect('edit.php?id=' . $course_id . '#modules');
    }
    
    $stmt = $pdo->prepare("INSERT INTO course_lessons (module_id, title, video_url, resource_url, content, video_duration, order_number, is_free, is_published) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$module_id, $lesson_title, $video_url, $resource_url, $content, $duration, $order_number, $is_free, $is_published]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => $is_published ? 'Lesson published and visible to enrolled freshers.' : 'Lesson saved as a draft.'
    ];
    redirect('edit.php?id=' . $course_id . '#modules');
}

// Update existing lesson content and publication state.
if (isset($_POST['update_lesson'])) {
    $lesson_id = (int)($_POST['lesson_id'] ?? 0);
    $lesson_title = trim(sanitize($_POST['lesson_title'] ?? ''));
    $video_url = trim(sanitize($_POST['video_url'] ?? ''));
    $resource_url = trim(sanitize($_POST['resource_url'] ?? ''));
    $content = sanitize($_POST['lesson_content'] ?? '');
    $duration = max(0, (int)($_POST['lesson_duration'] ?? 0));
    $order_number = max(0, (int)($_POST['lesson_order'] ?? 0));
    $is_free = isset($_POST['is_free']) ? 1 : 0;
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    $stmt = $pdo->prepare('UPDATE course_lessons l JOIN course_modules m ON m.id = l.module_id SET l.title = ?, l.video_url = ?, l.resource_url = ?, l.content = ?, l.video_duration = ?, l.order_number = ?, l.is_free = ?, l.is_published = ? WHERE l.id = ? AND m.course_id = ?');
    $stmt->execute([$lesson_title, $video_url, $resource_url, $content, $duration, $order_number, $is_free, $is_published, $lesson_id, $course_id]);
    if ($stmt->rowCount() === 0) {
        $verifyStmt = $pdo->prepare('SELECT COUNT(*) FROM course_lessons l JOIN course_modules m ON m.id = l.module_id WHERE l.id = ? AND m.course_id = ?');
        $verifyStmt->execute([$lesson_id, $course_id]);
        if (!(int)$verifyStmt->fetchColumn()) {
            $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'Lesson not found for this course.'];
            redirect('edit.php?id=' . $course_id . '#modules');
        }
    }
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => $is_published ? 'Lesson changes saved and published to enrolled freshers.' : 'Lesson changes saved as a draft.',
    ];
    redirect('edit.php?id=' . $course_id . '#modules');
}

// Delete lesson
if (isset($_GET['delete_lesson']) && isset($_GET['lesson_id'])) {
    $lesson_id = (int)$_GET['lesson_id'];
    $stmt = $pdo->prepare("DELETE l FROM course_lessons l JOIN course_modules m ON m.id = l.module_id WHERE l.id = ? AND m.course_id = ?");
    $stmt->execute([$lesson_id, $course_id]);
    
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
                                        <div class="col-md-3 d-flex align-items-center gap-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="module_is_published" id="newModulePublished" checked>
                                                <label class="form-check-label" for="newModulePublished">Publish</label>
                                            </div>
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
                                        <span class="badge bg-<?php echo !empty($module['is_published']) ? 'success' : 'secondary'; ?> me-2"><?php echo !empty($module['is_published']) ? 'Published' : 'Draft'; ?></span>
                                        <span class="badge bg-secondary"><?php echo count($module['lessons']); ?> lessons</span>
                                        <a href="?delete_module=1&module_id=<?php echo $module['id']; ?>&id=<?php echo $course_id; ?>" 
                                           class="btn btn-sm btn-danger" onclick="return confirm('Delete this module?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" class="row g-2 align-items-center mb-3 p-2 rounded bg-light">
                                        <input type="hidden" name="module_id" value="<?php echo (int)$module['id']; ?>">
                                        <div class="col-md-6"><input type="text" name="module_title" class="form-control form-control-sm" value="<?php echo htmlspecialchars($module['title'], ENT_QUOTES, 'UTF-8'); ?>" required aria-label="Module title"></div>
                                        <div class="col-md-2"><input type="number" name="module_order" class="form-control form-control-sm" value="<?php echo (int)$module['order_number']; ?>" min="0" aria-label="Module order"></div>
                                        <div class="col-md-2"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="module_is_published" id="modulePublished<?php echo (int)$module['id']; ?>" <?php echo !empty($module['is_published']) ? 'checked' : ''; ?>><label class="form-check-label small" for="modulePublished<?php echo (int)$module['id']; ?>">Visible</label></div></div>
                                        <div class="col-md-2"><button type="submit" name="update_module" class="btn btn-sm btn-outline-primary w-100">Save Module</button></div>
                                    </form>
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
                                            <input type="text" name="resource_url" class="form-control form-control-sm" placeholder="Resource URL (optional)">
                                        </div>
                                        <div class="col-md-12">
                                            <textarea name="lesson_content" class="form-control form-control-sm" rows="2" placeholder="Lesson content..."></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_published" id="lessonPublishedNew<?php echo (int)$module['id']; ?>" checked>
                                                <label class="form-check-label small" for="lessonPublishedNew<?php echo (int)$module['id']; ?>">Publish and show to enrolled freshers</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
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
                                                <span class="badge bg-<?php echo !empty($lesson['is_published']) ? 'success' : 'secondary'; ?> ms-1"><?php echo !empty($lesson['is_published']) ? 'Published' : 'Draft'; ?></span>
                                                <?php if ($lesson['is_free']): ?>
                                                    <span class="badge bg-success">Free</span>
                                                <?php endif; ?>
                                                <small class="text-muted ms-2"><?php echo $lesson['video_duration']; ?> min</small>
                                            </div>
                                            <div>
                                                <a href="?delete_lesson=1&lesson_id=<?php echo $lesson['id']; ?>&id=<?php echo $course_id; ?>" 
                                                   class="btn btn-sm btn-danger" onclick="return confirm('Delete this lesson?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </li>
                                        <li class="list-group-item bg-light">
                                            <details>
                                                <summary class="small fw-semibold">Edit lesson content and visibility</summary>
                                                <form method="POST" class="row g-2 mt-2">
                                                    <input type="hidden" name="lesson_id" value="<?php echo (int)$lesson['id']; ?>">
                                                    <div class="col-md-8"><label class="form-label small">Lesson title</label><input type="text" name="lesson_title" class="form-control form-control-sm" value="<?php echo htmlspecialchars($lesson['title'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
                                                    <div class="col-md-4"><label class="form-label small">Order</label><input type="number" name="lesson_order" class="form-control form-control-sm" min="0" value="<?php echo (int)$lesson['order_number']; ?>"></div>
                                                    <div class="col-md-6"><label class="form-label small">Video URL</label><input type="url" name="video_url" class="form-control form-control-sm" value="<?php echo htmlspecialchars((string)($lesson['video_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></div>
                                                    <div class="col-md-6"><label class="form-label small">Resource URL</label><input type="url" name="resource_url" class="form-control form-control-sm" value="<?php echo htmlspecialchars((string)($lesson['resource_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></div>
                                                    <div class="col-md-12"><label class="form-label small">Lesson content</label><textarea name="lesson_content" class="form-control form-control-sm" rows="5"><?php echo htmlspecialchars((string)($lesson['content'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea></div>
                                                    <div class="col-md-4"><label class="form-label small">Duration (minutes)</label><input type="number" name="lesson_duration" class="form-control form-control-sm" min="0" value="<?php echo (int)($lesson['video_duration'] ?? 0); ?>"></div>
                                                    <div class="col-md-4 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_free" id="lessonFree<?php echo (int)$lesson['id']; ?>" <?php echo !empty($lesson['is_free']) ? 'checked' : ''; ?>><label class="form-check-label small" for="lessonFree<?php echo (int)$lesson['id']; ?>">Free lesson</label></div></div>
                                                    <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_published" id="lessonPublished<?php echo (int)$lesson['id']; ?>" <?php echo !empty($lesson['is_published']) ? 'checked' : ''; ?>><label class="form-check-label small" for="lessonPublished<?php echo (int)$lesson['id']; ?>">Visible to freshers</label></div></div>
                                                    <div class="col-12"><button type="submit" name="update_lesson" class="btn btn-sm btn-primary"><i class="fas fa-save me-1"></i>Save Lesson</button></div>
                                                </form>
                                            </details>
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