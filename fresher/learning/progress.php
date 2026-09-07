<?php
$page_title = 'Learning Progress';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

// Get all courses with progress
$stmt = $pdo->prepare("SELECT e.*, c.id as course_id, c.title, c.thumbnail, c.duration,
                       u.full_name as mentor_name,
                       (SELECT COUNT(*) FROM course_lessons cl 
                        JOIN course_modules cm ON cl.module_id = cm.id 
                        WHERE cm.course_id = c.id) as total_lessons,
                       (SELECT COUNT(*) FROM lesson_progress lp 
                        JOIN course_lessons cl ON lp.lesson_id = cl.id 
                        JOIN course_modules cm ON cl.module_id = cm.id 
                        WHERE lp.fresher_id = ? AND cm.course_id = c.id AND lp.completed = 1) as completed_lessons
                       FROM enrollments e 
                       JOIN courses c ON e.course_id = c.id 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE e.fresher_id = ? AND e.status != 'dropped'
                       ORDER BY e.progress DESC");
$stmt->execute([$user_id, $user_id]);
$courses = $stmt->fetchAll();

// Calculate overall progress
$total_courses = count($courses);
$completed_courses = array_filter($courses, function($c) { return $c['status'] === 'completed'; });
$total_progress = $total_courses > 0 ? array_sum(array_column($courses, 'progress')) / $total_courses : 0;
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Learning Progress</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="my-course.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> My Courses
                    </a>
                </div>
            </div>
            
            <!-- Overall Progress -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5>Overall Progress</h5>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar" style="width: <?php echo round($total_progress); ?>%"></div>
                            </div>
                            <p class="mt-2"><?php echo round($total_progress); ?>% complete across <?php echo $total_courses; ?> courses</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3><?php echo $total_courses; ?></h3>
                                    <small class="text-muted">Total Courses</small>
                                </div>
                                <div class="col-4">
                                    <h3><?php echo count($completed_courses); ?></h3>
                                    <small class="text-muted">Completed</small>
                                </div>
                                <div class="col-4">
                                    <h3><?php echo $total_courses - count($completed_courses); ?></h3>
                                    <small class="text-muted">In Progress</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Course Progress List -->
            <div class="row g-4">
                <?php foreach ($courses as $course): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $course['thumbnail'] ?? '../../assets/images/course-placeholder.jpg'; ?>" 
                                     alt="<?php echo $course['title']; ?>" 
                                     class="rounded me-3" style="width: 80px; height: 60px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6><?php echo htmlspecialchars($course['title']); ?></h6>
                                    <small class="text-muted">by <?php echo htmlspecialchars($course['mentor_name']); ?></small>
                                </div>
                                <span class="badge bg-<?php echo $course['status'] === 'completed' ? 'success' : ($course['status'] === 'active' ? 'primary' : 'secondary'); ?>">
                                    <?php echo ucfirst($course['status']); ?>
                                </span>
                            </div>
                            
                            <div class="mt-3">
                                <div class="d-flex justify-content-between">
                                    <small>Progress</small>
                                    <small><?php echo round($course['progress'] ?? 0); ?>%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" style="width: <?php echo $course['progress'] ?? 0; ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="row text-center mt-3">
                                <div class="col-4">
                                    <small class="text-muted">Lessons</small>
                                    <p class="mb-0"><?php echo $course['completed_lessons']; ?>/<?php echo $course['total_lessons']; ?></p>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Duration</small>
                                    <p class="mb-0"><?php echo $course['duration']; ?>h</p>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Status</small>
                                    <p class="mb-0"><?php echo ucfirst($course['status']); ?></p>
                                </div>
                            </div>
                            
                            <?php if ($course['status'] !== 'completed'): ?>
                            <a href="course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-primary w-100 mt-2">
                                <i class="fas fa-play"></i> Continue Learning
                            </a>
                            <?php else: ?>
                                <a href="../certificate/view.php?id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-success w-100 mt-2">
                                <i class="fas fa-certificate"></i> View Certificate
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($courses)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5>No courses enrolled</h5>
                    <p class="text-muted">Start learning to track your progress!</p>
                    <a href="../../public/courses.php" class="btn btn-primary">Browse Courses</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>