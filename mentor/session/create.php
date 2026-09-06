<?php
$page_title = 'Schedule Session';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;

// Get mentor's courses
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE mentor_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_session'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $course_id = (int)$_POST['course_id'];
    $scheduled_at = $_POST['scheduled_at'];
    $duration = (int)$_POST['duration'];
    $max_participants = (int)$_POST['max_participants'];
    $meeting_link = sanitize($_POST['meeting_link']);
    $meeting_id = sanitize($_POST['meeting_id']);
    $is_free = isset($_POST['is_free']) ? 1 : 0;
    $price = (float)$_POST['price'];
    
    // Validate
    $errors = [];
    if (strlen($title) < 5) $errors[] = 'Title must be at least 5 characters.';
    if (strtotime($scheduled_at) < time()) $errors[] = 'Scheduled time must be in the future.';
    if ($duration < 15) $errors[] = 'Duration must be at least 15 minutes.';
    if ($max_participants < 1) $errors[] = 'Max participants must be at least 1.';
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO sessions (mentor_id, course_id, title, description, scheduled_at, duration, max_participants, meeting_link, meeting_id, is_free, price, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'scheduled')");
        $stmt->execute([$user_id, $course_id, $title, $description, $scheduled_at, $duration, $max_participants, $meeting_link, $meeting_id, $is_free, $price]);
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Session scheduled successfully!'
        ];
        redirect('index.php');
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
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Schedule Session</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Session Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" 
                                           placeholder="Enter session title" 
                                           value="<?php echo $_POST['title'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3" 
                                              placeholder="Describe what this session covers"><?php echo $_POST['description'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Course (Optional)</label>
                                    <select name="course_id" class="form-select">
                                        <option value="">No Course</option>
                                        <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>" <?php echo ($_POST['course_id'] ?? $course_id) == $course['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="scheduled_at" class="form-control" 
                                               value="<?php echo $_POST['scheduled_at'] ?? ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                        <input type="number" name="duration" class="form-control" 
                                               value="<?php echo $_POST['duration'] ?? 60; ?>" min="15" step="5" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Max Participants <span class="text-danger">*</span></label>
                                        <input type="number" name="max_participants" class="form-control" 
                                               value="<?php echo $_POST['max_participants'] ?? 50; ?>" min="1" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Meeting Link</label>
                                        <input type="url" name="meeting_link" class="form-control" 
                                               placeholder="https://meet.jit.si/room" 
                                               value="<?php echo $_POST['meeting_link'] ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meeting ID</label>
                                    <input type="text" name="meeting_id" class="form-control" 
                                           placeholder="Unique meeting identifier" 
                                           value="<?php echo $_POST['meeting_id'] ?? 'session_' . time(); ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_free" id="isFree" 
                                                   <?php echo isset($_POST['is_free']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="isFree">Free Session</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" id="priceField">
                                        <label class="form-label">Price ($)</label>
                                        <input type="number" name="price" class="form-control" 
                                               value="<?php echo $_POST['price'] ?? '0.00'; ?>" step="0.01" min="0">
                                    </div>
                                </div>
                                
                                <button type="submit" name="create_session" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus"></i> Schedule Session
                                </button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h6><i class="fas fa-info-circle text-primary"></i> Tips</h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Choose a clear, descriptive title
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Set appropriate duration for content
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Provide meeting link for easy access
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Link to course if session is part of it
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-1"></i>
                                    Set reasonable participant limit
                                </li>
                            </ul>
                            
                            <hr>
                            
                            <h6><i class="fas fa-video text-primary"></i> Meeting Platforms</h6>
                            <ul class="list-unstyled small">
                                <li><a href="https://meet.jit.si" target="_blank">Jitsi Meet</a> - Free</li>
                                <li><a href="https://zoom.us" target="_blank">Zoom</a> - Paid</li>
                                <li><a href="https://meet.google.com" target="_blank">Google Meet</a> - Free</li>
                                <li><a href="https://teams.microsoft.com" target="_blank">Microsoft Teams</a> - Free</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('isFree').addEventListener('change', function() {
    const priceField = document.getElementById('priceField');
    if (this.checked) {
        priceField.style.display = 'none';
        document.querySelector('input[name="price"]').value = '0.00';
    } else {
        priceField.style.display = 'block';
    }
});
</script>

<?php include '../../includes/footer.php'; ?>