<?php
$page_title = 'Join Session';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$session_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$code = isset($_GET['code']) ? sanitize($_GET['code']) : '';

// If code is provided, find session by meeting code
if ($code && !$session_id) {
    $stmt = $pdo->prepare("SELECT id FROM sessions WHERE meeting_id = ?");
    $stmt->execute([$code]);
    $session = $stmt->fetch();
    if ($session) {
        $session_id = $session['id'];
    }
}

if (!$session_id) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'icon' => 'exclamation-circle',
        'message' => 'Invalid session ID.'
    ];
    redirect('index.php');
}

// Get session details with booking verification
$stmt = $pdo->prepare("SELECT s.*, u.full_name as mentor_name, u.id as mentor_id, u.avatar,
                       c.id as course_id, c.title as course_title,
                       b.id as booking_id, b.status as booking_status,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'approved') as current_participants
                       FROM sessions s 
                       JOIN users u ON s.mentor_id = u.id 
                       LEFT JOIN courses c ON s.course_id = c.id 
                       LEFT JOIN bookings b ON b.session_id = s.id AND b.fresher_id = ? 
                       WHERE s.id = ?");
$stmt->execute([$user_id, $session_id]);
$session = $stmt->fetch();

if (!$session) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'icon' => 'exclamation-circle',
        'message' => 'Session not found.'
    ];
    redirect('index.php');
}

// Check if user has access
$has_access = false;
$can_join = false;
$join_message = '';

if ($session['booking_status'] === 'approved') {
    $has_access = true;
    $can_join = true;
    $join_message = 'You are registered for this session.';
} elseif ($session['booking_status'] === 'pending') {
    $has_access = false;
    $can_join = false;
    $join_message = 'Your booking request is pending approval.';
} elseif ($session['booking_status'] === 'cancelled') {
    $has_access = false;
    $can_join = false;
    $join_message = 'Your booking for this session has been cancelled.';
} elseif ($session['booking_status'] === 'rejected') {
    $has_access = false;
    $can_join = false;
    $join_message = 'Your booking request was rejected.';
} elseif ($session['is_free'] || ($session['course_id'] && $session['course_title'])) {
    // Check if enrolled in course
    if ($session['course_id']) {
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE fresher_id = ? AND course_id = ? AND status != 'dropped'");
        $stmt->execute([$user_id, $session['course_id']]);
        if ($stmt->fetch()) {
            $has_access = true;
            $can_join = true;
            $join_message = 'You are enrolled in the course. You can join this session.';
        } else {
            $has_access = false;
            $can_join = false;
            $join_message = 'You need to enroll in the course to join this session.';
        }
    } else {
        $has_access = false;
        $can_join = false;
        $join_message = 'You need to book this session to join.';
    }
} else {
    $has_access = false;
    $can_join = false;
    $join_message = 'You need to book this session to join.';
}

// Check if session is active
$is_active = false;
$session_status = $session['status'];
$current_time = time();
$session_time = strtotime($session['scheduled_at']);
$session_end = $session_time + ($session['duration'] * 60);

if ($session_status === 'scheduled' && $current_time >= $session_time && $current_time <= $session_end) {
    $is_active = true;
} elseif ($session_status === 'ongoing') {
    $is_active = true;
}

// Check if session has ended
$is_ended = $session_status === 'completed' || $current_time > $session_end;

// Handle join session (update attendance)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_session'])) {
    if ($has_access && $can_join && $is_active) {
        // Update attendance
        $stmt = $pdo->prepare("UPDATE bookings SET attended = 1 WHERE id = ?");
        $stmt->execute([$session['booking_id']]);
        
        // Log join
        $stmt = $pdo->prepare("INSERT INTO session_attendance (session_id, fresher_id, joined_at) 
                               VALUES (?, ?, NOW())");
        $stmt->execute([$session_id, $user_id]);
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'You have joined the session!'
        ];
    }
}

// Get meeting link
$meeting_link = $session['meeting_link'];
if (empty($meeting_link) && $session['meeting_id']) {
    // Generate meeting link if not provided
    $meeting_link = 'https://meet.jit.si/' . $session['meeting_id'];
}

// Get participant count
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT fresher_id) FROM session_attendance WHERE session_id = ?");
$stmt->execute([$session_id]);
$participant_count = $stmt->fetchColumn();

// Get participants list
$stmt = $pdo->prepare("SELECT u.id, u.full_name, u.avatar, sa.joined_at 
                       FROM session_attendance sa 
                       JOIN users u ON sa.fresher_id = u.id 
                       WHERE sa.session_id = ? 
                       ORDER BY sa.joined_at DESC LIMIT 20");
$stmt->execute([$session_id]);
$participants = $stmt->fetchAll();

// Handle leave session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_session'])) {
    $stmt = $pdo->prepare("UPDATE session_attendance SET left_at = NOW() 
                           WHERE session_id = ? AND fresher_id = ? AND left_at IS NULL");
    $stmt->execute([$session_id, $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'info',
        'icon' => 'info-circle',
        'message' => 'You have left the session.'
    ];
    redirect('my-sessions.php');
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
                <h1 class="h2">Join Session</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="my-sessions.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> My Sessions
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
                                    <p class="text-muted">
                                        <i class="fas fa-user"></i> 
                                        <?php echo htmlspecialchars($session['mentor_name']); ?>
                                        <?php if ($session['course_title']): ?>
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-book"></i> 
                                            <?php echo htmlspecialchars($session['course_title']); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-<?php 
                                        echo $is_active ? 'success' : ($is_ended ? 'secondary' : 'warning'); 
                                    ?> fs-6">
                                        <?php 
                                        if ($is_active) echo 'Live Now';
                                        elseif ($is_ended) echo 'Ended';
                                        else echo 'Upcoming';
                                        ?>
                                    </span>
                                </div>
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
                                            <?php echo $participant_count; ?> / <?php echo $session['max_participants']; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Join Section -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <?php if ($has_access && $can_join && $is_active): ?>
                                <?php if ($meeting_link): ?>
                                    <div class="mb-4">
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i> 
                                            <?php echo $join_message; ?>
                                        </div>
                                        <div class="join-room">
                                            <h4>Ready to join?</h4>
                                            <p class="text-muted">Click the button below to join the live session.</p>
                                            <div class="d-flex justify-content-center gap-3 mt-3">
                                                <a href="<?php echo $meeting_link; ?>" target="_blank" class="btn btn-success btn-lg">
                                                    <i class="fas fa-video"></i> Join Session Now
                                                </a>
                                                <form method="POST" class="d-inline">
                                                    <button type="submit" name="join_session" class="btn btn-outline-primary btn-lg">
                                                        <i class="fas fa-check"></i> Mark Attendance
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Meeting link is not available. Please contact the mentor.
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" class="mt-3">
                                    <button type="submit" name="leave_session" class="btn btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to leave this session?')">
                                        <i class="fas fa-sign-out-alt"></i> Leave Session
                                    </button>
                                </form>
                                
                            <?php elseif ($has_access && $can_join && !$is_active && !$is_ended): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-clock"></i>
                                    This session hasn't started yet.
                                    <br><small>Starts at <?php echo formatDateTime($session['scheduled_at']); ?></small>
                                </div>
                                <div class="countdown-timer mt-3" data-start="<?php echo $session_time; ?>">
                                    <h5>Time until start:</h5>
                                    <div class="display-4" id="countdown">--:--:--</div>
                                </div>
                                
                            <?php elseif ($is_ended): ?>
                                <div class="alert alert-secondary">
                                    <i class="fas fa-flag-checkered"></i>
                                    This session has ended.
                                </div>
                                <?php if ($session['recording_url']): ?>
                                    <a href="<?php echo $session['recording_url']; ?>" target="_blank" class="btn btn-primary">
                                        <i class="fas fa-play"></i> Watch Recording
                                    </a>
                                <?php endif; ?>
                                
                            <?php elseif (!$has_access): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-lock"></i>
                                    <?php echo $join_message; ?>
                                </div>
                                <?php if ($session['course_id'] && !$has_access): ?>
                                    <a href="../../public/course-detail.php?id=<?php echo $session['course_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-book"></i> Enroll in Course
                                    </a>
                                <?php else: ?>
                                    <a href="details.php?id=<?php echo $session['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus"></i> Book Session
                                    </a>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <div class="alert alert-secondary">
                                    <i class="fas fa-info-circle"></i>
                                    You cannot join this session at this time.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Participants -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0">
                                <i class="fas fa-users text-primary"></i> 
                                Participants (<?php echo $participant_count; ?>)
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($participants)): ?>
                                <div class="participants-list" style="max-height: 300px; overflow-y: auto;">
                                    <?php foreach ($participants as $participant): ?>
                                    <div class="d-flex align-items-center p-3 border-bottom">
                                        <img src="<?php echo getAvatar($participant); ?>" 
                                             class="rounded-circle me-2" 
                                             style="width: 32px; height: 32px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <span class="small"><?php echo htmlspecialchars($participant['full_name']); ?></span>
                                            <?php if ($participant['id'] == $user_id): ?>
                                                <span class="badge bg-primary">You</span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo getTimeAgo($participant['joined_at']); ?>
                                        </small>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small">No participants yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Session Info -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6><i class="fas fa-info-circle text-primary"></i> Session Info</h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-<?php 
                                        echo $is_active ? 'success' : ($is_ended ? 'secondary' : 'warning'); 
                                    ?>">
                                        <?php 
                                        if ($is_active) echo 'Live';
                                        elseif ($is_ended) echo 'Ended';
                                        else echo 'Scheduled';
                                        ?>
                                    </span>
                                </li>
                                <li class="mb-2">
                                    <strong>Mentor:</strong> 
                                    <?php echo htmlspecialchars($session['mentor_name']); ?>
                                </li>
                                <li class="mb-2">
                                    <strong>Duration:</strong> 
                                    <?php echo $session['duration']; ?> minutes
                                </li>
                                <li class="mb-2">
                                    <strong>Max Participants:</strong> 
                                    <?php echo $session['max_participants']; ?>
                                </li>
                                <li>
                                    <strong>Meeting ID:</strong> 
                                    <code><?php echo $session['meeting_id'] ?: 'N/A'; ?></code>
                                </li>
                            </ul>
                            
                            <?php if ($session['description']): ?>
                            <hr>
                            <h6>Description</h6>
                            <p class="small text-muted">
                                <?php echo nl2br(htmlspecialchars(substr($session['description'], 0, 150))); ?>
                                <?php if (strlen($session['description']) > 150): ?>...<?php endif; ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Countdown timer
document.addEventListener('DOMContentLoaded', function() {
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        const startTime = parseInt(countdownEl.parentElement.dataset.start) * 1000;
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = startTime - now;
            
            if (distance <= 0) {
                countdownEl.textContent = '00:00:00';
                location.reload();
                return;
            }
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            countdownEl.textContent = 
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        }
        
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
});

// Auto-refresh participants every 30 seconds
setInterval(function() {
    location.reload();
}, 30000);
</script>

<style>
.countdown-timer {
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    color: white;
}

.countdown-timer h5 {
    color: rgba(255,255,255,0.9);
}

#countdown {
    font-weight: bold;
    letter-spacing: 2px;
}

.participants-list::-webkit-scrollbar {
    width: 4px;
}

.participants-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}
</style>

<?php include '../../includes/footer.php'; ?>