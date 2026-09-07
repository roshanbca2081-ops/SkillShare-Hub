<?php
$page_title = 'My Sessions';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : 'upcoming';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "SELECT s.*, 
          (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'approved') as booked_count,
          (SELECT COUNT(*) FROM bookings WHERE session_id = s.id AND status = 'pending') as pending_count,
          c.title as course_title
          FROM sessions s 
          LEFT JOIN courses c ON s.course_id = c.id 
          WHERE s.mentor_id = ?";

$params = [$user_id];

if ($filter === 'upcoming') {
    $query .= " AND s.scheduled_at > NOW() AND s.status IN ('scheduled', 'ongoing')";
} elseif ($filter === 'past') {
    $query .= " AND (s.scheduled_at < NOW() OR s.status IN ('completed', 'cancelled'))";
} elseif ($filter === 'ongoing') {
    $query .= " AND s.status = 'ongoing'";
} elseif ($filter === 'cancelled') {
    $query .= " AND s.status = 'cancelled'";
}

if (!empty($search)) {
    $query .= " AND (s.title LIKE ? OR s.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY s.scheduled_at " . ($filter === 'upcoming' ? 'ASC' : 'DESC');

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sessions = $stmt->fetchAll();

// Get counts for tabs
$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND scheduled_at > NOW() AND status IN ('scheduled', 'ongoing')");
$stmt->execute([$user_id]);
$upcoming_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND status = 'ongoing'");
$stmt->execute([$user_id]);
$ongoing_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND (scheduled_at < NOW() OR status IN ('completed', 'cancelled'))");
$stmt->execute([$user_id]);
$past_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND status = 'cancelled'");
$stmt->execute([$user_id]);
$cancelled_count = $stmt->fetchColumn();

// Total sessions
$stmt = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE mentor_id = ?");
$stmt->execute([$user_id]);
$total_sessions = $stmt->fetchColumn();

// Total participants
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT b.fresher_id) FROM bookings b JOIN sessions s ON b.session_id = s.id WHERE s.mentor_id = ? AND b.status = 'approved'");
$stmt->execute([$user_id]);
$total_participants = $stmt->fetchColumn();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0 collapse" id="sidebarMenu">
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center pt-2 pb-3 mb-4 border-bottom">
                <div>
                    <h1 class="h3 mb-1 fw-bold">My Sessions</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active">Sessions</li>
                        </ol>
                    </nav>
                </div>
                <a href="create.php" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Schedule Session
                </a>
            </div>
            
            <!-- Stats Overview -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3 px-3 text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:44px;height:44px">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </div>
                            <h4 class="fw-bold mb-0 text-primary"><?php echo $total_sessions; ?></h4>
                            <small class="text-muted">Total Sessions</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3 px-3 text-center">
                            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:44px;height:44px">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <h4 class="fw-bold mb-0 text-success"><?php echo $upcoming_count; ?></h4>
                            <small class="text-muted">Upcoming</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3 px-3 text-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:44px;height:44px">
                                <i class="fas fa-bolt text-warning"></i>
                            </div>
                            <h4 class="fw-bold mb-0 text-warning"><?php echo $ongoing_count; ?></h4>
                            <small class="text-muted">Live Now</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body py-3 px-3 text-center">
                            <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:44px;height:44px">
                                <i class="fas fa-users text-info"></i>
                            </div>
                            <h4 class="fw-bold mb-0 text-info"><?php echo $total_participants; ?></h4>
                            <small class="text-muted">Total Participants</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Search + Filter Row -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form class="row g-2 align-items-center" method="GET">
                        <div class="col-12 col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" 
                                       placeholder="Search sessions..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <select name="filter" class="form-select" onchange="this.form.submit()">
                                <option value="upcoming" <?php echo $filter === 'upcoming' ? 'selected' : ''; ?>>Upcoming Sessions</option>
                                <option value="ongoing" <?php echo $filter === 'ongoing' ? 'selected' : ''; ?>>Live / Ongoing</option>
                                <option value="past" <?php echo $filter === 'past' ? 'selected' : ''; ?>>Past Sessions</option>
                                <option value="cancelled" <?php echo $filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Sessions</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <?php if (!empty($search)): ?>
                                <a href="?filter=<?php echo $filter; ?>" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'upcoming' ? 'active' : ''; ?>" href="?filter=upcoming">
                        <i class="fas fa-clock me-1"></i> Upcoming
                        <span class="badge bg-primary ms-1"><?php echo $upcoming_count; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'ongoing' ? 'active' : ''; ?>" href="?filter=ongoing">
                        <i class="fas fa-bolt me-1 text-warning"></i> Live
                        <?php if ($ongoing_count > 0): ?>
                            <span class="badge bg-success ms-1"><?php echo $ongoing_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'past' ? 'active' : ''; ?>" href="?filter=past">
                        <i class="fas fa-history me-1"></i> Past
                        <span class="badge bg-secondary ms-1"><?php echo $past_count; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $filter === 'cancelled' ? 'active' : ''; ?>" href="?filter=cancelled">
                        <i class="fas fa-ban me-1"></i> Cancelled
                        <?php if ($cancelled_count > 0): ?>
                            <span class="badge bg-danger ms-1"><?php echo $cancelled_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
            
            <!-- Sessions List -->
            <?php if (!empty($sessions)): ?>
                <div class="row g-4">
                    <?php foreach ($sessions as $session): 
                        $is_live = $session['status'] === 'ongoing';
                        $is_upcoming = $session['status'] === 'scheduled' && strtotime($session['scheduled_at']) > time();
                        $is_past = in_array($session['status'], ['completed', 'cancelled']) || strtotime($session['scheduled_at']) < time();
                        
                        // Status badge color
                        $badge_map = [
                            'ongoing'   => 'success',
                            'scheduled' => 'primary',
                            'completed' => 'secondary',
                            'cancelled' => 'danger',
                        ];
                        $badge_color = $badge_map[$session['status']] ?? 'secondary';
                        
                        // Occupancy %
                        $occupancy = $session['max_participants'] > 0 
                            ? round(($session['booked_count'] / $session['max_participants']) * 100) 
                            : 0;
                    ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-sm session-card <?php echo $is_live ? 'border-success border-2' : ''; ?>" 
                             style="transition: transform .2s, box-shadow .2s;">
                            
                            <?php if ($is_live): ?>
                            <div class="card-header bg-success text-white py-2 px-3 d-flex align-items-center gap-2">
                                <span class="live-pulse"></span>
                                <strong class="small">LIVE NOW</strong>
                                <?php if ($session['meeting_link']): ?>
                                    <a href="<?php echo htmlspecialchars($session['meeting_link']); ?>" 
                                       target="_blank" class="btn btn-sm btn-light ms-auto py-0 px-2">
                                        <i class="fas fa-video me-1"></i>Join
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1 me-2">
                                        <h6 class="mb-1 fw-semibold" title="<?php echo htmlspecialchars($session['title']); ?>">
                                            <?php echo htmlspecialchars(mb_strimwidth($session['title'], 0, 50, '...')); ?>
                                        </h6>
                                        <?php if ($session['course_title']): ?>
                                            <p class="text-muted mb-0 small">
                                                <i class="fas fa-book me-1"></i><?php echo htmlspecialchars(mb_strimwidth($session['course_title'], 0, 35, '...')); ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="text-muted mb-0 small"><i class="fas fa-calendar me-1"></i>Standalone Session</p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-<?php echo $badge_color; ?> flex-shrink-0">
                                        <?php echo ucfirst($session['status']); ?>
                                    </span>
                                </div>

                                <!-- Date/Time Info -->
                                <div class="bg-light rounded p-2 mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fas fa-calendar-day text-primary me-2 fa-fw"></i>
                                        <span class="small"><?php echo formatDateTime($session['scheduled_at']); ?></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-hourglass-half text-primary me-2 fa-fw"></i>
                                        <span class="small"><?php echo $session['duration']; ?> minutes</span>
                                        <?php if ($session['is_free']): ?>
                                            <span class="badge bg-success ms-auto">Free</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark ms-auto">$<?php echo number_format($session['price'], 0); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Occupancy Bar -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i>
                                            <?php echo $session['booked_count']; ?>/<?php echo $session['max_participants']; ?> booked
                                        </small>
                                        <?php if ($session['pending_count'] > 0): ?>
                                            <small class="text-warning">
                                                <i class="fas fa-clock me-1"></i><?php echo $session['pending_count']; ?> pending
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-<?php echo $occupancy >= 90 ? 'danger' : ($occupancy >= 60 ? 'warning' : 'success'); ?>" 
                                             style="width: <?php echo min($occupancy, 100); ?>%"></div>
                                    </div>
                                </div>
                                
                                <!-- Countdown for upcoming -->
                                <?php if ($is_upcoming): ?>
                                    <div class="countdown-timer text-center py-2 bg-primary bg-opacity-10 rounded mb-2" 
                                         data-time="<?php echo strtotime($session['scheduled_at']); ?>">
                                        <small class="text-primary fw-semibold">
                                            <i class="fas fa-clock me-1"></i>
                                            <span class="countdown-text">Loading...</span>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer bg-transparent pt-0 border-top-0 pb-3 px-3">
                                <div class="d-grid gap-2">
                                    <a href="view.php?id=<?php echo $session['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                    <div class="d-flex gap-2">
                                        <?php if ($session['status'] !== 'cancelled' && $session['status'] !== 'completed'): ?>
                                            <a href="edit.php?id=<?php echo $session['id']; ?>" class="btn btn-sm btn-warning flex-grow-1">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($session['status'] !== 'cancelled'): ?>
                                            <a href="cancel.php?id=<?php echo $session['id']; ?>" 
                                               class="btn btn-sm btn-danger <?php echo ($session['status'] !== 'cancelled' && $session['status'] !== 'completed') ? '' : 'flex-grow-1'; ?>"
                                               onclick="return confirm('Are you sure you want to cancel this session? All bookings will be cancelled and students notified.')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($session['meeting_link'] && ($is_live || $is_upcoming)): ?>
                                            <a href="<?php echo htmlspecialchars($session['meeting_link']); ?>" 
                                               target="_blank" class="btn btn-sm btn-success">
                                                <i class="fas fa-video"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-4" 
                         style="width: 100px; height: 100px;">
                        <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">No sessions found</h5>
                    <?php if (!empty($search)): ?>
                        <p class="text-muted">No sessions match your search "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
                        <a href="?filter=<?php echo $filter; ?>" class="btn btn-outline-secondary me-2">Clear Search</a>
                    <?php else: ?>
                        <p class="text-muted">You haven't scheduled any <?php echo $filter !== 'all' ? $filter : ''; ?> sessions yet.</p>
                    <?php endif; ?>
                    <a href="create.php" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-1"></i> Schedule Your First Session
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Live Pulse Animation */
.live-pulse {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 0 0 0 rgba(255,255,255,0.7);
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0%   { box-shadow: 0 0 0 0 rgba(255,255,255,0.7); }
    70%  { box-shadow: 0 0 0 8px rgba(255,255,255,0); }
    100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
}

/* Session card hover */
.session-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
}

/* Nav tabs */
.nav-tabs .nav-link {
    color: #6c757d;
    font-size: 0.875rem;
}
.nav-tabs .nav-link.active {
    font-weight: 600;
}

/* Progress bar */
.progress { border-radius: 10px; }

/* Responsive adjustments */
@media (max-width: 576px) {
    .nav-tabs .nav-link {
        padding: .4rem .6rem;
        font-size: .8rem;
    }
}
</style>

<script>
// Countdown timers
document.querySelectorAll('.countdown-timer').forEach(function(el) {
    var target = parseInt(el.dataset.time) * 1000;
    var textEl = el.querySelector('.countdown-text');
    
    function update() {
        var now = Date.now();
        var diff = target - now;
        
        if (diff <= 0) {
            textEl.textContent = 'Starting now!';
            el.classList.add('bg-success', 'bg-opacity-10');
            el.classList.remove('bg-primary', 'bg-opacity-10');
            return;
        }
        
        var days = Math.floor(diff / 86400000);
        var hours = Math.floor((diff % 86400000) / 3600000);
        var mins = Math.floor((diff % 3600000) / 60000);
        var secs = Math.floor((diff % 60000) / 1000);
        
        if (days > 0) {
            textEl.textContent = 'Starts in ' + days + 'd ' + hours + 'h ' + mins + 'm';
        } else if (hours > 0) {
            textEl.textContent = 'Starts in ' + hours + 'h ' + mins + 'm ' + secs + 's';
        } else {
            textEl.textContent = 'Starts in ' + mins + 'm ' + secs + 's';
        }
    }
    
    update();
    setInterval(update, 1000);
});
</script>

<?php include '../../includes/footer.php'; ?>