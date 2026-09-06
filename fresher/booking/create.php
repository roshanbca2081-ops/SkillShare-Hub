<?php
$page_title = 'Book a Session';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$mentor_id = isset($_GET['mentor']) ? (int)$_GET['mentor'] : 0;
$session_id = isset($_GET['session']) ? (int)$_GET['session'] : 0;

// Get available sessions
$query = "SELECT s.*, u.full_name as mentor_name, u.id as mentor_id, u.avatar, u.bio as mentor_bio,
          c.id as course_id, c.title as course_title
          FROM sessions s 
          JOIN users u ON s.mentor_id = u.id 
          LEFT JOIN courses c ON s.course_id = c.id 
          WHERE s.status = 'scheduled' AND s.scheduled_at > NOW()";

$params = [];

if ($mentor_id) {
    $query .= " AND s.mentor_id = ?";
    $params[] = $mentor_id;
}

if ($session_id) {
    $query .= " AND s.id = ?";
    $params[] = $session_id;
}

$query .= " ORDER BY s.scheduled_at";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sessions = $stmt->fetchAll();

// Get mentor list for filter
$mentors = $pdo->query("SELECT id, full_name FROM users WHERE role = 'mentor' AND is_active = 1 ORDER BY full_name")->fetchAll();

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_session'])) {
    $session_id = (int)$_POST['session_id'];
    $notes = sanitize($_POST['notes'] ?? '');
    
    // Check if already booked
    $stmt = $pdo->prepare("SELECT id FROM bookings WHERE fresher_id = ? AND session_id = ?");
    $stmt->execute([$user_id, $session_id]);
    if ($stmt->fetch()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'You have already booked this session.'
        ];
        redirect('create.php');
    }
    
    // Check session capacity
    $stmt = $pdo->prepare("SELECT max_participants, (SELECT COUNT(*) FROM bookings WHERE session_id = ? AND status = 'approved') as booked_count 
                           FROM sessions WHERE id = ?");
    $stmt->execute([$session_id, $session_id]);
    $session_data = $stmt->fetch();
    
    if ($session_data && $session_data['booked_count'] >= $session_data['max_participants']) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'This session is fully booked.'
        ];
        redirect('create.php');
    }
    
    // Create booking
    $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, notes, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$user_id, $session_id, $notes]);
    $booking_id = $pdo->lastInsertId();
    
    // Get session and mentor details for notification
    $stmt = $pdo->prepare("SELECT s.title, s.mentor_id, u.full_name as mentor_name 
                           FROM sessions s 
                           JOIN users u ON s.mentor_id = u.id 
                           WHERE s.id = ?");
    $stmt->execute([$session_id]);
    $session_info = $stmt->fetch();
    
    // Notify mentor
    if ($session_info) {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) 
                               VALUES (?, 'new_booking', 'New Booking Request', 
                                       CONCAT(?, ' has requested to book your session: ', ?),
                                       'mentor/bookings/view.php?id=' || ?)");
        $stmt->execute([
            $session_info['mentor_id'],
            getUserName(),
            $session_info['title'],
            $booking_id
        ]);
    }
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Booking request sent successfully! Waiting for mentor approval.'
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
                <h1 class="h2">Book a Session</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> My Bookings
                    </a>
                </div>
            </div>
            
            <?php if ($session_id && !empty($sessions)): ?>
                <!-- Direct Booking Form -->
                <?php $session = $sessions[0]; ?>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h4>Book: <?php echo htmlspecialchars($session['title']); ?></h4>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Mentor</small>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo getAvatar($session); ?>" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                                <span><?php echo htmlspecialchars($session['mentor_name']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Date & Time</small>
                                            <p class="mb-0"><i class="fas fa-calendar text-primary"></i> <?php echo formatDateTime($session['scheduled_at']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Duration</small>
                                            <p class="mb-0"><i class="fas fa-clock text-primary"></i> <?php echo $session['duration']; ?> minutes</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Price</small>
                                            <p class="mb-0">
                                                <?php if ($session['is_free']): ?>
                                                    <span class="badge bg-success">Free</span>
                                                <?php else: ?>
                                                    <span class="fw-bold">$<?php echo number_format($session['price'], 2); ?></span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if ($session['course_title']): ?>
                                    <div class="col-12">
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted">Course</small>
                                            <p class="mb-0"><i class="fas fa-book text-primary"></i> <?php echo htmlspecialchars($session['course_title']); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <form method="POST">
                                    <input type="hidden" name="session_id" value="<?php echo $session['id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Notes (Optional)</label>
                                        <textarea name="notes" class="form-control" rows="3" 
                                                  placeholder="Any specific topics you'd like to cover or questions for the mentor?"></textarea>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" name="book_session" class="btn btn-primary btn-lg">
                                            <i class="fas fa-calendar-check"></i> Confirm Booking
                                        </button>
                                        <a href="create.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left"></i> Choose Another Session
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Session Selection -->
                <!-- Filters -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Filter by Mentor</label>
                                <select name="mentor" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Mentors</option>
                                    <?php foreach ($mentors as $mentor): ?>
                                    <option value="<?php echo $mentor['id']; ?>" <?php echo $mentor_id == $mentor['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($mentor['full_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Apply Filter
                                </button>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <a href="create.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Available Sessions -->
                <?php if (!empty($sessions)): ?>
                    <div class="row g-4">
                        <?php foreach ($sessions as $session): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5><?php echo htmlspecialchars($session['title']); ?></h5>
                                        <?php if ($session['is_free']): ?>
                                            <span class="badge bg-success">Free</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="<?php echo getAvatar($session); ?>" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                        <span><?php echo htmlspecialchars($session['mentor_name']); ?></span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?>
                                        </span>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-clock"></i> <?php echo $session['duration']; ?> min
                                        </span>
                                    </div>
                                    
                                    <?php if ($session['course_title']): ?>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($session['course_title']); ?></span>
                                    <?php endif; ?>
                                    
                                    <p class="text-muted small mt-2"><?php echo substr($session['description'] ?? '', 0, 100); ?>...</p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span>
                                            <?php if (!$session['is_free']): ?>
                                                <span class="fw-bold">$<?php echo number_format($session['price'], 2); ?></span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="text-muted small">
                                            <i class="fas fa-users"></i> <?php echo $session['max_participants']; ?> spots
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <a href="create.php?session=<?php echo $session['id']; ?>" class="btn btn-primary w-100">
                                        <i class="fas fa-calendar-plus"></i> Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5>No sessions available</h5>
                        <p class="text-muted">There are no upcoming sessions available for booking.</p>
                        <a href="../dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>