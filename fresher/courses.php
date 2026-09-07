<?php
$page_title = 'Browse Courses';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';
require_once '../config/auth.php';

requireFresher();

$user_id = getUserId();
$field_id = isset($_GET['field']) ? (int)$_GET['field'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;
$level = isset($_GET['level']) ? sanitize($_GET['level']) : null;

$query = "SELECT c.*, u.full_name as mentor_name, u.avatar, u.title as mentor_title,
                 f.name as field_name, f.icon as field_icon,
                 (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id AND status != 'dropped') as enrollment_count,
                 (SELECT AVG(rating) FROM ratings WHERE mentor_id = c.mentor_id) as avg_rating
          FROM courses c 
          JOIN users u ON c.mentor_id = u.id 
          LEFT JOIN academic_fields f ON c.field_id = f.id 
          WHERE c.status = 'active'";

$params = [];

if ($field_id) {
    $query .= " AND c.field_id = ?";
    $params[] = $field_id;
}

if ($level) {
    $query .= " AND c.level = ?";
    $params[] = $level;
}

if ($search) {
    $query .= " AND (c.title LIKE ? OR c.description LIKE ? OR u.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY c.featured DESC, c.total_students DESC, c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();

// Get fields for filter
$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name")->fetchAll();

// Get user's enrolled courses
$stmt = $pdo->prepare("SELECT course_id FROM enrollments WHERE fresher_id = ? AND status != 'dropped'");
$stmt->execute([$user_id]);
$enrolled_courses = $stmt->fetchAll(PDO::FETCH_COLUMN);
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
                <h1 class="h2">Browse Courses</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search courses..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Field</label>
                            <select name="field" class="form-select" onchange="this.form.submit()">
                                <option value="">All Fields</option>
                                <?php foreach ($fields as $field): ?>
                                <option value="<?php echo $field['id']; ?>" <?php echo $field_id == $field['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($field['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Level</label>
                            <select name="level" class="form-select" onchange="this.form.submit()">
                                <option value="">All Levels</option>
                                <option value="beginner" <?php echo $level == 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                <option value="intermediate" <?php echo $level == 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                <option value="advanced" <?php echo $level == 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <?php if ($field_id || $level || $search): ?>
                                <a href="courses.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Results -->
            <?php if (!empty($courses)): ?>
                <div class="row g-4">
                    <?php foreach ($courses as $course): 
                        $is_enrolled = in_array($course['id'], $enrolled_courses);
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <img src="<?php echo $course['thumbnail'] ?? '../assets/images/course-placeholder.jpg'; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($course['title']); ?>" 
                                 style="height: 180px; object-fit: cover;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($course['field_name'] ?? 'General'); ?></span>
                                    <span class="badge bg-<?php echo $course['level'] == 'beginner' ? 'success' : ($course['level'] == 'intermediate' ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst($course['level']); ?>
                                    </span>
                                </div>
                                
                                <h6 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h6>
                                <p class="text-muted small"><?php echo substr(strip_tags($course['description']), 0, 100); ?>...</p>
                                
                                <div class="d-flex align-items-center mb-2">
                                    <img src="<?php echo getAvatar($course); ?>" class="rounded-circle me-2" style="width: 24px; height: 24px;">
                                    <small class="text-muted"><?php echo htmlspecialchars($course['mentor_name']); ?></small>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <span class="fw-bold text-primary">$<?php echo number_format($course['price'], 2); ?></span>
                                        <?php if ($course['avg_rating']): ?>
                                            <div class="text-warning small">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= round($course['avg_rating']) ? '' : 'text-muted'; ?>"></i>
                                                <?php endfor; ?>
                                                <span class="text-muted">(<?php echo number_format($course['avg_rating'], 1); ?>)</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-muted small">
                                        <i class="fas fa-users"></i> <?php echo $course['enrollment_count'] ?? 0; ?>
                                    </span>
                                </div>
                                
                                <div class="mt-3">
                                    <?php if ($is_enrolled): ?>
                                        <a href="learning/my-course.php" class="btn btn-success w-100">
                                            <i class="fas fa-play"></i> Continue Learning
                                        </a>
                                    <?php else: ?>
                                        <a href="../public/course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-primary w-100">
                                            <i class="fas fa-eye"></i> View Course
                                        </a>
                                    <?php endif; ?>
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
                    <p class="text-muted"><?php echo $search || $field_id || $level ? 'Try adjusting your filters.' : 'Check back later for new courses.'; ?></p>
                    <?php if ($search || $field_id || $level): ?>
                        <a href="courses.php" class="btn btn-primary">Clear Filters</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
