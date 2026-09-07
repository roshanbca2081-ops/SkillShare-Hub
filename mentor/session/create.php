<?php
$page_title = 'Schedule Session';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;

// Get mentor's active courses
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE mentor_id = ? AND status = 'active' ORDER BY title");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_session'])) {
    $title        = sanitize($_POST['title']);
    $description  = sanitize($_POST['description']);
    $course_id    = isset($_POST['course_id']) && $_POST['course_id'] !== '' ? (int)$_POST['course_id'] : null;
    $scheduled_at = $_POST['scheduled_at'];
    $duration     = (int)$_POST['duration'];
    $max_participants = (int)$_POST['max_participants'];
    $meeting_link = sanitize($_POST['meeting_link']);
    $meeting_id   = sanitize($_POST['meeting_id']);
    $session_type = sanitize($_POST['session_type'] ?? 'group');
    $is_free      = isset($_POST['is_free']) ? 1 : 0;
    $price        = $is_free ? 0.00 : (float)$_POST['price'];
    $notes        = sanitize($_POST['notes'] ?? '');
    
    // Validation
    if (strlen($title) < 5) $errors[] = 'Title must be at least 5 characters.';
    if (empty($scheduled_at)) $errors[] = 'Please select a date and time.';
    elseif (strtotime($scheduled_at) <= time()) $errors[] = 'Scheduled time must be in the future.';
    if ($duration < 15) $errors[] = 'Duration must be at least 15 minutes.';
    if ($max_participants < 1) $errors[] = 'Max participants must be at least 1.';
    if ($session_type === 'one_on_one') $max_participants = 1;
    if (!$is_free && $price < 0) $errors[] = 'Price cannot be negative.';
    if (!empty($meeting_link) && !filter_var($meeting_link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Please enter a valid meeting URL.';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO sessions 
            (mentor_id, course_id, title, description, scheduled_at, duration, max_participants, 
             meeting_link, meeting_id, is_free, price, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'scheduled')");
        $stmt->execute([
            $user_id, $course_id, $title, $description, $scheduled_at, $duration, 
            $max_participants, $meeting_link, $meeting_id, $is_free, $price
        ]);
        $new_session_id = $pdo->lastInsertId();
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Session "<strong>' . htmlspecialchars($title) . '</strong>" scheduled successfully!'
        ];
        redirect('view.php?id=' . $new_session_id);
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => implode('<br>', $errors)
        ];
    }
}

// Generate default meeting ID
$default_meeting_id = 'skhub_' . strtolower(substr(md5(uniqid()), 0, 8));
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
                    <h1 class="h3 mb-1 fw-bold">Schedule New Session</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Sessions</a></li>
                            <li class="breadcrumb-item active">Schedule</li>
                        </ol>
                    </nav>
                </div>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
            
            <div class="row g-4">
                <!-- Form -->
                <div class="col-lg-8">
                    <form method="POST" id="sessionForm" novalidate>
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
                                           placeholder="e.g. Introduction to Python Programming"
                                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required
                                           maxlength="150">
                                    <div class="form-text">A clear, descriptive title helps students find your session.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Description</label>
                                    <textarea name="description" class="form-control" rows="4" 
                                              placeholder="What will students learn in this session? Include topics, prerequisites, and what to prepare."
                                              maxlength="2000"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                    <div class="form-text">A good description increases bookings.</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Session Type</label>
                                        <select name="session_type" class="form-select" id="sessionType">
                                            <option value="group" <?php echo ($_POST['session_type'] ?? '') === 'group' ? 'selected' : ''; ?>>
                                                Group Session (Multiple students)
                                            </option>
                                            <option value="one_on_one" <?php echo ($_POST['session_type'] ?? '') === 'one_on_one' ? 'selected' : ''; ?>>
                                                1-on-1 Session (Single student)
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Linked Course (Optional)</label>
                                        <select name="course_id" class="form-select">
                                            <option value="">— No Course —</option>
                                            <?php foreach ($courses as $course): ?>
                                            <option value="<?php echo $course['id']; ?>" 
                                                    <?php echo (($_POST['course_id'] ?? $course_id) == $course['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($course['title']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
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
                                               value="<?php echo $_POST['scheduled_at'] ?? ''; ?>" 
                                               min="<?php echo date('Y-m-d\TH:i', strtotime('+30 minutes')); ?>"
                                               required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-medium">Duration <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="duration" class="form-control" 
                                                   value="<?php echo $_POST['duration'] ?? 60; ?>" 
                                                   min="15" max="480" step="15" required>
                                            <span class="input-group-text">minutes</span>
                                        </div>
                                        <div class="form-text">Min: 15 min | Max: 8 hours</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3" id="maxParticipantsField">
                                        <label class="form-label fw-medium">Max Participants <span class="text-danger">*</span></label>
                                        <input type="number" name="max_participants" class="form-control" id="maxParticipants"
                                               value="<?php echo $_POST['max_participants'] ?? 30; ?>" min="1" max="500" required>
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
                                               placeholder="https://meet.google.com/xyz-abc-def"
                                               value="<?php echo htmlspecialchars($_POST['meeting_link'] ?? ''); ?>">
                                    </div>
                                    <div class="form-text">Paste your Zoom, Google Meet, Jitsi, or Teams link.</div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-medium">Meeting ID / Room Name</label>
                                    <div class="input-group">
                                        <input type="text" name="meeting_id" class="form-control" id="meetingId"
                                               value="<?php echo htmlspecialchars($_POST['meeting_id'] ?? $default_meeting_id); ?>">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateMeetingId()" title="Generate new ID">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyMeetingId()" title="Copy ID">
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
                                               <?php echo isset($_POST['is_free']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-medium" for="isFree">
                                            This is a free session
                                        </label>
                                    </div>
                                    <span class="badge bg-success" id="freeBadge" style="<?php echo isset($_POST['is_free']) ? '' : 'display:none'; ?>">
                                        <i class="fas fa-heart me-1"></i>Free
                                    </span>
                                </div>
                                <div id="priceField" <?php echo isset($_POST['is_free']) ? 'style="display:none"' : ''; ?>>
                                    <label class="form-label fw-medium">Price</label>
                                    <div class="input-group" style="max-width: 200px;">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" class="form-control" 
                                               value="<?php echo $_POST['price'] ?? '0.00'; ?>" 
                                               step="0.01" min="0" max="9999">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" name="create_session" class="btn btn-primary px-4">
                                <i class="fas fa-calendar-plus me-2"></i> Schedule Session
                            </button>
                            <a href="index.php" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Sidebar Tips -->
                <div class="col-lg-4">
                    <!-- Preview Card -->
                    <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 80px;">
                        <div class="card-header bg-primary text-white py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-lightbulb me-2"></i>Tips for Great Sessions
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="tip-item d-flex mb-3">
                                <div class="flex-shrink-0 rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                     style="width:36px;height:36px;min-width:36px">
                                    <i class="fas fa-pencil-alt text-primary small"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Clear Title</div>
                                    <div class="text-muted" style="font-size:.8rem">Be specific about the topic students will learn.</div>
                                </div>
                            </div>
                            <div class="tip-item d-flex mb-3">
                                <div class="flex-shrink-0 rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                     style="width:36px;height:36px;min-width:36px">
                                    <i class="fas fa-clock text-success small"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Right Duration</div>
                                    <div class="text-muted" style="font-size:.8rem">60–90 minutes works best. Short enough to stay focused.</div>
                                </div>
                            </div>
                            <div class="tip-item d-flex mb-3">
                                <div class="flex-shrink-0 rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                     style="width:36px;height:36px;min-width:36px">
                                    <i class="fas fa-video text-warning small"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Meeting Link</div>
                                    <div class="text-muted" style="font-size:.8rem">Add the meeting link before the session starts.</div>
                                </div>
                            </div>
                            <div class="tip-item d-flex">
                                <div class="flex-shrink-0 rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                     style="width:36px;height:36px;min-width:36px">
                                    <i class="fas fa-users text-info small"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold small">Reasonable Limit</div>
                                    <div class="text-muted" style="font-size:.8rem">More interactive with smaller groups (10–20 students).</div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h6 class="fw-semibold mb-3"><i class="fas fa-video text-primary me-2"></i>Meeting Platforms</h6>
                            <div class="d-grid gap-2">
                                <a href="https://meet.google.com" target="_blank" class="btn btn-outline-secondary btn-sm text-start">
                                    <img src="https://www.gstatic.com/images/branding/product/1x/meet_2020q4_48dp.png" 
                                         width="18" class="me-2" alt="">Google Meet <span class="badge bg-success ms-1">Free</span>
                                </a>
                                <a href="https://meet.jit.si" target="_blank" class="btn btn-outline-secondary btn-sm text-start">
                                    <i class="fas fa-video text-primary me-2"></i>Jitsi Meet <span class="badge bg-success ms-1">Free</span>
                                </a>
                                <a href="https://zoom.us" target="_blank" class="btn btn-outline-secondary btn-sm text-start">
                                    <i class="fas fa-video text-primary me-2"></i>Zoom
                                </a>
                                <a href="https://teams.microsoft.com" target="_blank" class="btn btn-outline-secondary btn-sm text-start">
                                    <i class="fas fa-video text-primary me-2"></i>Microsoft Teams
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Session type handler
document.getElementById('sessionType').addEventListener('change', function() {
    var maxField = document.getElementById('maxParticipants');
    if (this.value === 'one_on_one') {
        maxField.value = 1;
        maxField.readOnly = true;
        maxField.closest('.col-md-6').querySelector('.form-text') && 
            maxField.closest('.col-md-6').insertAdjacentHTML('beforeend', '<div class="form-text text-info"><i class="fas fa-info-circle me-1"></i>1-on-1 sessions allow only 1 student.</div>');
    } else {
        maxField.readOnly = false;
        if (maxField.value <= 1) maxField.value = 30;
        var tip = maxField.closest('.col-md-6').querySelector('.text-info');
        if (tip) tip.remove();
    }
});

// Free session toggle
document.getElementById('isFree').addEventListener('change', function() {
    var priceField = document.getElementById('priceField');
    var freeBadge = document.getElementById('freeBadge');
    if (this.checked) {
        priceField.style.display = 'none';
        document.querySelector('input[name="price"]').value = '0.00';
        freeBadge.style.display = '';
    } else {
        priceField.style.display = '';
        freeBadge.style.display = 'none';
    }
});

// Generate meeting ID
function generateMeetingId() {
    var chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    var id = 'skhub_';
    for (var i = 0; i < 8; i++) id += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('meetingId').value = id;
}

// Copy meeting ID
function copyMeetingId() {
    var input = document.getElementById('meetingId');
    input.select();
    document.execCommand('copy');
    
    // Feedback
    var btn = input.nextElementSibling.nextElementSibling;
    var original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check text-success"></i>';
    setTimeout(function() { btn.innerHTML = original; }, 1500);
}

// Form validation highlight
document.getElementById('sessionForm').addEventListener('submit', function(e) {
    var title = this.querySelector('[name="title"]');
    var schedTime = this.querySelector('[name="scheduled_at"]');
    var duration = this.querySelector('[name="duration"]');
    
    var valid = true;
    
    if (title.value.trim().length < 5) {
        title.classList.add('is-invalid');
        valid = false;
    } else title.classList.remove('is-invalid');
    
    if (!schedTime.value || new Date(schedTime.value) <= new Date()) {
        schedTime.classList.add('is-invalid');
        valid = false;
    } else schedTime.classList.remove('is-invalid');
    
    if (parseInt(duration.value) < 15) {
        duration.classList.add('is-invalid');
        valid = false;
    } else duration.classList.remove('is-invalid');
    
    if (!valid) {
        e.preventDefault();
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
});
</script>

<?php include '../../includes/footer.php'; ?>