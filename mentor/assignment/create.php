<?php
$page_title = 'Create Assignment';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();

// Get mentor's courses
$stmt = $pdo->prepare("SELECT id, title FROM courses WHERE mentor_id = ? AND status = 'active'");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_assignment'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $instructions = sanitize($_POST['instructions']);
    $course_id = (int)$_POST['course_id'];
    $due_date = $_POST['due_date'];
    $max_score = (float)$_POST['max_score'];
    $weight = (float)$_POST['weight'];
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO assignments (mentor_id, course_id, title, description, instructions, due_date, max_score, weight, is_published) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $course_id, $title, $description, $instructions, $due_date, $max_score, $weight, $is_published]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Assignment created successfully!'
    ];
    redirect('index.php');
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
                <h1 class="h2">Create Assignment</h1>
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
                                    <label class="form-label">Assignment Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" 
                                           placeholder="Enter assignment title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Course <span class="text-danger">*</span></label>
                                    <select name="course_id" class="form-select" required>
                                        <option value="">Select a course</option>
                                        <?php foreach ($courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>">
                                            <?php echo htmlspecialchars($course['title']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3" 
                                              placeholder="Describe the assignment"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Instructions</label>
                                    <textarea name="instructions" class="form-control" rows="5" 
                                              placeholder="Detailed instructions for students"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="due_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Max Score <span class="text-danger">*</span></label>
                                        <input type="number" name="max_score" class="form-control" 
                                               value="100" step="0.5" min="1" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Weight (%)</label>
                                    <input type="number" name="weight" class="form-control" 
                                           value="10" step="1" min="0" max="100">
                                    <small class="text-muted">Weight of this assignment in the overall course grade</small>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="is_published" class="form-check-input" id="isPublished" checked>
                                    <label class="form-check-label" for="isPublished">Publish immediately</label>
                                    <br><small class="text-muted">Students will see this assignment when published</small>
                                </div>
                                
                                <button type="submit" name="create_assignment" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Assignment
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
                                    Provide clear instructions
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Set realistic due dates
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Define clear grading criteria
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Consider assignment weight
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-1"></i>
                                    Publish only when ready
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>