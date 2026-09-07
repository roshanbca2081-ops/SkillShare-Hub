<?php
$page_title = 'Course Learning';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check enrollment
$user_id = getUserId();
$stmt = $pdo->prepare("SELECT * FROM enrollments WHERE fresher_id = ? AND course_id = ? AND status != 'dropped'");
$stmt->execute([$user_id, $course_id]);
$enrollment = $stmt->fetch();

if (!$enrollment) {
    redirect('my-course.php');
}

// Get course details
$stmt = $pdo->prepare("SELECT c.*, u.full_name as mentor_name, u.avatar 
                       FROM courses c 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE c.id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    redirect('my-course.php');
}

// Get modules with lessons
$stmt = $pdo->prepare("SELECT * FROM course_modules WHERE course_id = ? AND is_published = 1 ORDER BY order_number");
$stmt->execute([$course_id]);
$modules = $stmt->fetchAll();

$lessons = [];
foreach ($modules as $module) {
    $stmt = $pdo->prepare("SELECT * FROM course_lessons WHERE module_id = ? AND is_published = 1 ORDER BY order_number");
    $stmt->execute([$module['id']]);
    $module_lessons = $stmt->fetchAll();
    
    // Get progress for each lesson
    foreach ($module_lessons as &$lesson) {
        $stmt = $pdo->prepare("SELECT completed FROM lesson_progress WHERE fresher_id = ? AND lesson_id = ?");
        $stmt->execute([$user_id, $lesson['id']]);
        $progress = $stmt->fetch();
        $lesson['completed'] = $progress ? $progress['completed'] : false;
    }
    $lessons[$module['id']] = $module_lessons;
}

// Get first lesson
$first_lesson = null;
foreach ($modules as $module) {
    if (!empty($lessons[$module['id']])) {
        $first_lesson = $lessons[$module['id']][0];
        break;
    }
}

// Get current lesson
$lesson_id = isset($_GET['lesson']) ? (int)$_GET['lesson'] : ($first_lesson ? $first_lesson['id'] : 0);
$current_lesson = null;
if ($lesson_id) {
    $stmt = $pdo->prepare("SELECT * FROM course_lessons WHERE id = ? AND is_published = 1");
    $stmt->execute([$lesson_id]);
    $current_lesson = $stmt->fetch();
}

// Mark lesson as complete
if (isset($_POST['complete_lesson']) && $current_lesson) {
    $stmt = $pdo->prepare("INSERT INTO lesson_progress (fresher_id, lesson_id, completed, completed_at) 
                           VALUES (?, ?, 1, NOW()) 
                           ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()");
    $stmt->execute([$user_id, $current_lesson['id']]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Lesson marked as complete!'
    ];
    redirect('course.php?id=' . $course_id . '&lesson=' . $lesson_id);
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
                <h1 class="h2"><?php echo htmlspecialchars($course['title']); ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="my-course.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Courses
                    </a>
                </div>
            </div>
            
            <div class="row">
                <!-- Sidebar - Course Content -->
                <div class="col-lg-3 order-lg-1 order-2">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-3">
                            <h6 class="mb-3">Course Content</h6>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar" style="width: <?php echo $enrollment['progress'] ?? 0; ?>%"></div>
                            </div>
                            <small class="text-muted d-block mb-3"><?php echo $enrollment['progress'] ?? 0; ?>% complete</small>
                            
                            <?php foreach ($modules as $module): ?>
                            <div class="module-group mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="small"><?php echo htmlspecialchars($module['title']); ?></strong>
                                    <span class="badge bg-secondary"><?php echo count($lessons[$module['id']] ?? []); ?></span>
                                </div>
                                <?php if (!empty($lessons[$module['id']])): ?>
                                    <?php foreach ($lessons[$module['id']] as $lesson): ?>
                                    <a href="course.php?id=<?php echo $course_id; ?>&lesson=<?php echo $lesson['id']; ?>" 
                                       class="d-flex align-items-center text-decoration-none p-1 rounded <?php echo ($current_lesson && $current_lesson['id'] == $lesson['id']) ? 'bg-primary bg-opacity-10' : ''; ?>">
                                        <span class="me-2">
                                            <?php if ($lesson['completed']): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-circle text-muted" style="font-size: 8px;"></i>
                                            <?php endif; ?>
                                        </span>
                                        <span class="small text-truncate <?php echo ($current_lesson && $current_lesson['id'] == $lesson['id']) ? 'text-primary' : 'text-muted'; ?>">
                                            <?php echo htmlspecialchars($lesson['title']); ?>
                                        </span>
                                    </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="col-lg-9 order-lg-2 order-1">
                    <?php if ($current_lesson): ?>
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h3><?php echo htmlspecialchars($current_lesson['title']); ?></h3>
                                
                                <?php if ($current_lesson['video_url']): ?>
                                <div class="ratio ratio-16x9 mb-4">
                                    <video controls>
                                        <source src="<?php echo $current_lesson['video_url']; ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <?php endif; ?>
                                
                                <div class="lesson-content">
                                    <?php echo nl2br(htmlspecialchars($current_lesson['content'] ?? 'No content available.')); ?>
                                </div>
                                
                                <?php if ($current_lesson['resource_url']): ?>
                                <div class="mt-3">
                                    <a href="<?php echo $current_lesson['resource_url']; ?>" class="btn btn-outline-primary" download>
                                        <i class="fas fa-download"></i> Download Resource
                                    </a>
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <div>
                                        <?php if ($current_lesson['completed']): ?>
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Completed</span>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <button type="submit" name="complete_lesson" class="btn btn-success">
                                                    <i class="fas fa-check"></i> Mark as Complete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <?php if ($current_lesson['completed']): ?>
                                            <button class="btn btn-outline-secondary" onclick="markIncomplete(<?php echo $current_lesson['id']; ?>)">
                                                <i class="fas fa-undo"></i> Mark Incomplete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation between lessons -->
                        <div class="d-flex justify-content-between">
                            <?php
                            // Find previous and next lessons
                            $all_lessons = [];
                            foreach ($modules as $module) {
                                $all_lessons = array_merge($all_lessons, $lessons[$module['id']] ?? []);
                            }
                            $current_index = array_search($current_lesson['id'], array_column($all_lessons, 'id'));
                            $prev_lesson = $current_index > 0 ? $all_lessons[$current_index - 1] : null;
                            $next_lesson = $current_index < count($all_lessons) - 1 ? $all_lessons[$current_index + 1] : null;
                            ?>
                            <?php if ($prev_lesson): ?>
                                <a href="course.php?id=<?php echo $course_id; ?>&lesson=<?php echo $prev_lesson['id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left"></i> Previous: <?php echo htmlspecialchars($prev_lesson['title']); ?>
                                </a>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>
                            
                            <?php if ($next_lesson): ?>
                                <a href="course.php?id=<?php echo $course_id; ?>&lesson=<?php echo $next_lesson['id']; ?>" class="btn btn-primary">
                                    Next: <?php echo htmlspecialchars($next_lesson['title']); ?> <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php else: ?>
                                <a href="my-course.php" class="btn btn-success">
                                    <i class="fas fa-check"></i> Course Complete!
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                <h4>No lessons available</h4>
                                <p class="text-muted">This course doesn't have any lessons yet.</p>
                                <a href="my-course.php" class="btn btn-primary">Back to Courses</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markIncomplete(lessonId) {
    if (confirm('Mark this lesson as incomplete?')) {
        window.location.href = '#';
    }
}
</script>

<?php include '../../includes/footer.php'; ?>