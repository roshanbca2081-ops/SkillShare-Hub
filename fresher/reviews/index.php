<?php
$page_title = 'My Reviews';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT r.*, u.full_name as mentor_name, u.avatar,
                       c.title as course_title, s.title as session_title
                       FROM ratings r 
                       JOIN users u ON r.mentor_id = u.id 
                       LEFT JOIN courses c ON r.course_id = c.id 
                       LEFT JOIN sessions s ON r.session_id = s.id 
                       WHERE r.fresher_id = ?
                       ORDER BY r.created_at DESC");
$stmt->execute([$user_id]);
$reviews = $stmt->fetchAll();
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
                <h1 class="h2">My Reviews</h1>
            </div>
            
            <?php if (!empty($reviews)): ?>
                <div class="row g-4">
                    <?php foreach ($reviews as $review): ?>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <img src="<?php echo getAvatar($review); ?>" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                                    <div class="flex-grow-1">
                                        <h6><?php echo htmlspecialchars($review['mentor_name']); ?></h6>
                                        <div class="text-warning mb-1">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="mb-1"><?php echo htmlspecialchars($review['review'] ?? ''); ?></p>
                                        <small class="text-muted">
                                            <?php if ($review['course_title']): ?>
                                                Course: <?php echo htmlspecialchars($review['course_title']); ?>
                                            <?php elseif ($review['session_title']): ?>
                                                Session: <?php echo htmlspecialchars($review['session_title']); ?>
                                            <?php endif; ?>
                                            <br><?php echo getTimeAgo($review['created_at']); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <h5>No reviews yet</h5>
                    <p class="text-muted">Your reviews will appear here after you rate courses or sessions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>