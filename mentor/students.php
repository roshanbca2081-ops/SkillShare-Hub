<?php
$page_title = 'My Students';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';
require_once '../config/auth.php';

requireMentor();

$user_id = getUserId();
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;

$query = "SELECT DISTINCT u.id, u.full_name, u.email, u.avatar, u.created_at as joined_date,
          e.course_id, e.progress, e.status as enrollment_status, e.started_at as enrolled_at,
          c.title as course_title,
          (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ?) as messages_sent,
          (SELECT COUNT(*) FROM messages WHERE sender_id = ? AND receiver_id = u.id) as messages_received,
          (SELECT COUNT(*) FROM assignments a 
           JOIN assignment_submissions s ON a.id = s.assignment_id 
           WHERE s.fresher_id = u.id AND a.mentor_id = ?) as submissions_count
          FROM enrollments e 
          JOIN users u ON e.fresher_id = u.id 
          JOIN courses c ON e.course_id = c.id 
          WHERE c.mentor_id = ?";

$params = [$user_id, $user_id, $user_id, $user_id];

if ($search) {
    $query .= " AND (u.full_name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($course_id) {
    $query .= " AND e.course_id = ?";
    $params[] = $course_id;
}

$query .= " ORDER BY e.started_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();

// Get courses for filter
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE mentor_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../includes/mentor-sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Students</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-primary"><?php echo count($students); ?> Students</span>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Search students..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="course" class="form-select">
                                <option value="">All Courses</option>
                                <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" <?php echo $course_id == $course['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Students List -->
            <?php if (!empty($students)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Enrolled</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo getAvatar($student); ?>" 
                                                     class="rounded-circle me-2" style="width: 36px; height: 36px;">
                                                <div>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($student['email']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['course_title']); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                    <div class="progress-bar" style="width: <?php echo $student['progress'] ?? 0; ?>%"></div>
                                                </div>
                                                <small><?php echo round($student['progress'] ?? 0); ?>%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $student['enrollment_status'] === 'active' ? 'success' : 
                                                    ($student['enrollment_status'] === 'completed' ? 'primary' : 'secondary'); 
                                            ?>">
                                                <?php echo ucfirst($student['enrollment_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($student['enrolled_at']); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="message/index.php?user_id=<?php echo $student['id']; ?>" class="btn btn-outline-primary" title="Message">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                                <a href="../fresher/learning/progress.php?student=<?php echo $student['id']; ?>&course=<?php echo $student['course_id']; ?>" class="btn btn-outline-info" title="Progress">
                                                    <i class="fas fa-chart-line"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                    <h5>No students found</h5>
                    <p class="text-muted">You don't have any students enrolled in your courses yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>