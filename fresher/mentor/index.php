<?php
$page_title = 'Mentors';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$field_id = isset($_GET['field']) ? (int)$_GET['field'] : 0;

$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM courses WHERE mentor_id = u.id AND status = 'active') as course_count,
          (SELECT AVG(rating) FROM ratings WHERE mentor_id = u.id) as avg_rating,
          (SELECT COUNT(*) FROM ratings WHERE mentor_id = u.id) as review_count
          FROM users u 
          WHERE u.role = 'mentor' AND u.is_active = 1";

$params = [];

if ($search) {
    $query .= " AND (u.full_name LIKE ? OR u.bio LIKE ? OR u.skills LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($field_id) {
    $query .= " AND EXISTS (SELECT 1 FROM courses c WHERE c.mentor_id = u.id AND c.field_id = ? AND c.status = 'active')";
    $params[] = $field_id;
}

$query .= " ORDER BY avg_rating DESC, course_count DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$mentors = $stmt->fetchAll();

// Get fields for filter
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
                <h1 class="h2">Mentors</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <!-- Search & Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Search mentors by name, expertise..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        <?php if ($search || $field_id): ?>
                            <a href="index.php" class="btn btn-secondary">Clear</a>
                        <?php endif; ?>
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
                        <?php if ($search): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Mentors Grid -->
            <div class="row g-4">
                <?php foreach ($mentors as $mentor): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="mentor-card">
                        <img src="<?php echo getAvatar($mentor); ?>" class="mentor-card-avatar" alt="<?php echo htmlspecialchars($mentor['full_name']); ?>">
                        <h5 class="mentor-card-name"><?php echo htmlspecialchars($mentor['full_name']); ?></h5>
                        <p class="mentor-card-title"><?php echo htmlspecialchars($mentor['title'] ?? 'Mentor'); ?></p>
                        
                        <?php if ($mentor['avg_rating']): ?>
                        <div class="text-warning mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= round($mentor['avg_rating']) ? '' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                            <small class="text-muted">(<?php echo $mentor['review_count'] ?? 0; ?> reviews)</small>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mentor-card-stats">
                            <div class="mentor-card-stat">
                                <span class="mentor-card-stat-value"><?php echo $mentor['course_count'] ?? 0; ?></span>
                                <span class="mentor-card-stat-label">Courses</span>
                            </div>
                            <div class="mentor-card-stat">
                                <span class="mentor-card-stat-value"><?php echo $mentor['review_count'] ?? 0; ?></span>
                                <span class="mentor-card-stat-label">Reviews</span>
                            </div>
                        </div>
                        
                        <a href="details.php?id=<?php echo $mentor['id']; ?>" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user"></i> View Profile
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($mentors)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                    <h5>No mentors found</h5>
                    <p class="text-muted">Try adjusting your search or filters.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>