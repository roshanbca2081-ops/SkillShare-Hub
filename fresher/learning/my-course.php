<?php
$page_title = 'My Courses';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

// Get all enrolled courses
$stmt = $pdo->prepare("SELECT e.*, c.id as course_id, c.title, c.thumbnail, c.level, c.price, c.duration,
                       u.full_name as mentor_name, u.id as mentor_id, u.avatar as mentor_avatar,
                       (SELECT COUNT(*) FROM course_lessons cl 
                        JOIN course_modules cm ON cl.module_id = cm.id 
                        WHERE cm.course_id = c.id) as total_lessons
                       FROM enrollments e 
                       JOIN courses c ON e.course_id = c.id 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE e.fresher_id = ? AND e.status != 'dropped'
                       ORDER BY e.last_accessed_at DESC");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

// Handle course drop
if (isset($_GET['drop']) && isset($_GET['id'])) {
    $course_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE enrollments SET status = 'dropped' WHERE fresher_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Course dropped successfully.'
    ];
    redirect('my-courses.php');
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
                <h1 class="h2">My Courses</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../../public/courses.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Browse New Courses
                    </a>
                </div>
            </div>
            
            <?php if (!empty($courses)): ?>
                <div class="row g-4">
                    <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo $course['thumbnail'] ?? '../../assets/images/course-placeholder.jpg'; ?>" 
                                 class="card-img-top" alt="<?php echo $course['title']; ?>" 
                                 style="height: 180px; object-fit: cover;">
                            <div class="card-body">
                                <h5><?php echo htmlspecialchars($course['title']); ?></h5>
                                <p class="text-muted small">by <?php echo htmlspecialchars($course['mentor_name']); ?></p>
                                
                                <div class="d-flex gap-2 mb-2">
                                    <span class="badge bg-<?php echo $course['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($course['status']); ?>
                                    </span>
                                    <span class="badge bg-secondary"><?php echo getCourseLevel($course['level']); ?></span>
                                    <?php if ($course['status'] === 'completed'): ?>
                                        <span class="badge bg-info"><i class="fas fa-certificate"></i> Certified</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <small>Progress</small>
                                        <small><?php echo $course['progress'] ?? 0; ?>%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" style="width: <?php echo $course['progress'] ?? 0; ?>%; 
                                             <?php echo ($course['progress'] ?? 0) >= 100 ? 'background: #51CF66;' : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between text-muted small">
                                    <span><i class="fas fa-book"></i> <?php echo $course['completed_lessons']; ?>/<?php echo $course['total_lessons']; ?> lessons</span>
                                    <span><i class="fas fa-clock"></i> <?php echo $course['duration']; ?>h</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <?php if ($course['status'] === 'active' || $course['status'] === 'paused'): ?>
                                    <a href="course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary w-100">
                                        <i class="fas fa-play"></i> Continue Learning
                                    </a>
                                <?php elseif ($course['status'] === 'completed'): ?>
                                    <a href="../../public/course-detail.php?id=<?php echo $course['course_id']; ?>" class="btn btn-outline-success w-100">
                                        <i class="fas fa-certificate"></i> View Certificate
                                    </a>
                                <?php else: ?>
                                    <a href="course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-eye"></i> View Course
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                    <h4>No Courses Yet</h4>
                    <p class="text-muted">You haven't enrolled in any courses. Start your learning journey today!</p>
                    <a href="../../public/courses.php" class="btn btn-primary">Browse Courses</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>