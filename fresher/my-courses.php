<?php
$page_title = 'My Courses';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';
require_once '../config/auth.php';

requireFresher();

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT e.*, c.id as course_id, c.title, c.thumbnail, c.level, c.price, 
                       u.full_name as mentor_name, u.id as mentor_id
                       FROM enrollments e 
                       JOIN courses c ON e.course_id = c.id 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE e.fresher_id = ? AND e.status != 'dropped'
                       ORDER BY e.enrolled_at DESC");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Courses</h1>
                <a href="../public/courses.php" class="btn btn-primary"><i class="fas fa-plus"></i> Browse New Courses</a>
            </div>
            
            <?php if (!empty($courses)): ?>
                <div class="row g-4">
                    <?php foreach ($courses as $course): ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo $course['thumbnail'] ?? '../assets/images/course-placeholder.jpg'; ?>" class="card-img-top" alt="<?php echo $course['title']; ?>" style="height: 180px; object-fit: cover;">
                            <div class="card-body">
                                <h5><?php echo htmlspecialchars($course['title']); ?></h5>
                                <p class="text-muted small">by <?php echo htmlspecialchars($course['mentor_name']); ?></p>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-<?php echo $course['status'] === 'active' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($course['status']); ?>
                                    </span>
                                    <span class="badge bg-secondary"><?php echo getCourseLevel($course['level']); ?></span>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <small>Progress</small>
                                        <small><?php echo $course['progress'] ?? 0; ?>%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar" style="width: <?php echo $course['progress'] ?? 0; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="learning/course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-play"></i> Continue Learning
                                </a>
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
                    <a href="../public/courses.php" class="btn btn-primary">Browse Courses</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>