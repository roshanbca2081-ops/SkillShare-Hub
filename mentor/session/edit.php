<?php
$page_title = 'Edit Session';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$session_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = ? AND mentor_id = ?");
$stmt->execute([$session_id, $user_id]);
$session = $stmt->fetch();

if (!$session) {
    $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'Session not found.'];
    redirect('index.php');
}

// Get mentor's active courses
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE mentor_id = ? AND status = 'active' ORDER BY title");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

// Get booking stats
$stmt = $pdo->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM bookings WHERE session_id = ?");
$stmt->execute([$session_id]);
$stats = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_session'])) {
    $title        = sanitize($_POST['title']);
    $description  = sanitize($_POST['description']);
    $course_id    = isset($_POST['course_id']) && $_POST['course_id'] !== '' ? (int)$_POST['course_id'] : null;
    $scheduled_at = $_POST['scheduled_at'];
    $duration     = (int)$_POST['duration'];
    $max_participants = (int)$_POST['max_participants'];
    $meeting_link = sanitize($_POST['meeting_link']);
    $meeting_id   = sanitize($_POST['meeting_id']);
    $is_free      = isset($_POST['is_free']) ? 1 : 0;
    $price        = $is_free ? 0.00 : (float)$_POST['price'];
    $status       = sanitize($_POST['status']);
    
    $errors = [];
    if (strlen($title) < 5) $errors[] = 'Title must be at least 5 characters.';
    if (empty($scheduled_at)) $errors[] = 'Please select a date and time.';
    if ($duration < 15) $errors[] = 'Duration must be at least 15 minutes.';
    if ($max_participants < 1) $errors[] = 'Max participants must be at least 1.';
    if ($max_participants < $stats['approved']) {
        $errors[] = 'Max participants cannot be less than current approved bookings (' . $stats['approved'] . ').';
    }
    if (!empty($meeting_link) && !filter_var($meeting_link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Please enter a valid meeting URL.';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE sessions SET 
            title = ?, description = ?, course_id = ?, scheduled_at = ?, 
            duration = ?, max_participants = ?, meeting_link = ?, meeting_id = ?, 
            is_free = ?, price = ?, status = ? 
            WHERE id = ? AND mentor_id = ?");
        $stmt->execute([
            $title, $description, $course_id, $scheduled_at, $duration, 
            $max_participants, $meeting_link, $meeting_id, $is_free, $price, 
            $status, $session_id, $user_id
        ]);
        
        // If cancelled, cancel all pending bookings
        if ($status === 'cancelled' && $session['status'] !== 'cancelled') {
            $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE session_id = ? AND status IN ('pending','approved')")
                ->execute([$session_id]);
        }
        
        // Reload session data
        $stmt = $pdo->prepare("SELECT * FROM sessions WHERE id = ?");
        $stmt->execute([$session_id]);
        $session = $stmt->fetch();
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Session updated successfully!'
        ];
        redirect('edit.php?id=' . $session_id);
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
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center pt-2 pb-3 mb-4 border-bottom">
                <div>
                    <h1 class="h3 mb-1 fw-bold">Edit Session</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Sessions</a></li>
                            <li class="breadcrumb-item"><a href="view.php?id=<?php echo $session_id; ?>" class="text-decoration-none"><?php echo htmlspecialchars(mb_strimwidth($session['title'], 0, 30, '...')); ?></a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="view.php?id=<?php echo $session_id; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> View
                    </a>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
            
            <!-- Status Alert for Cancelled -->
            <?php if ($session['status'] === 'cancelled'): ?>
            <div class="alert alert-warning d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>This session is <strong>cancelled</strong>. You can still edit details or change its status.</div>
            </div>
            <?php endif; ?>
            
            <div class="row g-4">
                <!-- Form -->
                <div class="col-lg-8">
                    <form method="POST" id="editSessionForm" novalidate>
                        <!-- Basic Info -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-info-circle text-primary me-2"></i>Basic Information
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Session Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" 
                                           value="<?php echo htmlspecialchars($session['title']); ?>" required maxlength="150">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Description</label>
                                    <textarea name="description" class="form-control" rows="4" maxlength="2000"><?php echo htmlspecialchars($session['description']); ?></textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-medium">Linked Course (Optional)</label>
                                    <select name="course_id" class="form-select">
                                        <option value="">— No Course —</option>
                                        <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>" 
                                                <?php echo $session['course_id'] == $course['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>Schedule & Capacity
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="scheduled_at" class="form-control" 
                                               value="<?php echo date('Y-m-d\TH:i', strtotime($session['scheduled_at'])); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Duration <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="duration" class="form-control" 
                                                   value="<?php echo $session['duration']; ?>" min="15" max="480" step="15" required>
                                            <span class="input-group-text">minutes</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Max Participants <span class="text-danger">*</span></label>
                                        <input type="number" name="max_participants" class="form-control" 
                                               value="<?php echo $session['max_participants']; ?>" 
                                               min="<?php echo max(1, $stats['approved']); ?>" max="500" required>
                                        <?php if ($stats['approved'] > 0): ?>
                                        <div class="form-text text-info">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Minimum <?php echo $stats['approved']; ?> (current approved bookings)
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Session Status</label>
                                        <select name="status" class="form-select" id="statusSelect">
                                            <option value="scheduled" <?php echo $session['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                            <option value="ongoing" <?php echo $session['status'] === 'ongoing' ? 'selected' : ''; ?>>Ongoing (Live)</option>
                                            <option value="completed" <?php echo $session['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $session['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Meeting Details -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-video text-primary me-2"></i>Meeting Details
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Meeting Link</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                        <input type="url" name="meeting_link" class="form-control" 
                                               value="<?php echo htmlspecialchars($session['meeting_link']); ?>"
                                               placeholder="https://...">
                                        <?php if ($session['meeting_link']): ?>
                                        <a href="<?php echo htmlspecialchars($session['meeting_link']); ?>" 
                                           target="_blank" class="btn btn-outline-success" title="Test link">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-medium">Meeting ID</label>
                                    <div class="input-group">
                                        <input type="text" name="meeting_id" class="form-control" id="meetingId"
                                               value="<?php echo htmlspecialchars($session['meeting_id']); ?>">
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyMeetingId()" title="Copy">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent py-3">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-tag text-primary me-2"></i>Pricing
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_free" id="isFree" 
                                               <?php echo $session['is_free'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-medium" for="isFree">Free session</label>
                                    </div>
                                </div>
                                <div id="priceField" <?php echo $session['is_free'] ? 'style="display:none"' : ''; ?>>
                                    <label class="form-label fw-medium">Price</label>
                                    <div class="input-group" style="max-width: 200px;">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" class="form-control" 
                                               value="<?php echo $session['price']; ?>" step="0.01" min="0" max="9999">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Warning if cancelling with bookings -->
                        <div id="cancelWarning" class="alert alert-danger d-none mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> Changing status to "Cancelled" will cancel all pending and approved bookings, 
                            and notify <?php echo $stats['approved'] + $stats['pending']; ?> students.
                        </div>
                        
                        <!-- Submit -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" name="update_session" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                            <a href="view.php?id=<?php echo $session_id; ?>" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-times me-2"></i> Discard
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Sidebar Stats -->
                <div class="col-lg-4">
                    <!-- Session Stats -->
                    <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 80px;">
                        <div class="card-header bg-transparent py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-chart-bar text-primary me-2"></i>Session Statistics
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Total Bookings</span>
                                <strong><?php echo $stats['total']; ?></strong>
                            </div>
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i>Approved</span>
                                <span class="badge bg-success"><?php echo $stats['approved']; ?></span>
                            </div>
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fas fa-clock text-warning me-1"></i>Pending</span>
                                <span class="badge bg-warning text-dark"><?php echo $stats['pending']; ?></span>
                            </div>
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fas fa-times-circle text-danger me-1"></i>Cancelled</span>
                                <span class="badge bg-danger"><?php echo $stats['cancelled']; ?></span>
                            </div>
                            <div class="p-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fas fa-calendar me-1"></i>Created</span>
                                <small><?php echo formatDate($session['created_at']); ?></small>
                            </div>
                        </div>
                        
                        <?php if ($session['status'] !== 'cancelled'): ?>
                        <div class="card-footer bg-transparent">
                            <div class="d-grid gap-2">
                                <?php if ($session['meeting_link']): ?>
                                <a href="<?php echo htmlspecialchars($session['meeting_link']); ?>" 
                                   target="_blank" class="btn btn-success btn-sm">
                                    <i class="fas fa-video me-1"></i> Join Session
                                </a>
                                <?php endif; ?>
                                <a href="view.php?id=<?php echo $session_id; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-users me-1"></i> Manage Bookings
                                </a>
                                <?php if ($session['status'] !== 'cancelled'): ?>
                                <a href="cancel.php?id=<?php echo $session_id; ?>" class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Cancel this session? All bookings will be cancelled.')">
                                    <i class="fas fa-ban me-1"></i> Cancel Session
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Free toggle
document.getElementById('isFree').addEventListener('change', function() {
    document.getElementById('priceField').style.display = this.checked ? 'none' : '';
    if (this.checked) document.querySelector('input[name="price"]').value = '0.00';
});

// Status change warning
document.getElementById('statusSelect').addEventListener('change', function() {
    var warning = document.getElementById('cancelWarning');
    if (this.value === 'cancelled' && '<?php echo $session['status']; ?>' !== 'cancelled') {
        warning.classList.remove('d-none');
    } else {
        warning.classList.add('d-none');
    }
});

// Copy meeting ID
function copyMeetingId() {
    var input = document.getElementById('meetingId');
    input.select();
    document.execCommand('copy');
    var btn = input.nextElementSibling;
    var original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check text-success"></i>';
    setTimeout(function() { btn.innerHTML = original; }, 1500);
}
</script>

<?php include '../../includes/footer.php'; ?>