<?php
$page_title = 'Courses by Field';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$field_id = isset($_GET['field']) ? (int)$_GET['field'] : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Get field info
$stmt = $pdo->prepare("SELECT * FROM academic_fields WHERE id = ?");
$stmt->execute([$field_id]);
$field = $stmt->fetch();

$query = "SELECT c.*, u.full_name as mentor_name, u.avatar 
          FROM courses c 
          JOIN users u ON c.mentor_id = u.id 
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

$query .= " ORDER BY c.featured DESC, c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();

// Get all fields for filter
$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name")->fetchAll();
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
                <h1 class="h2">
                    <?php if ($field): ?>
                        <?php echo htmlspecialchars($field['name']); ?> Courses
                    <?php else: ?>
                        All Courses
                    <?php endif; ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="fields.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> All Fields
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="hidden" name="field" value="<?php echo $field_id; ?>">
                        <input type="text" name="search" class="form-control" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        <?php if ($search): ?>
                            <a href="courses.php?field=<?php echo $field_id; ?>" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <select name="field" class="form-select" onchange="this.form.submit()">
                            <option value="">All Fields</option>
                            <?php foreach ($fields as $f): ?>
                            <option value="<?php echo $f['id']; ?>" <?php echo $field_id == $f['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($f['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($search): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Courses Grid -->
            <div class="row g-4">
                <?php foreach ($courses as $course): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="course-card">
                        <div class="course-card-image">
                            <img src="<?php echo $course['thumbnail'] ?? '../../assets/images/course-placeholder.jpg'; ?>" 
                                 alt="<?php echo $course['title']; ?>">
                            <?php if ($course['featured']): ?>
                                <span class="badge bg-warning position-absolute top-0 start-0 m-2">Featured</span>
                            <?php endif; ?>
                            <?php if ($course['price'] == 0): ?>
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">Free</span>
                            <?php endif; ?>
                        </div>
                        <div class="course-card-body">
                            <h6 class="course-card-title"><?php echo htmlspecialchars($course['title']); ?></h6>
                            <div class="course-card-meta">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($course['mentor_name']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo $course['duration']; ?>h</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-secondary"><?php echo getCourseLevel($course['level']); ?></span>
                                <span class="badge bg-info"><?php echo $course['total_students'] ?? 0; ?> students</span>
                            </div>
                        </div>
                        <div class="course-card-footer">
                            <span class="course-card-price">
                                <?php if ($course['discount_price']): ?>
                                    <span class="original">$<?php echo number_format($course['price'], 2); ?></span>
                                    $<?php echo number_format($course['discount_price'], 2); ?>
                                <?php else: ?>
                                    $<?php echo number_format($course['price'], 2); ?>
                                <?php endif; ?>
                            </span>
                            <a href="../../public/course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">
                                View Course
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($courses)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5>No courses found</h5>
                    <p class="text-muted">Try adjusting your search or filters.</p>
                    <a href="courses.php" class="btn btn-primary">Clear Filters</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>