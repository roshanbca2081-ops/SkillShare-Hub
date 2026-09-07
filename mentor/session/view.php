<?php
$page_title = 'Session Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$session_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT s.*, c.title as course_title,
                       u.full_name as mentor_name,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'approved') as approved_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'pending') as pending_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'completed') as completed_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'cancelled') as cancelled_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND attended = 1) as attended_count
                       FROM sessions s 
                       LEFT JOIN courses c ON s.course_id = c.id 
                       LEFT JOIN users u ON s.mentor_id = u.id
                       WHERE s.id = ? AND s.mentor_id = ?");
$stmt->execute([$session_id, $user_id]);
$session = $stmt->fetch();

if (!$session) {
    $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'Session not found.'];
    redirect('index.php');
}

// Handle inline booking actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_booking'])) {
        $bid = (int)$_POST['booking_id'];
        $pdo->prepare("UPDATE bookings SET status = 'approved' WHERE id = ? AND session_id = ?")->execute([$bid, $session_id]);
        $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Booking approved!'];
        redirect('view.php?id=' . $session_id . '#bookings');
    }
    if (isset($_POST['reject_booking'])) {
        $bid = (int)$_POST['booking_id'];
        $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND session_id = ?")->execute([$bid, $session_id]);
        $_SESSION['alert'] = ['type' => 'warning', 'icon' => 'times-circle', 'message' => 'Booking rejected.'];
        redirect('view.php?id=' . $session_id . '#bookings');
    }
    if (isset($_POST['mark_attended'])) {
        $bid = (int)$_POST['booking_id'];
        $attended = (int)$_POST['attended'];
        $pdo->prepare("UPDATE bookings SET attended = ? WHERE id = ? AND session_id = ?")->execute([$attended, $bid, $session_id]);
        redirect('view.php?id=' . $session_id . '#attendance');
    }
    if (isset($_POST['mark_all_attended'])) {
        $pdo->prepare("UPDATE bookings SET attended = 1 WHERE session_id = ? AND status = 'approved'")->execute([$session_id]);
        $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'All approved participants marked as attended.'];
        redirect('view.php?id=' . $session_id . '#attendance');
    }
    if (isset($_POST['complete_session'])) {
        $pdo->prepare("UPDATE sessions SET status = 'completed' WHERE id = ? AND mentor_id = ?")->execute([$session_id, $user_id]);
        $pdo->prepare("UPDATE bookings SET status = 'completed' WHERE session_id = ? AND status = 'approved'")->execute([$session_id]);
        $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Session marked as completed!'];
        redirect('view.php?id=' . $session_id);
    }
}

// Reload session after possible updates
$stmt = $pdo->prepare("SELECT s.*, c.title as course_title,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'approved') as approved_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'pending') as pending_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'completed') as completed_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'cancelled') as cancelled_count,
                       (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND attended = 1) as attended_count
                       FROM sessions s LEFT JOIN courses c ON s.course_id = c.id
                       WHERE s.id = ? AND s.mentor_id = ?");
$stmt->execute([$session_id, $user_id]);
$session = $stmt->fetch();

// Get bookings with student info
$stmt = $pdo->prepare("SELECT b.*, u.full_name, u.email, u.avatar, u.phone
                       FROM bookings b 
                       JOIN users u ON b.fresher_id = u.id 
                       WHERE b.session_id = ? 
                       ORDER BY FIELD(b.status,'pending','approved','cancelled','completed'), b.booking_date DESC");
$stmt->execute([$session_id]);
$bookings = $stmt->fetchAll();

// Get pending bookings for quick actions
$pending_bookings = array_filter($bookings, fn($b) => $b['status'] === 'pending');
$approved_bookings = array_filter($bookings, fn($b) => $b['status'] === 'approved');

// Status helpers
$badge_map = [
    'ongoing'   => 'success',
    'scheduled' => 'primary',
    'completed' => 'secondary',
    'cancelled' => 'danger',
];
$badge_color = $badge_map[$session['status']] ?? 'secondary';
$is_live = $session['status'] === 'ongoing';
$is_upcoming = $session['status'] === 'scheduled' && strtotime($session['scheduled_at']) > time();
$is_past = in_array($session['status'], ['completed', 'cancelled']);
$occupancy = $session['max_participants'] > 0 
    ? round(($session['approved_count'] / $session['max_participants']) * 100) : 0;
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center pt-2 pb-3 mb-4 border-bottom">
                <div>
                    <h1 class="h3 mb-1 fw-bold">Session Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Sessions</a></li>
                            <li class="breadcrumb-item active"><?php echo htmlspecialchars(mb_strimwidth($session['title'], 0, 40, '...')); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($is_live && $session['meeting_link']): ?>
                    <a href="<?php echo htmlspecialchars($session['meeting_link']); ?>" target="_blank" class="btn btn-success">
                        <i class="fas fa-video me-1"></i> Join Live
                    </a>
                    <?php endif; ?>
                    <?php if (!$is_past): ?>
                    <a href="edit.php?id=<?php echo $session_id; ?>" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            
            <?php if ($is_live): ?>
            <!-- Live Banner -->
            <div class="alert alert-success d-flex align-items-center gap-3 mb-4 py-3">
                <span class="live-pulse-green"></span>
                <div>
                    <strong>Session is LIVE!</strong>
                    <?php if ($session['meeting_link']): ?>
                        — <a href="<?php echo htmlspecialchars($session['meeting_link']); ?>" target="_blank" class="alert-link">
                            Click here to join <i class="fas fa-external-link-alt ms-1"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php if ($session['status'] !== 'completed'): ?>
                <form method="POST" class="ms-auto">
                    <button type="submit" name="complete_session" class="btn btn-sm btn-outline-success"
                            onclick="return confirm('Mark session as completed?')">
                        <i class="fas fa-flag-checkered me-1"></i> Mark Complete
                    </button>
                </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($is_upcoming && count($pending_bookings) > 0): ?>
            <!-- Pending Bookings Alert -->
            <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
                <i class="fas fa-clock fa-lg"></i>
                <div>
                    <strong><?php echo count($pending_bookings); ?> pending booking request(s)</strong> waiting for your approval.
                    <a href="#bookings" class="alert-link ms-1">Review below</a>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Session Info Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <?php if ($is_live): ?>
                        <div class="card-header bg-success text-white py-2 px-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="live-pulse"></span>
                                <strong class="small">LIVE SESSION</strong>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($session['title']); ?></h4>
                                    <?php if ($session['course_title']): ?>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-book me-1"></i>
                                        <?php echo htmlspecialchars($session['course_title']); ?>
                                    </p>
                                    <?php else: ?>
                                    <p class="text-muted mb-0"><i class="fas fa-calendar me-1"></i>Standalone Session</p>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-<?php echo $badge_color; ?> fs-6 px-3 py-2">
                                    <?php echo ucfirst($session['status']); ?>
                                </span>
                            </div>
                            
                            <!-- Key Details Grid -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-4 col-6">
                                    <div class="bg-light rounded p-3 h-100">
                                        <small class="text-muted d-block mb-1">Date & Time</small>
                                        <div class="fw-semibold small">
                                            <i class="fas fa-calendar-day text-primary me-1"></i>
                                            <?php echo formatDateTime($session['scheduled_at']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="bg-light rounded p-3 h-100">
                                        <small class="text-muted d-block mb-1">Duration</small>
                                        <div class="fw-semibold small">
                                            <i class="fas fa-hourglass text-primary me-1"></i>
                                            <?php echo $session['duration']; ?> minutes
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="bg-light rounded p-3 h-100">
                                        <small class="text-muted d-block mb-1">Price</small>
                                        <div class="fw-semibold small">
                                            <i class="fas fa-tag text-primary me-1"></i>
                                            <?php echo $session['is_free'] ? '<span class="text-success">Free</span>' : '$' . number_format($session['price'], 2); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="bg-light rounded p-3 h-100">
                                        <small class="text-muted d-block mb-1">Participants</small>
                                        <div class="fw-semibold small">
                                            <i class="fas fa-users text-primary me-1"></i>
                                            <?php echo $session['approved_count']; ?> / <?php echo $session['max_participants']; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="bg-light rounded p-3 h-100">
                                        <small class="text-muted d-block mb-1">Attended</small>
                                        <div class="fw-semibold small">
                                            <i class="fas fa-user-check text-primary me-1"></i>
                                            <?php echo $session['attended_count']; ?> students
                                        </div>
                                    </div>
                                </div>
                                <?php if ($session['meeting_id']): ?>
                                <div class="col-md-4 col-6">
                                    <div class="bg-light rounded p-3 h-100">
                                        <small class="text-muted d-block mb-1">Meeting ID</small>
                                        <div class="fw-semibold small text-truncate">
                                            <i class="fas fa-key text-primary me-1"></i>
                                            <?php echo htmlspecialchars($session['meeting_id']); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Occupancy Progress -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Capacity</small>
                                    <small class="fw-semibold"><?php echo $occupancy; ?>%</small>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 8px;">
                                    <div class="progress-bar bg-<?php echo $occupancy >= 90 ? 'danger' : ($occupancy >= 60 ? 'warning' : 'success'); ?>" 
                                         style="width: <?php echo min($occupancy, 100); ?>%;"></div>
                                </div>
                            </div>
                            
                            <?php if ($session['description']): ?>
                            <hr>
                            <div>
                                <h6 class="fw-semibold mb-2">Description</h6>
                                <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($session['description'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($session['meeting_link']): ?>
                            <hr>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <h6 class="fw-semibold mb-0">Meeting Link:</h6>
                                <a href="<?php echo htmlspecialchars($session['meeting_link']); ?>" target="_blank" 
                                   class="btn btn-success">
                                    <i class="fas fa-video me-2"></i> Join Session
                                </a>
                                <button type="button" onclick="copyText('<?php echo addslashes($session['meeting_link']); ?>')" 
                                        class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-copy me-1"></i> Copy Link
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Bookings -->
                    <div class="card border-0 shadow-sm mb-4" id="bookings">
                        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-calendar-check me-2 text-primary"></i>Bookings
                                <span class="badge bg-secondary ms-1"><?php echo count($bookings); ?></span>
                            </h6>
                            <?php if (!empty($pending_bookings)): ?>
                            <span class="badge bg-warning text-dark">
                                <?php echo count($pending_bookings); ?> awaiting approval
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($bookings)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Student</th>
                                            <th>Status</th>
                                            <th class="d-none d-md-table-cell">Booked On</th>
                                            <th class="text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                        <tr class="<?php echo $booking['status'] === 'pending' ? 'table-warning' : ''; ?>">
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="<?php echo getAvatar($booking); ?>" 
                                                         class="rounded-circle flex-shrink-0" 
                                                         style="width:36px;height:36px;object-fit:cover"
                                                         alt="<?php echo htmlspecialchars($booking['full_name']); ?>">
                                                    <div>
                                                        <div class="fw-semibold small"><?php echo htmlspecialchars($booking['full_name']); ?></div>
                                                        <div class="text-muted" style="font-size:.75rem"><?php echo htmlspecialchars($booking['email']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $bstatus_map = ['pending'=>'warning text-dark','approved'=>'success','completed'=>'primary','cancelled'=>'danger'];
                                                $bstatus_color = $bstatus_map[$booking['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $bstatus_color; ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                                <?php if ($booking['attended']): ?>
                                                    <i class="fas fa-check-circle text-success ms-1" title="Attended"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <small class="text-muted"><?php echo formatDate($booking['booking_date']); ?></small>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                    <?php if ($booking['status'] === 'pending'): ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                        <button type="submit" name="approve_booking" 
                                                                class="btn btn-sm btn-success" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                        <button type="submit" name="reject_booking" 
                                                                class="btn btn-sm btn-danger" title="Reject"
                                                                onclick="return confirm('Reject this booking?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                    <a href="../booking/view.php?id=<?php echo $booking['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="View details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No bookings yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Attendance -->
                    <div class="card border-0 shadow-sm" id="attendance">
                        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-user-check me-2 text-primary"></i>Attendance
                                <span class="badge bg-secondary ms-1"><?php echo $session['attended_count']; ?> / <?php echo $session['approved_count']; ?></span>
                            </h6>
                            <?php if (!empty($approved_bookings) && !$is_past): ?>
                            <form method="POST">
                                <button type="submit" name="mark_all_attended" class="btn btn-sm btn-outline-success"
                                        onclick="return confirm('Mark ALL approved students as attended?')">
                                    <i class="fas fa-check-double me-1"></i> Mark All Present
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($approved_bookings)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Student</th>
                                            <th class="d-none d-md-table-cell">Booked</th>
                                            <th>Attended</th>
                                            <th class="text-end pe-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($approved_bookings as $booking): ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="<?php echo getAvatar($booking); ?>" 
                                                         class="rounded-circle flex-shrink-0" 
                                                         style="width:32px;height:32px;object-fit:cover" alt="">
                                                    <div>
                                                        <div class="fw-semibold small"><?php echo htmlspecialchars($booking['full_name']); ?></div>
                                                        <div class="text-muted" style="font-size:.75rem"><?php echo htmlspecialchars($booking['email']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <small class="text-muted"><?php echo formatDate($booking['booking_date']); ?></small>
                                            </td>
                                            <td>
                                                <?php if ($booking['attended']): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Present
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark border">Absent</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-3">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                    <?php if ($booking['attended']): ?>
                                                        <input type="hidden" name="attended" value="0">
                                                        <button type="submit" name="mark_attended" 
                                                                class="btn btn-sm btn-outline-warning" title="Mark absent">
                                                            <i class="fas fa-user-times me-1"></i> Unmark
                                                        </button>
                                                    <?php else: ?>
                                                        <input type="hidden" name="attended" value="1">
                                                        <button type="submit" name="mark_attended" 
                                                                class="btn btn-sm btn-success" title="Mark attended">
                                                            <i class="fas fa-user-check me-1"></i> Mark Present
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-user-clock fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No approved bookings yet.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 80px;">
                        <!-- Quick Stats -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-chart-pie text-primary me-2"></i>Session Stats
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Total Bookings</span>
                                    <strong><?php echo count($bookings); ?></strong>
                                </div>
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i>Approved</span>
                                    <span class="badge bg-success"><?php echo $session['approved_count']; ?></span>
                                </div>
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><i class="fas fa-clock text-warning me-1"></i>Pending</span>
                                    <span class="badge bg-warning text-dark"><?php echo $session['pending_count']; ?></span>
                                </div>
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><i class="fas fa-times-circle text-danger me-1"></i>Cancelled</span>
                                    <span class="badge bg-danger"><?php echo $session['cancelled_count']; ?></span>
                                </div>
                                <div class="p-3 d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><i class="fas fa-user-check text-info me-1"></i>Attended</span>
                                    <span class="badge bg-info"><?php echo $session['attended_count']; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-grid gap-2">
                                    <?php if ($session['meeting_link']): ?>
                                    <a href="<?php echo htmlspecialchars($session['meeting_link']); ?>" 
                                       target="_blank" class="btn btn-success">
                                        <i class="fas fa-video me-2"></i>Join Session
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!$is_past): ?>
                                    <a href="edit.php?id=<?php echo $session_id; ?>" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>Edit Session
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($is_live): ?>
                                    <form method="POST">
                                        <button type="submit" name="complete_session" class="btn btn-outline-secondary w-100"
                                                onclick="return confirm('Mark this session as completed?')">
                                            <i class="fas fa-flag-checkered me-2"></i>Mark as Completed
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($session['status'] !== 'cancelled' && $session['status'] !== 'completed'): ?>
                                    <a href="cancel.php?id=<?php echo $session_id; ?>" class="btn btn-outline-danger"
                                       onclick="return confirm('Cancel this session? All bookings will be cancelled.')">
                                        <i class="fas fa-ban me-2"></i>Cancel Session
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="create.php" class="btn btn-outline-primary">
                                        <i class="fas fa-plus me-2"></i>New Session
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Session Timeline -->
                        <?php if ($is_upcoming): ?>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="text-primary mb-2"><i class="fas fa-clock fa-2x"></i></div>
                                <h6 class="fw-semibold">Session starts in</h6>
                                <div class="countdown-display" data-time="<?php echo strtotime($session['scheduled_at']); ?>">
                                    <div class="row g-2 text-center mt-2">
                                        <div class="col-3">
                                            <div class="bg-primary text-white rounded p-2">
                                                <div class="fw-bold fs-5 countdown-days">--</div>
                                                <div style="font-size:.65rem">DAYS</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="bg-primary text-white rounded p-2">
                                                <div class="fw-bold fs-5 countdown-hours">--</div>
                                                <div style="font-size:.65rem">HRS</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="bg-primary text-white rounded p-2">
                                                <div class="fw-bold fs-5 countdown-mins">--</div>
                                                <div style="font-size:.65rem">MIN</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="bg-primary text-white rounded p-2">
                                                <div class="fw-bold fs-5 countdown-secs">--</div>
                                                <div style="font-size:.65rem">SEC</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.live-pulse {
    display: inline-block; width: 10px; height: 10px; border-radius: 50%;
    background: #fff; box-shadow: 0 0 0 0 rgba(255,255,255,.7);
    animation: pulse 1.5s infinite;
}
.live-pulse-green {
    display: inline-block; width: 14px; height: 14px; border-radius: 50%;
    background: #198754; box-shadow: 0 0 0 0 rgba(25,135,84,.7);
    animation: pulse 1.5s infinite; flex-shrink: 0;
}
@keyframes pulse {
    0%   { box-shadow: 0 0 0 0 rgba(255,255,255,.7); }
    70%  { box-shadow: 0 0 0 8px rgba(255,255,255,0); }
    100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
}
.progress { border-radius: 10px; }
</style>

<script>
// Copy helper
function copyText(text) {
    navigator.clipboard.writeText(text).then(function() {
        var btn = event.target.closest('button');
        var original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-success me-1"></i> Copied!';
        setTimeout(function() { btn.innerHTML = original; }, 2000);
    });
}

// Countdown timer
var countdownEl = document.querySelector('.countdown-display');
if (countdownEl) {
    var target = parseInt(countdownEl.dataset.time) * 1000;
    function updateCountdown() {
        var diff = target - Date.now();
        if (diff <= 0) {
            countdownEl.innerHTML = '<div class="alert alert-success mt-2">Session is starting now!</div>';
            return;
        }
        var days = Math.floor(diff / 86400000);
        var hours = Math.floor((diff % 86400000) / 3600000);
        var mins = Math.floor((diff % 3600000) / 60000);
        var secs = Math.floor((diff % 60000) / 1000);
        countdownEl.querySelector('.countdown-days').textContent = String(days).padStart(2,'0');
        countdownEl.querySelector('.countdown-hours').textContent = String(hours).padStart(2,'0');
        countdownEl.querySelector('.countdown-mins').textContent = String(mins).padStart(2,'0');
        countdownEl.querySelector('.countdown-secs').textContent = String(secs).padStart(2,'0');
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
}
</script>

<?php include '../../includes/footer.php'; ?>