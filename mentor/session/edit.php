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
    redirect('index.php');
}

// Get mentor's courses
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE mentor_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_session'])) {
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
    $status = sanitize($_POST['status']);
    
    $stmt = $pdo->prepare("UPDATE sessions SET 
                           title = ?, description = ?, course_id = ?, scheduled_at = ?, 
                           duration = ?, max_participants = ?, meeting_link = ?, meeting_id = ?, 
                           is_free = ?, price = ?, status = ? 
                           WHERE id = ? AND mentor_id = ?");
    $stmt->execute([$title, $description, $course_id, $scheduled_at, $duration, $max_participants, 
                    $meeting_link, $meeting_id, $is_free, $price, $status, $session_id, $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Session updated successfully!'
    ];
    redirect('edit.php?id=' . $session_id);
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
                <h1 class="h2">Edit Session</h1>
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
                                           value="<?php echo htmlspecialchars($session['title']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($session['description']); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Course (Optional)</label>
                                    <select name="course_id" class="form-select">
                                        <option value="">No Course</option>
                                        <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>" <?php echo $session['course_id'] == $course['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="scheduled_at" class="form-control" 
                                               value="<?php echo date('Y-m-d\TH:i', strtotime($session['scheduled_at'])); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                        <input type="number" name="duration" class="form-control" 
                                               value="<?php echo $session['duration']; ?>" min="15" step="5" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Max Participants <span class="text-danger">*</span></label>
                                        <input type="number" name="max_participants" class="form-control" 
                                               value="<?php echo $session['max_participants']; ?>" min="1" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Meeting Link</label>
                                        <input type="url" name="meeting_link" class="form-control" 
                                               value="<?php echo htmlspecialchars($session['meeting_link']); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meeting ID</label>
                                    <input type="text" name="meeting_id" class="form-control" 
                                           value="<?php echo htmlspecialchars($session['meeting_id']); ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_free" id="isFree" 
                                                   <?php echo $session['is_free'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="isFree">Free Session</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" id="priceField" <?php echo $session['is_free'] ? 'style="display:none"' : ''; ?>>
                                        <label class="form-label">Price ($)</label>
                                        <input type="number" name="price" class="form-control" 
                                               value="<?php echo $session['price']; ?>" step="0.01" min="0">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="scheduled" <?php echo $session['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                        <option value="ongoing" <?php echo $session['status'] === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                        <option value="completed" <?php echo $session['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $session['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                
                                <button type="submit" name="update_session" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Session
                                </button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h6>Session Stats</h6>
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE session_id = ? AND status = 'approved'");
                            $stmt->execute([$session_id]);
                            $approved = $stmt->fetchColumn();
                            
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE session_id = ? AND status = 'pending'");
                            $stmt->execute([$session_id]);
                            $pending = $stmt->fetchColumn();
                            ?>
                            <ul class="list-unstyled small">
                                <li><strong>Approved:</strong> <?php echo $approved; ?></li>
                                <li><strong>Pending:</strong> <?php echo $pending; ?></li>
                                <li><strong>Created:</strong> <?php echo formatDateTime($session['created_at']); ?></li>
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