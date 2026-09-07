<?php
$page_title = 'Mentor Profile';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';

$mentor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT u.*, 
                       (SELECT COUNT(*) FROM courses WHERE mentor_id = u.id AND status = 'active') as course_count,
                       (SELECT AVG(rating) FROM ratings WHERE mentor_id = u.id) as avg_rating,
                       (SELECT COUNT(*) FROM ratings WHERE mentor_id = u.id) as review_count
                       FROM users u 
                       WHERE u.id = ? AND u.role = 'mentor' AND u.is_active = 1");
$stmt->execute([$mentor_id]);
$mentor = $stmt->fetch();

if (!$mentor) {
    redirect('../404.php');
}

// Get courses
$stmt = $pdo->prepare("SELECT * FROM courses WHERE mentor_id = ? AND status = 'active' ORDER BY created_at DESC");
$stmt->execute([$mentor_id]);
$courses = $stmt->fetchAll();

// Get ratings
$stmt = $pdo->prepare("SELECT r.*, u.full_name, u.avatar 
                       FROM ratings r 
                       JOIN users u ON r.fresher_id = u.id 
                       WHERE r.mentor_id = ? 
                       ORDER BY r.created_at DESC LIMIT 10");
$stmt->execute([$mentor_id]);
$ratings = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-body text-center p-4">
                    <img src="<?php echo getAvatar($mentor); ?>" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4><?php echo htmlspecialchars($mentor['full_name']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($mentor['title'] ?? 'Mentor'); ?></p>
                    
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
                    
                    <?php if (isLoggedIn() && isFresher() && getUserId() != $mentor_id): ?>
                         <a href="../fresher/message/chat.php?user_id=<?php echo $mentor_id; ?>" class="btn btn-primary w-100 mb-2">
                             <i class="fas fa-envelope"></i> Send Message
                         </a>
                         <a href="../fresher/booking/create.php?mentor=<?php echo $mentor_id; ?>" class="btn btn-success w-100">
                             <i class="fas fa-calendar-check"></i> Book Session
                         </a>
                     <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Bio -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4>About</h4>
                    <p><?php echo nl2br(htmlspecialchars($mentor['bio'] ?? 'No bio available.')); ?></p>
                    
                    <?php if ($mentor['skills']): ?>
                    <h5>Skills & Expertise</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach (explode(',', $mentor['skills']) as $skill): ?>
                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars(trim($skill)); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Courses -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4>Courses</h4>
                    <?php if (!empty($courses)): ?>
                        <div class="row g-3">
                            <?php foreach ($courses as $course): ?>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <img src="<?php echo $course['thumbnail'] ?? '../assets/images/course-placeholder.jpg'; ?>" class="card-img-top" alt="<?php echo $course['title']; ?>" style="height: 140px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6><?php echo htmlspecialchars($course['title']); ?></h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary"><?php echo getCourseLevel($course['level']); ?></span>
                                            <span class="fw-bold">$<?php echo number_format($course['price'], 2); ?></span>
                                        </div>
                                        <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-primary mt-2 w-100">View Course</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No courses available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Reviews -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4>Reviews</h4>
                    <?php if (!empty($ratings)): ?>
                        <?php foreach ($ratings as $rating): ?>
                        <div class="d-flex border-bottom pb-3 mb-3">
                            <img src="<?php echo getAvatar($rating); ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <div class="d-flex align-items-center">
                                    <strong><?php echo htmlspecialchars($rating['full_name']); ?></strong>
                                    <small class="text-muted ms-2"><?php echo getTimeAgo($rating['created_at']); ?></small>
                                </div>
                                <div class="text-warning small">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $rating['rating'] ? '' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="mb-0"><?php echo htmlspecialchars($rating['review'] ?? ''); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No reviews yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>