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
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $contact = sanitize($_POST['contact'] ?? '');
    $interested_course = sanitize($_POST['interested_course'] ?? '');
    $interested_skill = sanitize($_POST['interested_skill'] ?? '');
    $why_choose_skill = sanitize($_POST['why_choose_skill'] ?? '');
    $preferred_time = sanitize($_POST['preferred_time'] ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? '');
    $payment_before_session = isset($_POST['payment_before_session']) ? 1 : 0;
    $notes = sanitize($_POST['notes'] ?? '');
    
    $errors = [];
    
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($contact)) $errors[] = 'Contact number is required.';
    if (empty($interested_course)) $errors[] = 'Please select a course.';
    if (empty($interested_skill)) $errors[] = 'Please select a skill.';
    if (empty($why_choose_skill)) $errors[] = 'Please explain why you chose this skill.';
    if (empty($preferred_time)) $errors[] = 'Please select a preferred time.';
    if (empty($payment_method)) $errors[] = 'Please select a payment method.';
    
    if (empty($errors)) {
        $user_id = getUserId();
        $payment_status = $payment_before_session ? 'pending' : 'after_session';
        $booking_details = [
            'name' => $name,
            'email' => $email,
            'address' => $address,
            'contact' => $contact,
            'interested_course' => $interested_course,
            'interested_skill' => $interested_skill,
            'why_choose_skill' => $why_choose_skill,
            'preferred_time' => $preferred_time,
            'payment_method' => $payment_method,
            'payment_status' => $payment_status
        ];
        $enhanced_notes = $notes . "\n\n[Booking Details]\n" . print_r($booking_details, true);
        
        $check_stmt = $pdo->prepare("SELECT id, status FROM bookings WHERE fresher_id = ? AND session_id = ?");
        $check_stmt->execute([$user_id, $session_id]);
        $existing = $check_stmt->fetch();
        
        if ($existing) {
            $_SESSION['alert'] = [
                'type' => 'warning',
                'icon' => 'exclamation-circle',
                'message' => 'You have already booked this session. Your booking status: ' . ucfirst($existing['status']) . '.'
            ];
            redirect('create.php?session=' . $session_id);
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, name, email, address, contact, interested_course, interested_skill, why_choose_skill, preferred_time, payment_method, payment_status, notes, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $session_id, $name, $email, $address, $contact, $interested_course, $interested_skill, $why_choose_skill, $preferred_time, $payment_method, $payment_status, $notes]);
        } catch (PDOException $e) {
            $stmt = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, notes, status) 
                                   VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $session_id, $enhanced_notes]);
        }
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
                                           'mentor/booking/view.php?id=' || ?)");
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
        
        if ($payment_before_session && !in_array($payment_method, ['after_session'])) {
            redirect('payment/checkout.php?booking=' . $booking_id);
        } else {
            redirect('index.php');
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => implode('<br>', $errors)
        ];
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
                                    
                                    <!-- Personal Information -->
                                    <h6 class="mb-3">Personal Information</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user']['full_name'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                                <input type="tel" name="contact" class="form-control" placeholder="+977-XXXXXXXXX" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <input type="text" name="address" class="form-control" placeholder="Your address">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Course & Skill Information -->
                                    <h6 class="mb-3">Course & Skill Information</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Interested Course <span class="text-danger">*</span></label>
                                                <select name="interested_course" class="form-select" required>
                                                    <option value="">Select a course</option>
                                                    <?php if ($session['course_title']): ?>
                                                        <option value="<?php echo htmlspecialchars($session['course_title']); ?>" selected>
                                                            <?php echo htmlspecialchars($session['course_title']); ?>
                                                        </option>
                                                    <?php endif; ?>
                                                    <?php
                                                    $stmt = $pdo->query("SELECT title FROM courses WHERE status = 'active' ORDER BY title");
                                                    while ($course = $stmt->fetch()) {
                                                        if (!$session['course_title'] || $course['title'] != $session['course_title']) {
                                                            echo '<option value="' . htmlspecialchars($course['title']) . '">' . htmlspecialchars($course['title']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Interested Skill <span class="text-danger">*</span></label>
                                                <select name="interested_skill" class="form-select" required>
                                                    <option value="">Select a skill</option>
                                                    <option value="Web Development">Web Development</option>
                                                    <option value="Mobile Development">Mobile Development</option>
                                                    <option value="Data Science">Data Science</option>
                                                    <option value="UI/UX Design">UI/UX Design</option>
                                                    <option value="Digital Marketing">Digital Marketing</option>
                                                    <option value="Cloud Computing">Cloud Computing</option>
                                                    <option value="Cybersecurity">Cybersecurity</option>
                                                    <option value="AI/ML">AI/ML</option>
                                                    <option value="Blockchain">Blockchain</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Why do you want to learn this skill? <span class="text-danger">*</span></label>
                                                <textarea name="why_choose_skill" class="form-control" rows="3" required placeholder="Tell us about your goals and why you want to learn this skill..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Schedule & Payment -->
                                    <h6 class="mb-3">Schedule & Payment</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Preferred Time <span class="text-danger">*</span></label>
                                                <input type="text" name="preferred_time" class="form-control" placeholder="e.g., Weekdays 6-8 PM, Weekends" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                                <select name="payment_method" class="form-select" required>
                                                    <option value="">Select payment method</option>
                                                    <option value="esewa">eSewa</option>
                                                    <option value="khalti">Khalti</option>
                                                    <option value="fonepay">Fonepay</option>
                                                    <option value="bank">Bank Transfer</option>
                                                    <option value="card">Credit/Debit Card</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label">Payment Timing</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_before_session" value="1" id="payBefore" checked>
                                                    <label class="form-check-label" for="payBefore">
                                                        Pay before session starts
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_before_session" value="0" id="payAfter">
                                                    <label class="form-check-label" for="payAfter">
                                                        Pay after session completes
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Notes -->
                                    <div class="mb-3">
                                        <label class="form-label">Additional Notes (Optional)</label>
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