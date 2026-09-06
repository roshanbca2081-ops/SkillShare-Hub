<?php
$page_title = 'Search Results';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';

$results = [];

if ($query) {
    // Search courses
    $stmt = $pdo->prepare("SELECT id, title, description, 'course' as type, thumbnail, mentor_id, price 
                           FROM courses WHERE status = 'active' AND (title LIKE ? OR description LIKE ?)");
    $stmt->execute(["%$query%", "%$query%"]);
    $courses = $stmt->fetchAll();
    
    // Search mentors
    $stmt = $pdo->prepare("SELECT id, full_name, bio, 'mentor' as type, avatar, skills 
                           FROM users WHERE role = 'mentor' AND is_active = 1 AND (full_name LIKE ? OR bio LIKE ? OR skills LIKE ?)");
    $stmt->execute(["%$query%", "%$query%", "%$query%"]);
    $mentors = $stmt->fetchAll();
    
    $results = array_merge($courses, $mentors);
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container py-5">
    <h1 class="display-4 fw-bold text-center mb-4">Search Results</h1>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="GET" action="search.php" class="mb-4">
                <div class="input-group">
                    <input type="text" name="q" class="form-control form-control-lg" placeholder="Search for courses, mentors..." value="<?php echo htmlspecialchars($query); ?>" required>
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-search"></i> Search</button>
                </div>
            </form>
            
            <?php if ($query): ?>
                <p class="text-muted">Found <?php echo count($results); ?> results for "<?php echo htmlspecialchars($query); ?>"</p>
                
                <?php if (!empty($results)): ?>
                    <div class="search-results">
                        <?php foreach ($results as $result): ?>
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <?php if ($result['type'] === 'course'): ?>
                                        <div class="d-flex align-items-start">
                                            <img src="<?php echo $result['thumbnail'] ?? '../assets/images/course-placeholder.jpg'; ?>" class="me-3" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                            <div>
                                                <span class="badge bg-primary mb-1">Course</span>
                                                <h5><?php echo htmlspecialchars($result['title']); ?></h5>
                                                <p class="text-muted small"><?php echo substr($result['description'] ?? '', 0, 150); ?>...</p>
                                                <a href="course-detail.php?id=<?php echo $result['id']; ?>" class="btn btn-sm btn-outline-primary">View Course</a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-start">
                                            <img src="<?php echo getAvatar($result); ?>" class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                            <div>
                                                <span class="badge bg-success mb-1">Mentor</span>
                                                <h5><?php echo htmlspecialchars($result['full_name']); ?></h5>
                                                <p class="text-muted small"><?php echo substr($result['bio'] ?? '', 0, 150); ?>...</p>
                                                <a href="mentor-profile.php?id=<?php echo $result['id']; ?>" class="btn btn-sm btn-outline-primary">View Profile</a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5>No results found</h5>
                        <p class="text-muted">Try different keywords or browse our courses and mentors.</p>
                        <a href="courses.php" class="btn btn-outline-primary">Browse Courses</a>
                        <a href="mentor.php" class="btn btn-outline-secondary">Find Mentors</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p>Enter a search term to find courses and mentors.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>