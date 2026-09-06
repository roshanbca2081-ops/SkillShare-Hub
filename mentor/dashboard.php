<?php
$page_title = 'Mentor Dashboard';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';
require_once '../config/auth.php';

requireMentor();

$user_id = getUserId();

// Get mentor data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$mentor = $stmt->fetch();

// Get statistics
$stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE mentor_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$active_courses = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE mentor_id = ? AND status = 'pending'");
$stmt->execute([$user_id]);
$pending_courses = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.mentor_id = ? AND e.status = 'active'");
$stmt->execute([$user_id]);
$active_students = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings b JOIN sessions s ON b.session_id = s.id WHERE s.mentor_id = ? AND b.status = 'pending'");
$stmt->execute([$user_id]);
$pending_bookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND status = 'scheduled' AND scheduled_at > NOW()");
$stmt->execute([$user_id]);
$upcoming_sessions = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND status = 'ongoing'");
$stmt->execute([$user_id]);
$ongoing_sessions = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT AVG(rating) FROM ratings WHERE mentor_id = ?");
$stmt->execute([$user_id]);
$avg_rating = $stmt->fetchColumn() ?: 0;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM ratings WHERE mentor_id = ?");
$stmt->execute([$user_id]);
$total_reviews = $stmt->fetchColumn();

// Get recent bookings
$stmt = $pdo->prepare("SELECT b.*, s.title as session_title, s.scheduled_at, u.full_name as student_name, u.avatar 
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON b.fresher_id = u.id 
                       WHERE s.mentor_id = ? AND b.status = 'pending'
                       ORDER BY b.booking_date DESC LIMIT 5");
$stmt->execute([$user_id]);
$pending_bookings_list = $stmt->fetchAll();

// Get recent students
$stmt = $pdo->prepare("SELECT DISTINCT u.id, u.full_name, u.avatar, u.email, e.started_at AS enrolled_at, e.progress 
                       FROM enrollments e 
                       JOIN users u ON e.fresher_id = u.id 
                       JOIN courses c ON e.course_id = c.id 
                       WHERE c.mentor_id = ? AND e.status = 'active'
                       ORDER BY e.last_accessed_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_students = $stmt->fetchAll();

// Get upcoming sessions
$stmt = $pdo->prepare("SELECT * FROM sessions WHERE mentor_id = ? AND status IN ('scheduled', 'ongoing') AND scheduled_at > NOW() ORDER BY scheduled_at LIMIT 5");
$stmt->execute([$user_id]);
$upcoming_sessions_list = $stmt->fetchAll();

// Get recent earnings
$stmt = $pdo->prepare("SELECT SUM(p.amount) as total, COUNT(*) as count 
                       FROM payments p 
                       JOIN courses c ON p.course_id = c.id 
                       WHERE c.mentor_id = ? AND p.status = 'completed'
                       AND p.payment_date > DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stmt->execute([$user_id]);
$monthly_earnings = $stmt->fetch();

// Get total earnings
$stmt = $pdo->prepare("SELECT SUM(p.amount) as total FROM payments p JOIN courses c ON p.course_id = c.id WHERE c.mentor_id = ? AND p.status = 'completed'");
$stmt->execute([$user_id]);
$total_earnings = $stmt->fetchColumn() ?: 0;

// Get unread notifications
$unread_notifications = getUnreadNotifications($pdo, $user_id);
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../includes/mentor-sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge bg-primary me-2">
                        <i class="fas fa-star"></i> <?php echo number_format($avg_rating, 1); ?> ★ (<?php echo $total_reviews; ?>)
                    </span>
                    <a href="my-courses/create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Course
                    </a>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="dashboard-widget">
                        <div class="dashboard-widget-header">
                            <div class="dashboard-widget-icon dashboard-widget-icon-primary">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <span class="badge bg-primary">Active</span>
                        </div>
                        <div class="dashboard-widget-value"><?php echo $active_courses; ?></div>
                        <div class="dashboard-widget-label">Active Courses</div>
                        <small class="text-muted"><?php echo $pending_courses; ?> pending</small>
                    </div>
                </div>
                
                <div class="col-md-3 col-6">
                    <div class="dashboard-widget">
                        <div class="dashboard-widget-header">
                            <div class="dashboard-widget-icon dashboard-widget-icon-success">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="dashboard-widget-value"><?php echo $active_students; ?></div>
                        <div class="dashboard-widget-label">Active Students</div>
                        <small class="text-muted">Enrolled in your courses</small>
                    </div>
                </div>
                
                <div class="col-md-3 col-6">
                    <div class="dashboard-widget">
                        <div class="dashboard-widget-header">
                            <div class="dashboard-widget-icon dashboard-widget-icon-warning">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <span class="badge bg-warning">Upcoming</span>
                        </div>
                        <div class="dashboard-widget-value"><?php echo $upcoming_sessions + $ongoing_sessions; ?></div>
                        <div class="dashboard-widget-label">Sessions</div>
                        <small class="text-muted"><?php echo $ongoing_sessions; ?> ongoing</small>
                    </div>
                </div>
                
                <div class="col-md-3 col-6">
                    <div class="dashboard-widget">
                        <div class="dashboard-widget-header">
                            <div class="dashboard-widget-icon dashboard-widget-icon-danger">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <span class="badge bg-danger">Revenue</span>
                        </div>
                        <div class="dashboard-widget-value">$<?php echo number_format($total_earnings, 0); ?></div>
                        <div class="dashboard-widget-label">Total Earnings</div>
                        <small class="text-muted">$<?php echo number_format($monthly_earnings['total'] ?? 0, 0); ?> this month</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Pending Bookings -->
                    <?php if (!empty($pending_bookings_list)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-clock"></i> Pending Booking Requests</h6>
                                <a href="booking/index.php" class="btn btn-sm btn-dark">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($pending_bookings_list as $booking): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo getAvatar($booking); ?>" class="rounded-circle me-2" style="width: 36px; height: 36px;">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($booking['student_name']); ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> <?php echo formatDateTime($booking['scheduled_at']); ?>
                                            <br><i class="fas fa-book"></i> <?php echo htmlspecialchars($booking['session_title']); ?>
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <a href="booking/approve.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="booking/reject.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Reject this booking?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    <a href="booking/view.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Recent Students -->
                    <?php if (!empty($recent_students)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-user-graduate"></i> Recent Students</h6>
                                <a href="students.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($recent_students as $student): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo getAvatar($student); ?>" class="rounded-circle me-2" style="width: 36px; height: 36px;">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($student['full_name']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($student['email']); ?>
                                            • Progress: <?php echo round($student['progress'] ?? 0); ?>%
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <a href="message/index.php?user_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Upcoming Sessions -->
                    <?php if (!empty($upcoming_sessions_list)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Upcoming Sessions</h6>
                                <a href="session/index.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($upcoming_sessions_list as $session): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($session['title']); ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?>
                                        • <?php echo $session['duration']; ?> min
                                    </small>
                                </div>
                                <div>
                                    <span class="badge bg-<?php echo $session['status'] === 'ongoing' ? 'success' : 'primary'; ?>">
                                        <?php echo ucfirst($session['status']); ?>
                                    </span>
                                    <a href="session/view.php?id=<?php echo $session['id']; ?>" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6><i class="fas fa-bolt text-primary"></i> Quick Actions</h6>
                            <div class="d-grid gap-2">
                                <a href="session/create.php" class="btn btn-outline-primary">
                                    <i class="fas fa-calendar-plus"></i> Schedule Session
                                </a>
                                <a href="my-courses/create.php" class="btn btn-outline-success">
                                    <i class="fas fa-plus"></i> Create Course
                                </a>
                                <a href="resources/upload.php" class="btn btn-outline-info">
                                    <i class="fas fa-upload"></i> Upload Resource
                                </a>
                                <a href="assignments/create.php" class="btn btn-outline-warning">
                                    <i class="fas fa-tasks"></i> Create Assignment
                                </a>
                                <a href="certificates/issue.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-certificate"></i> Issue Certificate
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Summary -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <img src="<?php echo getAvatar($mentor); ?>" class="rounded-circle mb-3" style="width: 80px; height: 80px;">
                            <h6><?php echo htmlspecialchars($mentor['full_name']); ?></h6>
                            <p class="text-muted small"><?php echo htmlspecialchars($mentor['title'] ?? 'Mentor'); ?></p>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <h6><?php echo $active_courses; ?></h6>
                                    <small class="text-muted">Courses</small>
                                </div>
                                <div class="col-4">
                                    <h6><?php echo $active_students; ?></h6>
                                    <small class="text-muted">Students</small>
                                </div>
                                <div class="col-4">
                                    <h6><?php echo number_format($avg_rating, 1); ?></h6>
                                    <small class="text-muted">Rating</small>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <a href="account/profile.php" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-user-edit"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>