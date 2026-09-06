<?php
$page_title = 'Courses';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$field_id = isset($_GET['field']) ? (int)$_GET['field'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;

$query = "SELECT c.*, u.full_name as mentor_name, u.avatar, f.name as field_name 
          FROM courses c 
          JOIN users u ON c.mentor_id = u.id 
          LEFT JOIN academic_fields f ON c.field_id = f.id 
          WHERE c.status = 'active'";

$params = [];

if ($field_id) {
    $query .= " AND c.field_id = ?";
    $params[] = $field_id;
}

if ($search) {
    $query .= " AND (c.title LIKE ? OR c.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();

// Get fields for filter
$fields = $pdo->query("SELECT * FROM academic_fields ORDER BY name")->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto text-center mb-5">
            <h1 class="display-4 fw-bold">Browse Courses</h1>
            <p class="text-muted">Find the perfect course to accelerate your career</p>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search courses..." value="<?php echo $search ?? ''; ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="col-md-6">
            <form method="GET" class="d-flex gap-2">
                <select name="field" class="form-select" onchange="this.form.submit()">
                    <option value="">All Fields</option>
                    <?php foreach ($fields as $field): ?>
                    <option value="<?php echo $field['id']; ?>" <?php echo $field_id == $field['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($field['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($field_id || $search): ?>
                    <a href="courses.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="row g-4">
        <?php foreach ($courses as $course): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <img src="<?php echo $course['thumbnail'] ?? '../assets/images/course-placeholder.jpg'; ?>" class="card-img-top" alt="<?php echo $course['title']; ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary"><?php echo getCourseLevel($course['level']); ?></span>
                        <span class="badge bg-info"><?php echo htmlspecialchars($course['field_name'] ?? 'General'); ?></span>
                    </div>
                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p class="card-text text-muted small"><?php echo substr($course['description'], 0, 100) . '...'; ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo getAvatar($course); ?>" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                            <small class="text-muted"><?php echo htmlspecialchars($course['mentor_name']); ?></small>
                        </div>
                        <span class="fw-bold">$<?php echo number_format($course['price'], 2); ?></span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-primary w-100">View Course</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($courses)): ?>
        <div class="col-12 text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5>No courses found</h5>
            <p class="text-muted">Try adjusting your search or filters.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>