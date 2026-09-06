<?php
$page_title = 'My Courses';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'all';

$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id AND status = 'active') as student_count,
          (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating,
          (SELECT COUNT(*) FROM course_lessons cl
           JOIN course_modules cm ON cl.module_id = cm.id
           WHERE cm.course_id = c.id) as lesson_count
          FROM courses c 
          WHERE c.mentor_id = ?";

$params = [$user_id];

if ($filter === 'active') {
    $query .= " AND c.status = 'active'";
} elseif ($filter === 'pending') {
    $query .= " AND c.status = 'pending'";
} elseif ($filter === 'draft') {
    $query .= " AND c.status = 'draft'";
} elseif ($filter === 'archived') {
    $query .= " AND c.status = 'archived'";
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();

// Get counts
$counts = [
    'all' => count($courses),
    'active' => 0,
    'pending' => 0,
    'draft' => 0,
    'archived' => 0
];

foreach ($courses as $course) {
    if (isset($counts[$course['status']])) {
        $counts[$course['status']]++;
    }
}

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $course_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE courses SET status = 'archived' WHERE id = ? AND mentor_id = ?");
    $stmt->execute([$course_id, $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Course archived successfully.'
    ];
    redirect('index.php');
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
                <h1 class="h2">My Courses</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Course
                    </a>
                </div>
            </div>
            
            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'all' ? 'active' : ''; ?>" href="?filter=all">
                        All <span class="badge bg-secondary"><?php echo $counts['all']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'active' ? 'active' : ''; ?>" href="?filter=active">
                        Active <span class="badge bg-success"><?php echo $counts['active']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'pending' ? 'active' : ''; ?>" href="?filter=pending">
                        Pending <span class="badge bg-warning"><?php echo $counts['pending']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'draft' ? 'active' : ''; ?>" href="?filter=draft">
                        Draft <span class="badge bg-secondary"><?php echo $counts['draft']; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'archived' ? 'active' : ''; ?>" href="?filter=archived">
                        Archived <span class="badge bg-danger"><?php echo $counts['archived']; ?></span>
                    </a>
                </li>
            </ul>
            
            <!-- Courses Grid -->
            <?php if (!empty($courses)): ?>
                <div class="row g-4">
                    <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <img src="<?php echo $course['thumbnail'] ?? '../../assets/images/course-placeholder.jpg'; ?>" 
                                 class="card-img-top" alt="<?php echo $course['title']; ?>" 
                                 style="height: 160px; object-fit: cover;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5><?php echo htmlspecialchars($course['title']); ?></h5>
                                    <span class="badge bg-<?php 
                                        echo $course['status'] === 'active' ? 'success' : 
                                            ($course['status'] === 'pending' ? 'warning' : 
                                            ($course['status'] === 'draft' ? 'secondary' : 'danger')); 
                                    ?>">
                                        <?php echo ucfirst($course['status']); ?>
                                    </span>
                                </div>
                                
                                <p class="text-muted small"><?php echo substr($course['description'], 0, 80); ?>...</p>
                                
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-users"></i> <?php echo $course['student_count'] ?? 0; ?>
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-star"></i> <?php echo number_format($course['avg_rating'] ?? 0, 1); ?>
                                    </span>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-dollar-sign"></i> $<?php echo number_format($course['price'], 2); ?>
                                    </span>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-secondary"><?php echo getCourseLevel($course['level']); ?></span>
                                    <span class="badge bg-info"><?php echo $course['duration']; ?>h</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="view.php?id=<?php echo $course['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="edit.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-warning flex-grow-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <?php if ($course['status'] !== 'archived'): ?>
                                        <a href="?delete=1&id=<?php echo $course['id']; ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Archive this course?')">
                                            <i class="fas fa-archive"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                    <h5>No courses found</h5>
                    <p class="text-muted">You haven't created any courses yet.</p>
                    <a href="create.php" class="btn btn-primary">Create Your First Course</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>