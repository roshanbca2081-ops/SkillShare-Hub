<?php
$page_title = 'Issue Certificate';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();

// Get mentor's courses with completed students
$stmt = $pdo->prepare("SELECT DISTINCT c.id, c.title 
                       FROM courses c 
                       WHERE c.mentor_id = ? AND c.status = 'active'");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_certificate'])) {
    $course_id = (int)$_POST['course_id'];
    $student_id = (int)$_POST['student_id'];
    
    // Check if certificate already exists
    $stmt = $pdo->prepare("SELECT id FROM certificates WHERE fresher_id = ? AND course_id = ?");
    $stmt->execute([$student_id, $course_id]);
    if ($stmt->fetch()) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Certificate already issued for this student.'
        ];
        redirect('issue.php');
    }
    
    // Generate certificate code
    $code = 'CERT-' . strtoupper(substr(md5($course_id . $student_id . time()), 0, 10));
    
    $stmt = $pdo->prepare("INSERT INTO certificates (fresher_id, course_id, certificate_code) VALUES (?, ?, ?)");
    $stmt->execute([$student_id, $course_id, $code]);
    
    // Notify student
     $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) 
                            VALUES (?, 'certificate', 'Certificate Issued', 
                                    CONCAT('You have been issued a certificate for completing the course.'),
                                    'fresher/certificate/view.php?id=' || ?)");
    $stmt->execute([$student_id, $course_id]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Certificate issued successfully!'
    ];
    redirect('index.php');
}

// Get students for selected course via AJAX
$course_id = isset($_GET['course']) ? (int)$_GET['course'] : 0;
$students = [];

if ($course_id) {
    $stmt = $pdo->prepare("SELECT u.id, u.full_name, u.email, e.progress 
                           FROM enrollments e 
                           JOIN users u ON e.fresher_id = u.id 
                           WHERE e.course_id = ? AND e.status = 'completed' 
                           AND NOT EXISTS (SELECT 1 FROM certificates c WHERE c.fresher_id = u.id AND c.course_id = ?)");
    $stmt->execute([$course_id, $course_id]);
    $students = $stmt->fetchAll();
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
                <h1 class="h2">Issue Certificate</h1>
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
                                    <label class="form-label">Select Course <span class="text-danger">*</span></label>
                                    <select name="course_id" class="form-select" id="courseSelect" required>
                                        <option value="">Select a course</option>
                                        <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>" <?php echo $course_id == $course['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Select Student <span class="text-danger">*</span></label>
                                    <select name="student_id" class="form-select" id="studentSelect" required>
                                        <option value="">Select a student</option>
                                        <?php foreach ($students as $student): ?>
                                        <option value="<?php echo $student['id']; ?>">
                                            <?php echo htmlspecialchars($student['full_name']); ?> 
                                            (<?php echo round($student['progress']); ?>% completed)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <button type="submit" name="issue_certificate" class="btn btn-primary">
                                    <i class="fas fa-certificate"></i> Issue Certificate
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h6><i class="fas fa-info-circle text-primary"></i> Information</h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Only students with completed courses
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Unique certificate codes generated
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Students notified automatically
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-1"></i>
                                    Certificates can be verified online
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('courseSelect').addEventListener('change', function() {
    if (this.value) {
        window.location.href = 'issue.php?course=' + this.value;
    }
});
</script>

<?php include '../../includes/footer.php'; ?>