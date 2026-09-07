<?php
$page_title = 'Course Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get course details
$stmt = $pdo->prepare("SELECT c.*, f.name as field_name,
                       (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as total_enrollments,
                       (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id AND status = 'active') as active_enrollments,
                       (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id AND status = 'completed') as completed_enrollments,
                       (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating,
                       (SELECT COUNT(*) FROM ratings WHERE course_id = c.id) as total_reviews,
                       (SELECT COUNT(*) FROM course_modules WHERE course_id = c.id) as total_modules,
                       (SELECT COUNT(*) FROM course_lessons cl 
                        JOIN course_modules cm ON cl.module_id = cm.id 
                        WHERE cm.course_id = c.id) as total_lessons
                       FROM courses c 
                       LEFT JOIN academic_fields f ON c.field_id = f.id 
                       WHERE c.id = ? AND c.mentor_id = ?");
$stmt->execute([$course_id, $user_id]);
$course = $stmt->fetch();

if (!$course) {
    redirect('index.php');
}

// Get modules with lessons
$stmt = $pdo->prepare("SELECT * FROM course_modules WHERE course_id = ? ORDER BY order_number");
$stmt->execute([$course_id]);
$modules = $stmt->fetchAll();

foreach ($modules as &$module) {
    $stmt = $pdo->prepare("SELECT * FROM course_lessons WHERE module_id = ? ORDER BY order_number");
    $stmt->execute([$module['id']]);
    $module['lessons'] = $stmt->fetchAll();
}

// Get recent enrollments
$stmt = $pdo->prepare("SELECT e.*, u.full_name, u.email, u.avatar 
                       FROM enrollments e 
                       JOIN users u ON e.fresher_id = u.id 
                       WHERE e.course_id = ? 
                       ORDER BY e.started_at DESC LIMIT 10");
$stmt->execute([$course_id]);
$enrollments = $stmt->fetchAll();
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
                <h1 class="h2">Course Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="edit.php?id=<?php echo $course_id; ?>" class="btn btn-warning me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Course Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                    <p class="text-muted">
                                        <i class="fas fa-book"></i> <?php echo htmlspecialchars($course['field_name'] ?? 'General'); ?>
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-tag"></i> <?php echo getCourseLevel($course['level']); ?>
                                    </p>
                                </div>
                                <span class="badge bg-<?php 
                                    echo $course['status'] === 'active' ? 'success' : 
                                        ($course['status'] === 'pending' ? 'warning' : 
                                        ($course['status'] === 'draft' ? 'secondary' : 'danger')); 
                                ?> fs-6">
                                    <?php echo ucfirst($course['status']); ?>
                                </span>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Price</small>
                                        <p class="mb-0">$<?php echo number_format($course['price'], 2); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Duration</small>
                                        <p class="mb-0"><?php echo $course['duration']; ?> hours</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Rating</small>
                                        <p class="mb-0">
                                            <?php if ($course['avg_rating']): ?>
                                                <?php echo number_format($course['avg_rating'], 1); ?> ★ 
                                                <small class="text-muted">(<?php echo $course['total_reviews']; ?> reviews)</small>
                                            <?php else: ?>
                                                No reviews yet
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($course['description']): ?>
                            <div class="mt-3">
                                <h6>Description</h6>
                                <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($course['requirements']): ?>
                            <div class="mt-3">
                                <h6>Requirements</h6>
                                <p><?php echo nl2br(htmlspecialchars($course['requirements'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($course['learning_outcomes']): ?>
                            <div class="mt-3">
                                <h6>Learning Objectives</h6>
                                <p><?php echo nl2br(htmlspecialchars($course['learning_outcomes'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Course Content -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">
                                <i class="fas fa-list"></i> Course Content 
                                <span class="badge bg-secondary"><?php echo $course['total_modules']; ?> modules, <?php echo $course['total_lessons']; ?> lessons</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($modules as $module): ?>
                            <div class="border-bottom">
                                <div class="p-3 bg-light">
                                    <h6 class="mb-0">Module <?php echo $module['order_number']; ?>: <?php echo htmlspecialchars($module['title']); ?></h6>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($module['lessons'] as $lesson): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <span class="badge bg-secondary me-2"><?php echo $lesson['order_number']; ?></span>
                                            <?php echo htmlspecialchars($lesson['title']); ?>
                                            <?php if ($lesson['is_free']): ?>
                                                <span class="badge bg-success">Free</span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="text-muted">
                                            <i class="fas fa-clock"></i> <?php echo $lesson['video_duration']; ?> min
                                            <?php if ($lesson['video_url']): ?>
                                                <i class="fas fa-video ms-2"></i>
                                            <?php endif; ?>
                                        </span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endforeach; ?>
                            <?php if (empty($modules)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No content added yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Enrollments -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">
                                <i class="fas fa-users"></i> Enrollments 
                                <span class="badge bg-secondary"><?php echo $course['total_enrollments']; ?> total</span>
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($enrollments)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th>Enrolled</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enrollments as $enrollment): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo getAvatar($enrollment); ?>" 
                                                         class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                                    <?php echo htmlspecialchars($enrollment['full_name']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                        <div class="progress-bar" style="width: <?php echo $enrollment['progress'] ?? 0; ?>%"></div>
                                                    </div>
                                                    <small><?php echo round($enrollment['progress'] ?? 0); ?>%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $enrollment['status'] === 'active' ? 'success' : 
                                                        ($enrollment['status'] === 'completed' ? 'primary' : 'secondary'); 
                                                ?>">
                                                    <?php echo ucfirst($enrollment['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($enrollment['enrolled_at']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No enrollments yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h6>Course Statistics</h6>
                            
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Total Students</span>
                                <strong><?php echo $course['total_enrollments']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Active Students</span>
                                <strong><?php echo $course['active_enrollments']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Completed</span>
                                <strong><?php echo $course['completed_enrollments']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Modules</span>
                                <strong><?php echo $course['total_modules']; ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between py-2">
                                <span class="text-muted">Lessons</span>
                                <strong><?php echo $course['total_lessons']; ?></strong>
                            </div>
                            
                            <hr>
                            
                            <div class="d-grid gap-2">
                                <a href="../session/create.php?course=<?php echo $course_id; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-calendar-plus"></i> Schedule Session
                                </a>
                                <a href="../resources/upload.php?course=<?php echo $course_id; ?>" class="btn btn-outline-info">
                                    <i class="fas fa-upload"></i> Upload Resource
                                </a>
                                 <a href="../assignment/create.php?course=<?php echo $course_id; ?>" class="btn btn-outline-warning">
                                    <i class="fas fa-tasks"></i> Create Assignment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>