<?php
$page_title = 'Learning Dashboard';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

// Get all enrolled courses with progress
$stmt = $pdo->prepare("SELECT e.*, c.id as course_id, c.title, c.thumbnail, c.level, c.price, c.duration,
                       c.description, c.total_lessons, u.full_name as mentor_name, u.id as mentor_id,
                       (SELECT COUNT(*) FROM course_lessons cl 
                        JOIN course_modules cm ON cl.module_id = cm.id 
                        WHERE cm.course_id = c.id AND cl.is_published = 1) as total_lessons,
                       (SELECT COUNT(*) FROM lesson_progress lp 
                        JOIN course_lessons cl ON lp.lesson_id = cl.id 
                        JOIN course_modules cm ON cl.module_id = cm.id 
                        WHERE lp.fresher_id = ? AND cm.course_id = c.id AND lp.completed = 1) as completed_lessons
                       FROM enrollments e 
                       JOIN courses c ON e.course_id = c.id 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE e.fresher_id = ? AND e.status != 'dropped'
                       ORDER BY e.last_accessed_at DESC, e.progress DESC");
$stmt->execute([$user_id, $user_id]);
$courses = $stmt->fetchAll();

// Calculate overall statistics
$total_courses = count($courses);
$completed_courses = 0;
$in_progress_courses = 0;
$total_progress = 0;

foreach ($courses as $course) {
    if ($course['status'] === 'completed') {
        $completed_courses++;
    } elseif ($course['status'] === 'active' || $course['status'] === 'paused') {
        $in_progress_courses++;
    }
    $total_progress += $course['progress'] ?? 0;
}

$avg_progress = $total_courses > 0 ? round($total_progress / $total_courses) : 0;

// Get recent learning activity
$stmt = $pdo->prepare("SELECT lp.*, cl.title as lesson_title, c.id as course_id, c.title as course_title,
                       cm.id as module_id, cm.title as module_title
                       FROM lesson_progress lp 
                       JOIN course_lessons cl ON lp.lesson_id = cl.id 
                       JOIN course_modules cm ON cl.module_id = cm.id 
                       JOIN courses c ON cm.course_id = c.id 
                       WHERE lp.fresher_id = ? 
                       ORDER BY lp.last_watched_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$recent_activity = $stmt->fetchAll();

// Get recommended courses
$stmt = $pdo->prepare("SELECT c.*, u.full_name as mentor_name, 
                       (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as student_count,
                       (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating
                       FROM courses c 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE c.status = 'active' 
                       AND c.id NOT IN (SELECT course_id FROM enrollments WHERE fresher_id = ? AND status != 'dropped')
                       ORDER BY RAND() LIMIT 4");
$stmt->execute([$user_id]);
$recommended_courses = $stmt->fetchAll();

// Get upcoming deadlines
$stmt = $pdo->prepare("SELECT a.*, c.id as course_id, c.title as course_title,
                       (SELECT id FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as submitted
                       FROM assignments a 
                       JOIN courses c ON a.course_id = c.id 
                       JOIN enrollments e ON e.course_id = c.id 
                       WHERE e.fresher_id = ? AND a.due_date > NOW() AND a.is_published = 1
                       ORDER BY a.due_date ASC LIMIT 5");
$stmt->execute([$user_id, $user_id]);
$upcoming_deadlines = $stmt->fetchAll();

// Get certificates count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM certificates WHERE fresher_id = ? AND is_valid = 1");
$stmt->execute([$user_id]);
$certificates_count = $stmt->fetchColumn();
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
                <h1 class="h2">My Learning</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="my-course.php" class="btn btn-primary">
                        <i class="fas fa-book-open"></i> View All Courses
                    </a>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-primary mb-0"><?php echo $total_courses; ?></h3>
                            <small class="text-muted">Total Courses</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-success mb-0"><?php echo $completed_courses; ?></h3>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-warning mb-0"><?php echo $avg_progress; ?>%</h3>
                            <small class="text-muted">Avg Progress</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-3">
                            <h3 class="text-info mb-0"><?php echo $certificates_count; ?></h3>
                            <small class="text-muted">Certificates</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Continue Learning -->
                    <?php if (!empty($courses)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-play-circle text-primary"></i> Continue Learning</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            $continue_courses = array_filter($courses, function($c) { 
                                return $c['status'] !== 'completed' && ($c['progress'] ?? 0) < 100;
                            });
                            $continue_courses = array_slice($continue_courses, 0, 3);
                            ?>
                            <?php foreach ($continue_courses as $course): ?>
                            <div class="continue-item mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $course['thumbnail'] ?? '../../assets/images/course-placeholder.jpg'; ?>" 
                                         alt="<?php echo $course['title']; ?>" 
                                         class="rounded me-3" 
                                         style="width: 80px; height: 60px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($course['title']); ?></h6>
                                        <small class="text-muted">by <?php echo htmlspecialchars($course['mentor_name']); ?></small>
                                        <div class="mt-1">
                                            <div class="d-flex justify-content-between">
                                                <span class="badge bg-<?php echo $course['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($course['status']); ?>
                                                </span>
                                                <small><?php echo round($course['progress'] ?? 0); ?>%</small>
                                            </div>
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar" style="width: <?php echo $course['progress'] ?? 0; ?>%; 
                                                     <?php echo ($course['progress'] ?? 0) >= 100 ? 'background: #51CF66;' : ''; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-primary ms-2">
                                        <i class="fas fa-play"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($continue_courses)): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="mb-0">All caught up! You've completed all your courses.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Recent Activity -->
                    <?php if (!empty($recent_activity)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-history text-primary"></i> Recent Activity</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_activity as $activity): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($activity['lesson_title']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($activity['course_title']); ?> 
                                                • <?php echo htmlspecialchars($activity['module_title']); ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-<?php echo $activity['completed'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $activity['completed'] ? 'Completed' : 'In Progress'; ?>
                                            </span>
                                            <small class="text-muted d-block"><?php echo getTimeAgo($activity['last_watched_at']); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Upcoming Deadlines -->
                    <?php if (!empty($upcoming_deadlines)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-clock text-warning"></i> Upcoming Deadlines</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <?php foreach ($upcoming_deadlines as $deadline): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($deadline['title']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($deadline['course_title']); ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <?php if ($deadline['submitted']): ?>
                                                <span class="badge bg-success">Submitted</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">
                                                    <?php 
                                                    $days_left = ceil((strtotime($deadline['due_date']) - time()) / 86400);
                                                    echo $days_left . ' day' . ($days_left > 1 ? 's' : '');
                                                    ?>
                                                </span>
                                            <?php endif; ?>
                                            <small class="text-muted d-block">Due: <?php echo formatDate($deadline['due_date']); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Progress Overview -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="mb-3">Learning Progress</h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <small>Overall Progress</small>
                                    <small><?php echo $avg_progress; ?>%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: <?php echo $avg_progress; ?>%"></div>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-4">
                                    <span class="badge bg-primary d-block py-2 mb-1"><?php echo $total_courses; ?></span>
                                    <small class="text-muted">Total</small>
                                </div>
                                <div class="col-4">
                                    <span class="badge bg-success d-block py-2 mb-1"><?php echo $completed_courses; ?></span>
                                    <small class="text-muted">Completed</small>
                                </div>
                                <div class="col-4">
                                    <span class="badge bg-warning d-block py-2 mb-1"><?php echo $in_progress_courses; ?></span>
                                    <small class="text-muted">In Progress</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recommended Courses -->
                    <?php if (!empty($recommended_courses)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0"><i class="fas fa-star text-warning"></i> Recommended For You</h6>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($recommended_courses as $course): ?>
                            <div class="p-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $course['thumbnail'] ?? '../../assets/images/course-placeholder.jpg'; ?>" 
                                         alt="<?php echo $course['title']; ?>" 
                                         class="rounded me-2" 
                                         style="width: 50px; height: 40px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small"><?php echo htmlspecialchars($course['title']); ?></h6>
                                        <small class="text-muted">by <?php echo htmlspecialchars($course['mentor_name']); ?></small>
                                        <div>
                                            <?php if ($course['avg_rating']): ?>
                                                <small class="text-warning">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?php echo $i <= round($course['avg_rating']) ? '' : 'text-muted'; ?>" style="font-size: 10px;"></i>
                                                    <?php endfor; ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="../../public/course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>