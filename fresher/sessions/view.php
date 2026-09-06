<?php
$page_title = 'Session Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$session_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$session_id) {
    redirect('index.php');
}

// Get session details
$stmt = $pdo->prepare("SELECT s.*, u.full_name as mentor_name, u.id as mentor_id, u.avatar, u.bio as mentor_bio,
                       c.id as course_id, c.title as course_title, c.thumbnail,
                       b.id as booking_id, b.status as booking_status, b.notes as booking_notes,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'approved') as total_bookings,
                       (SELECT COUNT(*) FROM session_attendance WHERE session_id = s.id) as total_participants,
                       (SELECT AVG(rating) FROM ratings WHERE session_id = s.id) as avg_rating,
                       (SELECT COUNT(*) FROM ratings WHERE session_id = s.id) as total_ratings,
                       (SELECT r.rating FROM ratings r WHERE r.session_id = s.id AND r.fresher_id = ?) as user_rating,
                       (SELECT r.review FROM ratings r WHERE r.session_id = s.id AND r.fresher_id = ?) as user_review
                       FROM sessions s 
                       JOIN users u ON s.mentor_id = u.id 
                       LEFT JOIN courses c ON s.course_id = c.id 
                       LEFT JOIN bookings b ON b.session_id = s.id AND b.fresher_id = ? 
                       WHERE s.id = ?");
$stmt->execute([$user_id, $user_id, $user_id, $session_id]);
$session = $stmt->fetch();

if (!$session) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'icon' => 'exclamation-circle',
        'message' => 'Session not found.'
    ];
    redirect('index.php');
}

$is_upcoming = $session['status'] === 'scheduled' && strtotime($session['scheduled_at']) > time();
$is_ongoing = $session['status'] === 'ongoing' || (strtotime($session['scheduled_at']) <= time() && strtotime($session['scheduled_at']) + ($session['duration'] * 60) > time());
$is_completed = $session['status'] === 'completed' || strtotime($session['scheduled_at']) + ($session['duration'] * 60) < time();
$is_booked = $session['booking_status'] === 'approved';
$is_pending = $session['booking_status'] === 'pending';

// Handle booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_session'])) {
    if (!$session['booking_id']) {
        $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, notes) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $session_id, $_POST['notes'] ?? '']);
        
        // Notify mentor
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) 
                               VALUES (?, 'new_booking', 'New Booking Request', 
                                       CONCAT(?, ' has requested to book your session: ', ?),
                                       'mentor/bookings/view.php?id=' || ?)");
        $stmt->execute([
            $session['mentor_id'],
            getUserName(),
            $session['title'],
            $session_id
        ]);
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Booking request sent! Waiting for mentor approval.'
        ];
        redirect('view.php?id=' . $session_id);
    }
}

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND fresher_id = ?");
    $stmt->execute([$session['booking_id'], $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Booking cancelled successfully.'
    ];
    redirect('view.php?id=' . $session_id);
}

// Handle rating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $rating = (int)$_POST['rating'];
    $review = sanitize($_POST['review'] ?? '');
    
    if ($rating >= 1 && $rating <= 5) {
        // Check if already rated
        $stmt = $pdo->prepare("SELECT id FROM ratings WHERE fresher_id = ? AND session_id = ?");
        $stmt->execute([$user_id, $session_id]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("UPDATE ratings SET rating = ?, review = ? WHERE fresher_id = ? AND session_id = ?");
            $stmt->execute([$rating, $review, $user_id, $session_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO ratings (mentor_id, fresher_id, session_id, rating, review) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$session['mentor_id'], $user_id, $session_id, $rating, $review]);
        }
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Thank you for your feedback!'
        ];
        redirect('view.php?id=' . $session_id);
    }
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
                <h1 class="h2">Session Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> All Sessions
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Session Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3><?php echo htmlspecialchars($session['title']); ?></h3>
                                    <?php if ($session['course_title']): ?>
                                        <p class="text-muted">
                                            <i class="fas fa-book"></i> 
                                            <a href="../../public/course-detail.php?id=<?php echo $session['course_id']; ?>">
                                                <?php echo htmlspecialchars($session['course_title']); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-<?php 
                                    echo $is_ongoing ? 'success' : ($is_completed ? 'secondary' : 'primary'); 
                                ?> fs-6">
                                    <?php 
                                    if ($is_ongoing) echo '🟢 Ongoing';
                                    elseif ($is_completed) echo '✅ Completed';
                                    else echo '📅 Scheduled';
                                    ?>
                                </span>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Date & Time</small>
                                        <p class="mb-0">
                                            <i class="fas fa-calendar text-primary"></i> 
                                            <?php echo formatDateTime($session['scheduled_at']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Duration</small>
                                        <p class="mb-0">
                                            <i class="fas fa-clock text-primary"></i> 
                                            <?php echo $session['duration']; ?> minutes
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Participants</small>
                                        <p class="mb-0">
                                            <i class="fas fa-users text-primary"></i> 
                                            <?php echo $session['total_participants']; ?> / <?php echo $session['max_participants']; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($session['description']): ?>
                            <div class="mt-3">
                                <h6>Description</h6>
                                <p><?php echo nl2br(htmlspecialchars($session['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($session['booking_notes']): ?>
                            <div class="mt-3">
                                <h6>Your Notes</h6>
                                <p class="bg-light p-3 rounded"><?php echo nl2br(htmlspecialchars($session['booking_notes'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Mentor Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5>Mentor</h5>
                            <div class="d-flex align-items-start">
                                <img src="<?php echo getAvatar($session); ?>" 
                                     class="rounded-circle me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($session['mentor_name']); ?></h6>
                                    <p class="text-muted small"><?php echo htmlspecialchars($session['mentor_bio'] ?? ''); ?></p>
                                    <div>
                                        <a href="../mentors/details.php?id=<?php echo $session['mentor_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-user"></i> View Profile
                                        </a>
                                        <a href="../messages/index.php?user_id=<?php echo $session['mentor_id']; ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-envelope"></i> Send Message
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reviews -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>
                                Reviews 
                                <?php if ($session['avg_rating']): ?>
                                    <span class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= round($session['avg_rating']) ? '' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                        <small class="text-muted">(<?php echo $session['total_ratings']; ?>)</small>
                                    </span>
                                <?php endif; ?>
                            </h5>
                            
                            <?php if ($session['total_ratings'] > 0): ?>
                                <div class="mt-3">
                                    <?php
                                    $stmt = $pdo->prepare("SELECT r.*, u.full_name, u.avatar 
                                                           FROM ratings r 
                                                           JOIN users u ON r.fresher_id = u.id 
                                                           WHERE r.session_id = ? 
                                                           ORDER BY r.created_at DESC LIMIT 5");
                                    $stmt->execute([$session_id]);
                                    $reviews = $stmt->fetchAll();
                                    ?>
                                    <?php foreach ($reviews as $review): ?>
                                    <div class="d-flex border-bottom pb-3 mb-3">
                                        <img src="<?php echo getAvatar($review); ?>" 
                                             class="rounded-circle me-2" 
                                             style="width: 32px; height: 32px; object-fit: cover;">
                                        <div>
                                            <div class="d-flex align-items-center">
                                                <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                                                <small class="text-muted ms-2"><?php echo getTimeAgo($review['created_at']); ?></small>
                                            </div>
                                            <div class="text-warning small">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'text-muted'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <?php if ($review['review']): ?>
                                                <p class="small mb-0"><?php echo htmlspecialchars($review['review']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No reviews yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Action Card -->
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h5>Actions</h5>
                            
                            <?php if ($is_booked): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> You are booked for this session
                                </div>
                                
                                <?php if ($is_ongoing || $is_upcoming): ?>
                                    <a href="join.php?id=<?php echo $session_id; ?>" class="btn btn-success w-100 mb-2">
                                        <i class="fas fa-video"></i> Join Session
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($is_upcoming): ?>
                                    <form method="POST">
                                        <button type="submit" name="cancel_booking" class="btn btn-danger w-100" 
                                                onclick="return confirm('Cancel your booking?')">
                                            <i class="fas fa-times"></i> Cancel Booking
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($is_completed && !$session['user_rating']): ?>
                                    <hr>
                                    <h6>Rate this Session</h6>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <div class="rating-stars" id="ratingStars">
                                                <span class="star" data-value="1" onclick="setRating(1)">☆</span>
                                                <span class="star" data-value="2" onclick="setRating(2)">☆</span>
                                                <span class="star" data-value="3" onclick="setRating(3)">☆</span>
                                                <span class="star" data-value="4" onclick="setRating(4)">☆</span>
                                                <span class="star" data-value="5" onclick="setRating(5)">☆</span>
                                            </div>
                                            <input type="hidden" name="rating" id="ratingValue" value="0">
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="review" class="form-control" rows="3" 
                                                      placeholder="Share your experience..."></textarea>
                                        </div>
                                        <button type="submit" name="submit_rating" class="btn btn-warning w-100" id="submitRating" disabled>
                                            <i class="fas fa-star"></i> Submit Rating
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($is_completed && $session['user_rating']): ?>
                                    <div class="alert alert-secondary">
                                        <i class="fas fa-star text-warning"></i> 
                                        You rated this session: 
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $session['user_rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                                
                            <?php elseif ($is_pending): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock"></i> Booking pending approval
                                </div>
                                <form method="POST">
                                    <button type="submit" name="cancel_booking" class="btn btn-danger w-100" 
                                            onclick="return confirm('Cancel your booking request?')">
                                        <i class="fas fa-times"></i> Cancel Request
                                    </button>
                                </form>
                                
                            <?php elseif ($is_upcoming || $is_ongoing): ?>
                                <?php if ($session['is_free'] || ($session['course_id'] && $session['course_title'])): ?>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Notes (Optional)</label>
                                            <textarea name="notes" class="form-control" rows="2"></textarea>
                                        </div>
                                        <button type="submit" name="book_session" class="btn btn-primary w-100">
                                            <i class="fas fa-calendar-plus"></i> Book Session
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-credit-card"></i> 
                                        This session costs $<?php echo number_format($session['price'], 2); ?>
                                    </div>
                                    <a href="../payments/checkout.php?session=<?php echo $session_id; ?>" class="btn btn-success w-100">
                                        <i class="fas fa-credit-card"></i> Pay & Book
                                    </a>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <div class="alert alert-secondary">
                                    <i class="fas fa-flag-checkered"></i> This session has ended
                                </div>
                                <?php if ($session['recording_url']): ?>
                                    <a href="<?php echo $session['recording_url']; ?>" target="_blank" class="btn btn-primary w-100">
                                        <i class="fas fa-play"></i> Watch Recording
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <hr>
                            
                            <!-- Quick Stats -->
                            <h6>Session Stats</h6>
                            <ul class="list-unstyled small">
                                <li class="mb-1">
                                    <strong>Total Bookings:</strong> <?php echo $session['total_bookings']; ?>
                                </li>
                                <li class="mb-1">
                                    <strong>Participants:</strong> <?php echo $session['total_participants']; ?>
                                </li>
                                <li class="mb-1">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-<?php 
                                        echo $is_ongoing ? 'success' : ($is_completed ? 'secondary' : 'primary'); 
                                    ?>">
                                        <?php 
                                        if ($is_ongoing) echo 'Ongoing';
                                        elseif ($is_completed) echo 'Completed';
                                        else echo 'Scheduled';
                                        ?>
                                    </span>
                                </li>
                                <?php if ($session['meeting_id']): ?>
                                <li>
                                    <strong>Meeting ID:</strong> 
                                    <code><?php echo $session['meeting_id']; ?></code>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedRating = 0;

function setRating(value) {
    selectedRating = value;
    document.getElementById('ratingValue').value = value;
    
    const stars = document.querySelectorAll('#ratingStars .star');
    stars.forEach((star, index) => {
        if (index < value) {
            star.textContent = '★';
            star.style.color = '#f6ad55';
        } else {
            star.textContent = '☆';
            star.style.color = '#cbd5e0';
        }
    });
    
    document.getElementById('submitRating').disabled = false;
}
</script>

<style>
.rating-stars .star {
    font-size: 28px;
    cursor: pointer;
    transition: all 0.2s;
    padding: 0 2px;
}

.rating-stars .star:hover {
    transform: scale(1.2);
}
</style>

<?php include '../../includes/footer.php'; ?>