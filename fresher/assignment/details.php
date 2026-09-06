<?php
$page_title = 'Assignment Details';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$assignment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$user_id = getUserId();

// Get assignment details
$stmt = $pdo->prepare("SELECT a.*, c.title as course_title, u.full_name as mentor_name,
                       (SELECT id FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as submission_id,
                       (SELECT submission_file FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as submission_file,
                       (SELECT submission_text FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as submission_text,
                       (SELECT submitted_at FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as submitted_at,
                       (SELECT score FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as score,
                       (SELECT feedback FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as feedback,
                       (SELECT status FROM assignment_submissions WHERE assignment_id = a.id AND fresher_id = ?) as submission_status
                       FROM assignments a 
                       JOIN courses c ON a.course_id = c.id 
                       JOIN users u ON a.mentor_id = u.id 
                       WHERE a.id = ? AND a.is_published = 1");
$stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $assignment_id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    redirect('index.php');
}

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_assignment'])) {
    $submission_text = sanitize($_POST['submission_text'] ?? '');
    $submission_file = null;
    
    // Handle file upload
    if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/assignments/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['submission_file']['name'], PATHINFO_EXTENSION);
        $file_name = 'assignment_' . $assignment_id . '_' . $user_id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $file_path)) {
            $submission_file = 'uploads/assignments/' . $file_name;
        }
    }
    
    if ($assignment['submission_id']) {
        // Update submission
        $stmt = $pdo->prepare("UPDATE assignment_submissions 
                               SET submission_text = ?, submission_file = COALESCE(?, submission_file), 
                                   submitted_at = NOW(), status = 'submitted' 
                               WHERE id = ?");
        $stmt->execute([$submission_text, $submission_file, $assignment['submission_id']]);
    } else {
        // New submission
        $stmt = $pdo->prepare("INSERT INTO assignment_submissions (assignment_id, fresher_id, submission_text, submission_file, status) 
                               VALUES (?, ?, ?, ?, 'submitted')");
        $stmt->execute([$assignment_id, $user_id, $submission_text, $submission_file]);
    }
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Assignment submitted successfully!'
    ];
    redirect('details.php?id=' . $assignment_id);
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
                <h1 class="h2">Assignment Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                            <p class="text-muted"><?php echo htmlspecialchars($assignment['course_title']); ?></p>
                            
                            <div class="mb-3">
                                <span class="badge bg-info">Due: <?php echo formatDateTime($assignment['due_date']); ?></span>
                                <span class="badge bg-secondary">Max Score: <?php echo $assignment['max_score']; ?></span>
                            </div>
                            
                            <h5>Description</h5>
                            <p><?php echo nl2br(htmlspecialchars($assignment['description'] ?? '')); ?></p>
                            
                            <?php if ($assignment['instructions']): ?>
                            <h5>Instructions</h5>
                            <p><?php echo nl2br(htmlspecialchars($assignment['instructions'])); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($assignment['attachment_url']): ?>
                            <a href="<?php echo $assignment['attachment_url']; ?>" class="btn btn-outline-primary" download>
                                <i class="fas fa-paperclip"></i> Download Attachment
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($assignment['submission_id'] && $assignment['submission_status'] === 'graded'): ?>
                    <!-- Feedback -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>Feedback</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Score:</strong> <?php echo $assignment['score']; ?>/<?php echo $assignment['max_score']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Submitted:</strong> <?php echo formatDateTime($assignment['submitted_at']); ?></p>
                                </div>
                            </div>
                            <p><strong>Feedback:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($assignment['feedback'] ?? 'No feedback provided yet.')); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-lg-4">
                    <!-- Submission Form -->
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h5>
                                <?php if ($assignment['submission_id']): ?>
                                    Update Submission
                                <?php else: ?>
                                    Submit Assignment
                                <?php endif; ?>
                            </h5>
                            
                            <?php if ($assignment['submission_id']): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    You submitted this assignment on <?php echo formatDateTime($assignment['submitted_at']); ?>
                                    <?php if ($assignment['submission_status'] === 'graded'): ?>
                                        <br><strong>Status:</strong> Graded
                                    <?php else: ?>
                                        <br><strong>Status:</strong> <?php echo ucfirst($assignment['submission_status'] ?? 'Submitted'); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Your Submission</label>
                                    <textarea name="submission_text" class="form-control" rows="5" 
                                              placeholder="Write your submission here..."><?php echo htmlspecialchars($assignment['submission_text'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Upload File (Optional)</label>
                                    <input type="file" name="submission_file" class="form-control" accept=".pdf,.doc,.docx,.zip,.txt">
                                    <?php if ($assignment['submission_file']): ?>
                                        <small class="text-muted">Current file: <a href="../../<?php echo $assignment['submission_file']; ?>" target="_blank">View File</a></small>
                                    <?php endif; ?>
                                </div>
                                
                                <button type="submit" name="submit_assignment" class="btn btn-primary w-100">
                                    <i class="fas fa-upload"></i> <?php echo $assignment['submission_id'] ? 'Update Submission' : 'Submit Assignment'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>