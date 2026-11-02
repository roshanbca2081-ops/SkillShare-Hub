<?php
$page_title = 'Dashboard';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';
require_once '../config/auth.php';

requireFresher();

$user_id = getUserId();

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$academicInterestOptions = require '../includes/academic-interest-options.php';
$selectedAcademicInterests = array_intersect(
    $academicInterestOptions,
    array_map('trim', explode(',', (string)($user['interests'] ?? '')))
);

// Get statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM enrollments WHERE fresher_id = ? AND status != 'dropped'");
$stmt->execute([$user_id]);
$total_courses = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM enrollments WHERE fresher_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$active_courses = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM enrollments WHERE fresher_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$completed_courses = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings b JOIN sessions s ON b.session_id = s.id WHERE b.fresher_id = ? AND b.status = 'approved' AND s.scheduled_at > NOW()");
$stmt->execute([$user_id]);
$upcoming_sessions = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM messages WHERE receiver_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$unread_messages = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$unread_notifications = $stmt->fetchColumn();

// Get recent courses
$stmt = $pdo->prepare("SELECT e.*, c.id as course_id, c.title, c.thumbnail, c.level, u.full_name as mentor_name 
                       FROM enrollments e 
                       JOIN courses c ON e.course_id = c.id 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE e.fresher_id = ? AND e.status != 'dropped'
                       ORDER BY e.last_accessed_at DESC LIMIT 4");
$stmt->execute([$user_id]);
$recent_courses = $stmt->fetchAll();

// Get upcoming sessions
$stmt = $pdo->prepare("SELECT b.*, s.id as session_id, s.title, s.scheduled_at, s.duration, s.meeting_link, 
                       u.full_name as mentor_name 
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON s.mentor_id = u.id 
                       WHERE b.fresher_id = ? AND b.status = 'approved' AND s.scheduled_at > NOW()
                       ORDER BY s.scheduled_at LIMIT 5");
$stmt->execute([$user_id]);
$upcoming = $stmt->fetchAll();

// Get recent notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_notifications = $stmt->fetchAll();

// Get pending assignments
$stmt = $pdo->prepare("SELECT a.*, c.title as course_title 
                       FROM assignments a 
                       JOIN courses c ON a.course_id = c.id 
                       JOIN enrollments e ON e.course_id = c.id 
                       WHERE e.fresher_id = ? 
                       AND a.due_date > NOW() 
                       AND a.is_published = 1
                       AND NOT EXISTS (
                           SELECT 1 FROM assignment_submissions 
                           WHERE assignment_id = a.id AND fresher_id = ?
                       )
                       ORDER BY a.due_date LIMIT 5");
$stmt->execute([$user_id, $user_id]);
$pending_assignments = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <!-- Welcome Section -->
            <header class="fresher-dashboard-head">
                <div class="fresher-dashboard-welcome">
                    <div class="fresher-dashboard-kicker">Welcome back,</div>
                    <h1><?php echo htmlspecialchars(strtoupper($user['full_name'])); ?>! <span aria-hidden="true">👋</span></h1>
                    <p>Keep learning, keep growing with SkillShare Hub.</p>
                </div>
                <i class="fas fa-graduation-cap fresher-hero-art" aria-hidden="true"></i>
                <div class="fresher-dashboard-profile">
                    <a class="fresher-profile-link" href="account/profile.php">
                        <?php echo htmlspecialchars($user['full_name']); ?> <i class="fas fa-caret-down" aria-hidden="true"></i>
                        <img src="<?php echo getAvatar($user); ?>" alt="Profile avatar">
                    </a>
                    <a class="fresher-dashboard-back" href="../public/courses.php" onclick="if (window.history.length > 1) { window.history.back(); return false; }">
                        <i class="fas fa-arrow-left me-2" aria-hidden="true"></i>Back
                    </a>
                </div>
            </header>

            <?php if (empty($selectedAcademicInterests)): ?>
                <section class="dashboard-onboarding-card mb-4" aria-label="Choose academic interests">
                    <span class="dashboard-onboarding-icon"><i class="fas fa-layer-group" aria-hidden="true"></i></span>
                    <div class="dashboard-onboarding-copy">
                        <strong>Choose Your Academic Interests</strong>
                        <p>You can select multiple fields and change them anytime. We’ll use your interests to help you discover relevant mentors.</p>
                    </div>
                    <div class="dashboard-onboarding-actions">
                        <a class="btn btn-outline-primary" href="account/profile.php">Choose Interests</a>
                        <a class="btn btn-primary" href="mentor/index.php">Find Relevant Mentors <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i></a>
                    </div>
                </section>
            <?php endif; ?>
            
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
                        <small class="text-muted">Total: <?php echo $total_courses; ?></small>
                    </div>
                </div>
                
                <div class="col-md-3 col-6">
                    <div class="dashboard-widget">
                        <div class="dashboard-widget-header">
                            <div class="dashboard-widget-icon dashboard-widget-icon-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span class="badge bg-success">Completed</span>
                        </div>
                        <div class="dashboard-widget-value"><?php echo $completed_courses; ?></div>
                        <div class="dashboard-widget-label">Completed Courses</div>
                        <small class="text-muted">🎉 Keep going!</small>
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
                        <div class="dashboard-widget-value"><?php echo $upcoming_sessions; ?></div>
                        <div class="dashboard-widget-label">Upcoming Sessions</div>
                        <small class="text-muted">Live learning</small>
                    </div>
                </div>
                
                <div class="col-md-3 col-6">
                    <div class="dashboard-widget">
                        <div class="dashboard-widget-header">
                            <div class="dashboard-widget-icon dashboard-widget-icon-danger">
                                <i class="fas fa-bell"></i>
                            </div>
                            <span class="badge bg-danger">New</span>
                        </div>
                        <div class="dashboard-widget-value"><?php echo $unread_notifications; ?></div>
                        <div class="dashboard-widget-label">Notifications</div>
                        <small class="text-muted"><?php echo $unread_messages; ?> unread messages</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Courses -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4 fresher-continue-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-clock text-primary"></i> Continue Learning</h5>
                                <a href="learning/my-course.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            
                            <?php if (!empty($recent_courses)): ?>
                                <?php foreach ($recent_courses as $course): ?>
                                <div class="course-progress-item mb-3">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars(getCourseThumbnail($course), ENT_QUOTES, 'UTF-8'); ?>"
                                             alt="<?php echo $course['title']; ?>" 
                                             class="rounded me-3" 
                                             style="width: 80px; height: 60px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($course['title']); ?></h6>
                                            <small class="text-muted">by <?php echo htmlspecialchars($course['mentor_name']); ?></small>
                                            <div class="mt-1">
                                                <div class="d-flex justify-content-between">
                                                    <span class="badge bg-<?php echo $course['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo ucfirst($course['status']); ?>
                                                    </span>
                                                    <small><?php echo $course['progress'] ?? 0; ?>%</small>
                                                </div>
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar" style="width: <?php echo $course['progress'] ?? 0; ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="learning/course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-primary ms-2">
                                            <i class="fas fa-play"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">You haven't enrolled in any courses yet.</p>
                                    <a href="../public/courses.php" class="btn btn-primary">Start Learning</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4 fresher-progress-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Your Learning Progress</h5>
                                <a href="learning/progress.php" class="btn btn-sm btn-outline-primary">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                            <div class="fresher-learning-progress">
                                <div class="fresher-progress-ring"><?php echo $total_courses > 0 ? (int) round(($completed_courses / $total_courses) * 100) : 0; ?>%</div>
                                <div>
                                    <small class="text-muted d-block mb-1"><i class="fas fa-arrow-down text-info me-2"></i>Courses Completed</small>
                                    <strong><?php echo $completed_courses; ?> / <?php echo $total_courses; ?></strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1"><i class="fas fa-shield-alt text-warning me-2"></i>Learning Streak</small>
                                    <strong><i class="fas fa-fire text-danger me-1"></i>0 days</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending Assignments -->
                    <?php if (!empty($pending_assignments)): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fas fa-tasks text-warning"></i> Pending Assignments</h5>
                            <?php foreach ($pending_assignments as $assignment): ?>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($assignment['course_title']); ?></small>
                                </div>
                                <div class="text-end">
                                    <small class="text-danger">Due: <?php echo formatDate($assignment['due_date']); ?></small>
                                    <a href="assignment/details.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary ms-2">
                                        View
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Right Sidebar -->
                <div class="col-lg-4">
                    <!-- Upcoming Sessions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-video text-primary"></i> Upcoming Sessions</h5>
                                <a href="sessions/my-session.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            
                            <?php if (!empty($upcoming)): ?>
                                <?php foreach ($upcoming as $session): ?>
                                <div class="session-item mb-3 p-2 bg-light rounded">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($session['title']); ?></h6>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($session['mentor_name']); ?>
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?>
                                    </small>
                                    <div class="mt-2">
                                        <?php if ($session['meeting_link']): ?>
                                            <a href="<?php echo $session['meeting_link']; ?>" target="_blank" class="btn btn-sm btn-success w-100">
                                                <i class="fas fa-video"></i> Join Session
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Meeting link pending</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-calendar-alt fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No upcoming sessions</p>
                                    <a href="sessions/index.php" class="btn btn-sm btn-primary">Book a Session</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Notifications -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-bell text-warning"></i> Recent Notifications</h5>
                                <a href="notification/index.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            
                            <?php if (!empty($recent_notifications)): ?>
                                <?php foreach ($recent_notifications as $notification): ?>
                                <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?> p-2 mb-1 rounded">
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i class="fas fa-<?php echo $notification['icon'] ?? 'bell'; ?> text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 small"><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <small class="text-muted"><?php echo getTimeAgo($notification['created_at']); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-bell fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No notifications</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>