<?php
$page_title = 'Bookmarks';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT w.*, c.id as course_id, c.title, c.thumbnail, c.price, c.level,
                       u.full_name as mentor_name, u.avatar
                       FROM wishlist w 
                       JOIN courses c ON w.course_id = c.id 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE w.fresher_id = ?
                       ORDER BY w.created_at DESC");
$stmt->execute([$user_id]);
$bookmarks = $stmt->fetchAll();

// Remove bookmark
if (isset($_GET['remove']) && isset($_GET['id'])) {
    $course_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE fresher_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Bookmark removed successfully.'
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
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Bookmarks</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../../public/courses.php" class="btn btn-primary">
                        <i class="fas fa-search"></i> Browse Courses
                    </a>
                </div>
            </div>
            
            <?php if (!empty($bookmarks)): ?>
                <div class="row g-4">
                    <?php foreach ($bookmarks as $bookmark): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="course-card">
                            <div class="course-card-image">
                                <img src="<?php echo $bookmark['thumbnail'] ?? '../../assets/images/course-placeholder.jpg'; ?>" 
                                     alt="<?php echo $bookmark['title']; ?>">
                                <div class="course-card-badge">
                                    <span class="badge bg-danger"><i class="fas fa-bookmark"></i></span>
                                </div>
                            </div>
                            <div class="course-card-body">
                                <h6 class="course-card-title"><?php echo htmlspecialchars($bookmark['title']); ?></h6>
                                <div class="course-card-meta">
                                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($bookmark['mentor_name']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-secondary"><?php echo getCourseLevel($bookmark['level']); ?></span>
                                    <span class="fw-bold">$<?php echo number_format($bookmark['price'], 2); ?></span>
                                </div>
                            </div>
                            <div class="course-card-footer">
                                <a href="../../public/course-detail.php?id=<?php echo $bookmark['course_id']; ?>" class="btn btn-sm btn-primary">
                                    View Course
                                </a>
                                <a href="?remove=1&id=<?php echo $bookmark['course_id']; ?>" class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Remove bookmark?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                    <h5>No bookmarks</h5>
                    <p class="text-muted">Save courses you're interested in for later.</p>
                    <a href="../../public/courses.php" class="btn btn-primary">Browse Courses</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>