<?php
$page_title = 'Course Detail';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';
require_once '../config/auth.php';

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!isLoggedIn()) {
    $course_url = 'public/course-detail.php?id=' . $course_id;
    $login_url = '../login.php?redirect=' . rawurlencode($course_url);
    $register_url = '../register.php?redirect=' . rawurlencode($course_url);
    ?>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/navbar.php'; ?>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card border-0 shadow-sm text-center p-4 p-md-5">
                    <div class="mb-3 text-primary"><i class="fas fa-lock fa-3x"></i></div>
                    <h1 class="h3 fw-bold mb-3">Sign in to view this course</h1>
                    <p class="text-muted mb-4">Create an account or log in to view course details, lessons, and enrollment options.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="<?php echo htmlspecialchars($login_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="<?php echo htmlspecialchars($register_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary"><i class="fas fa-user-plus"></i> Register</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
    <?php
    exit;
}

$stmt = $pdo->prepare("SELECT c.*, u.full_name as mentor_name, u.avatar, u.bio as mentor_bio, f.name as field_name 
                       FROM courses c 
                       JOIN users u ON c.mentor_id = u.id 
                       LEFT JOIN academic_fields f ON c.field_id = f.id 
                       WHERE c.id = ? AND c.status = 'active'");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    redirect('../404.php');
}

// Get modules
$stmt = $pdo->prepare("SELECT * FROM course_modules WHERE course_id = ? ORDER BY order_number");
$stmt->execute([$course_id]);
$modules = $stmt->fetchAll();

// Get enrollments
$is_enrolled = false;
$enrollment = null;
if (isLoggedIn() && isFresher()) {
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE course_id = ? AND fresher_id = ?");
    $stmt->execute([$course_id, getUserId()]);
    $enrollment = $stmt->fetch();
    $is_enrolled = $enrollment && $enrollment['status'] !== 'dropped';
}

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    requireLogin();
    requireFresher();
    
    if (!$is_enrolled) {
        $stmt = $pdo->prepare("INSERT INTO enrollments (fresher_id, course_id, status) VALUES (?, ?, 'active')");
        $stmt->execute([getUserId(), $course_id]);
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Successfully enrolled in the course!'
        ];
        redirect('course-detail.php?id=' . $course_id);
    }
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/alerts.php'; ?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($course['title']); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <?php if ($course['thumbnail']): ?>
                <img src="<?php echo $course['thumbnail']; ?>" class="card-img-top" alt="<?php echo $course['title']; ?>" style="max-height: 400px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-primary"><?php echo getCourseLevel($course['level']); ?></span>
                        <span class="badge bg-info"><?php echo htmlspecialchars($course['field_name'] ?? 'General'); ?></span>
                        <?php if ($course['price'] == 0): ?>
                            <span class="badge bg-success">Free</span>
                        <?php endif; ?>
                    </div>
                    <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($course['title']); ?></h1>
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo getAvatar($course); ?>" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <small class="text-muted">By</small>
                            <strong><?php echo htmlspecialchars($course['mentor_name']); ?></strong>
                        </div>
                    </div>
                    <p class="lead"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                    <div class="d-flex gap-4 mt-3">
                        <div><i class="fas fa-clock text-muted"></i> <?php echo $course['duration'] ?? 'N/A'; ?> hours</div>
                        <div><i class="fas fa-users text-muted"></i> <?php echo rand(50, 500); ?>+ students</div>
                    </div>
                </div>
            </div>
            
            <!-- Course Content -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4>Course Content</h4>
                    <?php foreach ($modules as $module): ?>
                    <div class="module mb-3">
                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                            <h6 class="mb-0"><?php echo htmlspecialchars($module['title']); ?></h6>
                            <span class="badge bg-secondary"><?php echo rand(3, 8); ?> lessons</span>
                        </div>
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM course_lessons WHERE module_id = ? ORDER BY order_number");
                        $stmt->execute([$module['id']]);
                        $lessons = $stmt->fetchAll();
                        ?>
                        <ul class="list-group list-group-flush mt-2">
                            <?php foreach ($lessons as $lesson): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-<?php echo $lesson['video_url'] ? 'play-circle' : 'file-alt'; ?> text-primary me-2"></i>
                                    <?php echo htmlspecialchars($lesson['title']); ?>
                                    <?php if ($lesson['is_free']): ?>
                                        <span class="badge bg-success ms-2">Free</span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted"><?php echo $lesson['duration'] ?? 'N/A'; ?> min</small>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($modules)): ?>
                    <p class="text-muted">No content available for this course yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Mentor Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4>About the Mentor</h4>
                    <div class="d-flex align-items-start">
                        <img src="<?php echo getAvatar($course); ?>" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                        <div>
                            <h5><?php echo htmlspecialchars($course['mentor_name']); ?></h5>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($course['mentor_bio'] ?? '')); ?></p>
                            <a href="mentor-profile.php?id=<?php echo $course['mentor_id']; ?>" class="btn btn-outline-primary btn-sm">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-body p-4">
                    <h3 class="text-center mb-3">$<?php echo number_format($course['price'], 2); ?></h3>
                    
                    <?php if (isLoggedIn() && isFresher()): ?>
                        <?php if ($is_enrolled): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> You are enrolled in this course
                                <?php if ($enrollment && $enrollment['progress'] > 0): ?>
                                    <br><small>Progress: <?php echo $enrollment['progress']; ?>%</small>
                                <?php endif; ?>
                            </div>
                            <?php if ($enrollment && $enrollment['status'] === 'active'): ?>
                                <a href="../fresher/learning/course.php?id=<?php echo $course_id; ?>" class="btn btn-success w-100">
                                    <i class="fas fa-play"></i> Continue Learning
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <form method="POST">
                                <button type="submit" name="enroll" class="btn btn-primary w-100">
                                    <i class="fas fa-shopping-cart"></i> Enroll Now
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php elseif (isLoggedIn() && isMentor()): ?>
                        <a href="../mentor/my-courses/edit.php?id=<?php echo $course_id; ?>" class="btn btn-warning w-100">
                            <i class="fas fa-edit"></i> Edit Course
                        </a>
                    <?php elseif (isLoggedIn() && isAdmin()): ?>
                        <a href="../admin/courses/edit.php?id=<?php echo $course_id; ?>" class="btn btn-warning w-100">
                            <i class="fas fa-edit"></i> Manage Course
                        </a>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Please <a href="../login.php" class="alert-link">login</a> to enroll in this course.
                        </div>
                        <a href="../register.php" class="btn btn-primary w-100">Create Account</a>
                    <?php endif; ?>
                    
                    <hr>
                    <div class="small text-muted">
                        <p><i class="fas fa-check-circle text-success"></i> Full lifetime access</p>
                        <p><i class="fas fa-check-circle text-success"></i> Certificate on completion</p>
                        <p><i class="fas fa-check-circle text-success"></i> Downloadable resources</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>