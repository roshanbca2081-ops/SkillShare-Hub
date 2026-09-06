<?php
$page_title = 'My Sessions';
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';
require_once '../config/auth.php';

requireFresher();

$user_id = getUserId();

// Upcoming sessions
$stmt = $pdo->prepare("SELECT b.*, s.id as session_id, s.title, s.description, s.scheduled_at, s.duration, s.meeting_link, 
                       u.full_name as mentor_name, u.id as mentor_id
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON s.mentor_id = u.id 
                       WHERE b.fresher_id = ? AND b.status = 'approved' AND s.scheduled_at > NOW()
                       ORDER BY s.scheduled_at");
$stmt->execute([$user_id]);
$upcoming = $stmt->fetchAll();

// Past sessions
$stmt = $pdo->prepare("SELECT b.*, s.id as session_id, s.title, s.description, s.scheduled_at, s.duration, s.recording_url,
                       u.full_name as mentor_name
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON s.mentor_id = u.id 
                       WHERE b.fresher_id = ? AND (b.status = 'completed' OR s.scheduled_at < NOW())
                       ORDER BY s.scheduled_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$past = $stmt->fetchAll();

// Pending bookings
$stmt = $pdo->prepare("SELECT b.*, s.title, s.scheduled_at, u.full_name as mentor_name 
                       FROM bookings b 
                       JOIN sessions s ON b.session_id = s.id 
                       JOIN users u ON s.mentor_id = u.id 
                       WHERE b.fresher_id = ? AND b.status = 'pending'
                       ORDER BY b.booking_date DESC");
$stmt->execute([$user_id]);
$pending = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Sessions</h1>
                <a href="sessions/index.php" class="btn btn-primary"><i class="fas fa-plus"></i> Book a Session</a>
            </div>
            
            <!-- Pending -->
            <?php if (!empty($pending)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5>Pending Requests</h5>
                    <?php foreach ($pending as $booking): ?>
                    <div class="alert alert-warning d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($booking['title']); ?></strong>
                            <br><small>with <?php echo htmlspecialchars($booking['mentor_name']); ?> - <?php echo formatDateTime($booking['scheduled_at']); ?></small>
                        </div>
                        <span class="badge bg-warning">Pending</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Upcoming -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5>Upcoming Sessions</h5>
                    <?php if (!empty($upcoming)): ?>
                        <div class="list-group">
                            <?php foreach ($upcoming as $session): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($session['title']); ?></h6>
                                        <small class="text-muted">with <?php echo htmlspecialchars($session['mentor_name']); ?></small>
                                        <br><small><i class="fas fa-calendar"></i> <?php echo formatDateTime($session['scheduled_at']); ?></small>
                                        <br><small><i class="fas fa-clock"></i> <?php echo $session['duration']; ?> minutes</small>
                                    </div>
                                    <div>
                                        <?php if ($session['meeting_link']): ?>
                                            <a href="<?php echo $session['meeting_link']; ?>" target="_blank" class="btn btn-success btn-sm">
                                                <i class="fas fa-video"></i> Join
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No upcoming sessions.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Past Sessions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5>Past Sessions</h5>
                    <?php if (!empty($past)): ?>
                        <div class="list-group">
                            <?php foreach ($past as $session): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($session['title']); ?></h6>
                                        <small class="text-muted">with <?php echo htmlspecialchars($session['mentor_name']); ?></small>
                                        <br><small class="text-muted"><?php echo formatDateTime($session['scheduled_at']); ?></small>
                                    </div>
                                    <div>
                                        <span class="badge bg-secondary">Completed</span>
                                        <?php if ($session['recording_url']): ?>
                                            <a href="<?php echo $session['recording_url']; ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-play"></i> Recording
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No past sessions.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>