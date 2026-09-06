<?php
$page_title = 'Mentors';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;

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

$query .= " ORDER BY avg_rating DESC, course_count DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$mentors = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto text-center mb-5">
            <h1 class="display-4 fw-bold">Our Mentors</h1>
            <p class="text-muted">Learn from experienced professionals who are passionate about teaching</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search mentors by name, expertise..." value="<?php echo $search ?? ''; ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                <?php if ($search): ?>
                    <a href="mentor.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="row g-4">
        <?php foreach ($mentors as $mentor): ?>
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center p-4">
                    <img src="<?php echo getAvatar($mentor); ?>" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    <h5><?php echo htmlspecialchars($mentor['full_name']); ?></h5>
                    <p class="text-muted small"><?php echo htmlspecialchars($mentor['bio'] ?? 'Experienced mentor'); ?></p>
                    
                    <?php if ($mentor['avg_rating']): ?>
                    <div class="text-warning mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= round($mentor['avg_rating']) ? '' : 'text-muted'; ?>"></i>
                        <?php endfor; ?>
                        <small class="text-muted">(<?php echo $mentor['review_count'] ?? 0; ?> reviews)</small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary"><?php echo $mentor['course_count'] ?? 0; ?> Courses</span>
                    </div>
                    
                    <a href="mentor-profile.php?id=<?php echo $mentor['id']; ?>" class="btn btn-outline-primary">View Profile</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($mentors)): ?>
        <div class="col-12 text-center py-5">
            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
            <h5>No mentors found</h5>
            <p class="text-muted"><?php echo $search ? 'Try a different search term.' : 'Check back later for new mentors.'; ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>