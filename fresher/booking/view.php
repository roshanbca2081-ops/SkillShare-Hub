<?php
$page_title = 'Booking Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = getUserId();

// Get booking details
$stmt = $pdo->prepare("SELECT b.*, s.id as session_id, s.title, s.description, s.scheduled_at, s.duration, 
                       s.meeting_link, s.recording_url, s.status as session_status, s.is_free, s.price,
                       u.full_name as mentor_name, u.id as mentor_id, u.avatar, u.bio as mentor_bio, u.skills,
                       c.id as course_id, c.title as course_title, c.thumbnail
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON s.mentor_id = u.id 
                       LEFT JOIN courses c ON s.course_id = c.id 
                       WHERE b.id = ? AND b.fresher_id = ?");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect('index.php');
}

// Get rating if exists
$stmt = $pdo->prepare("SELECT * FROM ratings WHERE fresher_id = ? AND session_id = ?");
$stmt->execute([$user_id, $booking['session_id']]);
$rating = $stmt->fetch();

$is_upcoming = $booking['status'] === 'approved' && strtotime($booking['scheduled_at']) > time();
$is_pending = $booking['status'] === 'pending';
$is_completed = $booking['status'] === 'completed' || strtotime($booking['scheduled_at']) < time();
$is_cancelled = $booking['status'] === 'cancelled';
$is_rejected = $booking['status'] === 'rejected';

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND fresher_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'warning',
        'icon' => 'exclamation-circle',
        'message' => 'Booking cancelled successfully.'
    ];
    redirect('view.php?id=' . $booking_id);
}

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $rating_value = (int)$_POST['rating'];
    $review = sanitize($_POST['review'] ?? '');
    
    if ($rating_value >= 1 && $rating_value <= 5) {
        $stmt = $pdo->prepare("INSERT INTO ratings (mentor_id, fresher_id, session_id, rating, review) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$booking['mentor_id'], $user_id, $booking['session_id'], $rating_value, $review]);
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Thank you for your feedback!'
        ];
        redirect('view.php?id=' . $booking_id);
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
                <h1 class="h2">Booking Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> My Bookings
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Booking Status -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <h3><?php echo htmlspecialchars($booking['title']); ?></h3>
                                <span class="badge bg-<?php 
                                    echo $is_upcoming ? 'success' : 
                                        ($is_pending ? 'warning' : 
                                        ($is_completed ? 'secondary' : 
                                        ($is_cancelled ? 'danger' : 'danger'))); 
                                ?> fs-6">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </div>
                            
                            <?php if ($booking['course_title']): ?>
                                <p class="text-muted">
                                    <i class="fas fa-book"></i> Part of: 
                                    <a href="../../public/course-detail.php?id=<?php echo $booking['course_id']; ?>">
                                        <?php echo htmlspecialchars($booking['course_title']); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            
                            <div class="row g-3 mt-3">
                                <div class="col-md-6">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Date & Time</small>
                                        <p class="mb-0"><i class="fas fa-calendar text-primary"></i> <?php echo formatDateTime($booking['scheduled_at']); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Duration</small>
                                        <p class="mb-0"><i class="fas fa-clock text-primary"></i> <?php echo $booking['duration']; ?> minutes</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Price</small>
                                        <p class="mb-0">
                                            <?php if ($booking['is_free']): ?>
                                                <span class="badge bg-success">Free</span>
                                            <?php else: ?>
                                                <span class="fw-bold">$<?php echo number_format($booking['price'], 2); ?></span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-light p-3 rounded">
                                        <small class="text-muted">Booking Date</small>
                                        <p class="mb-0"><i class="fas fa-clock text-primary"></i> <?php echo formatDateTime($booking['booking_date']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($booking['notes']): ?>
                            <div class="mt-3">
                                <h6>Notes</h6>
                                <p class="bg-light p-3 rounded"><?php echo nl2br(htmlspecialchars($booking['notes'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($booking['description']): ?>
                            <div class="mt-3">
                                <h6>Session Description</h6>
                                <p><?php echo nl2br(htmlspecialchars($booking['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Mentor Info -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>Mentor</h5>
                            <div class="d-flex align-items-start">
                                <img src="<?php echo getAvatar($booking); ?>" class="rounded-circle me-3" style="width: 60px; height: 60px;">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($booking['mentor_name']); ?></h6>
                                    <p class="text-muted small"><?php echo htmlspecialchars($booking['mentor_bio'] ?? ''); ?></p>
                                    <?php if ($booking['skills']): ?>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach (explode(',', $booking['skills']) as $skill): ?>
                                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mt-2">
                                         <a href="../mentor/details.php?id=<?php echo $booking['mentor_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-user"></i> View Full Profile
                                        </a>
                                         <a href="../message/index.php?user_id=<?php echo $booking['mentor_id']; ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-envelope"></i> Send Message
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Actions -->
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h5>Actions</h5>
                            
                            <?php if ($is_upcoming): ?>
                                <?php if ($booking['meeting_link']): ?>
                                    <a href="<?php echo $booking['meeting_link']; ?>" target="_blank" class="btn btn-success w-100 mb-2">
                                        <i class="fas fa-video"></i> Join Session
                                    </a>
                                <?php endif; ?>
                                
                                <form method="POST">
                                    <button type="submit" name="cancel_booking" class="btn btn-danger w-100 mb-2" 
                                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                                        <i class="fas fa-times"></i> Cancel Booking
                                    </button>
                                </form>
                                
                                <button class="btn btn-outline-primary w-100" onclick="addToCalendar()">
                                    <i class="fas fa-calendar-plus"></i> Add to Calendar
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($is_pending): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock"></i> Waiting for mentor approval
                                </div>
                                <form method="POST">
                                    <button type="submit" name="cancel_booking" class="btn btn-danger w-100" 
                                            onclick="return confirm('Cancel this booking request?')">
                                        <i class="fas fa-times"></i> Cancel Request
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($is_completed && !$rating): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-star"></i> Rate this session
                                </div>
                                
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Your Rating</label>
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
                                        <label class="form-label">Review (Optional)</label>
                                        <textarea name="review" class="form-control" rows="3" 
                                                  placeholder="Share your experience..."></textarea>
                                    </div>
                                    <button type="submit" name="submit_rating" class="btn btn-warning w-100" id="submitRating" disabled>
                                        <i class="fas fa-star"></i> Submit Rating
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($is_completed && $rating): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> You rated this session
                                    <div class="text-warning mt-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $rating['rating'] ? '' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <?php if ($rating['review']): ?>
                                        <p class="small mt-1">"<?php echo htmlspecialchars($rating['review']); ?>"</p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($is_cancelled || $is_rejected): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle"></i> 
                                    <?php echo $is_cancelled ? 'This booking has been cancelled.' : 'This booking has been rejected.'; ?>
                                </div>
                                <?php if ($is_cancelled): ?>
                                    <a href="create.php?session=<?php echo $booking['session_id']; ?>" class="btn btn-primary w-100">
                                        <i class="fas fa-redo"></i> Re-book Session
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <hr>
                            
                            <!-- Session Details -->
                            <h6 class="mt-3">Session Details</h6>
                            <ul class="list-unstyled small">
                                <li><strong>Booking ID:</strong> #<?php echo $booking['id']; ?></li>
                                <li><strong>Status:</strong> <?php echo ucfirst($booking['status']); ?></li>
                                <li><strong>Session:</strong> <?php echo htmlspecialchars($booking['title']); ?></li>
                                <?php if ($booking['session_status']): ?>
                                    <li><strong>Session Status:</strong> <?php echo ucfirst($booking['session_status']); ?></li>
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
    
    // Update star display
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
    
    // Enable submit button
    document.getElementById('submitRating').disabled = false;
}

function addToCalendar() {
    const title = "<?php echo htmlspecialchars($booking['title']); ?>";
    const date = "<?php echo date('Ymd\THis', strtotime($booking['scheduled_at'])); ?>";
    const duration = "<?php echo $booking['duration']; ?>";
    const endDate = new Date("<?php echo $booking['scheduled_at']; ?>");
    endDate.setMinutes(endDate.getMinutes() + parseInt(duration));
    const end = endDate.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
    
    const url = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${date}/${end}&details=${encodeURIComponent('Session with <?php echo htmlspecialchars($booking['mentor_name']); ?>')}`;
    window.open(url, '_blank');
}
</script>

<style>
.rating-stars .star {
    font-size: 30px;
    cursor: pointer;
    transition: all 0.2s;
    padding: 0 3px;
}

.rating-stars .star:hover {
    transform: scale(1.2);
}
</style>

<?php include '../../includes/footer.php'; ?>