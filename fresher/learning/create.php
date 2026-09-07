<?php
$page_title = 'Create New Course';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

// Note: This is for demonstration - normally only mentors can create courses
// This page shows how a fresher can create/request a course

$user_id = getUserId();
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$errors = [];

// Get fields for dropdown
$fields = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name")->fetchAll();

// Handle course creation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_course'])) {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $field_id = (int)($_POST['field_id'] ?? 0);
    $level = sanitize($_POST['level'] ?? 'beginner');
    $price = (float)($_POST['price'] ?? 0);
    $duration = (int)($_POST['duration'] ?? 0);
    $objectives = sanitize($_POST['objectives'] ?? '');
    $requirements = sanitize($_POST['requirements'] ?? '');
    
    // Validation
    if (strlen($title) < 5) {
        $errors[] = 'Title must be at least 5 characters.';
    }
    if (strlen($description) < 20) {
        $errors[] = 'Description must be at least 20 characters.';
    }
    if (!$field_id) {
        $errors[] = 'Please select an academic field.';
    }
    if (!in_array($level, ['beginner', 'intermediate', 'advanced'])) {
        $errors[] = 'Invalid level selected.';
    }
    if ($duration < 1) {
        $errors[] = 'Duration must be at least 1 hour.';
    }
    
    if (empty($errors)) {
        // Insert as a request (status: pending)
        $stmt = $pdo->prepare("INSERT INTO courses (mentor_id, field_id, title, description, level, price, duration, status, requirements, learning_outcomes) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)");
        $stmt->execute([$user_id, $field_id, $title, $description, $level, $price, $duration, $requirements, $objectives]);
        $course_id = $pdo->lastInsertId();
        
        // Create notification for admin
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) 
                               VALUES ((SELECT id FROM users WHERE role = 'admin' LIMIT 1), 'course_request', 
                                       'New Course Request', 
                                       CONCAT(?, ' has requested a new course: ', ?), 
                                       'admin/courses/edit.php?id=' || ?)");
        $stmt->execute([getUserName(), $title, $course_id]);
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Your course request has been submitted! An administrator will review it.'
        ];
        redirect('my-course.php');
    }
}

// Get course suggestions based on interests
$stmt = $pdo->prepare("SELECT interests FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$interests = $user['interests'] ?? '';
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
                <h1 class="h2">Request a Course</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="my-course.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> My Courses
                    </a>
                </div>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <!-- Steps -->
                            <div class="d-flex justify-content-between mb-4">
                                <div class="step <?php echo $step >= 1 ? 'active' : ''; ?>">
                                    <span class="step-number">1</span>
                                    <span class="step-label">Basic Info</span>
                                </div>
                                <div class="step-line <?php echo $step >= 2 ? 'active' : ''; ?>"></div>
                                <div class="step <?php echo $step >= 2 ? 'active' : ''; ?>">
                                    <span class="step-number">2</span>
                                    <span class="step-label">Course Details</span>
                                </div>
                                <div class="step-line <?php echo $step >= 3 ? 'active' : ''; ?>"></div>
                                <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">
                                    <span class="step-number">3</span>
                                    <span class="step-label">Review & Submit</span>
                                </div>
                            </div>
                            
                            <form method="POST" id="courseForm">
                                <?php if ($step == 1): ?>
                                <!-- Step 1: Basic Information -->
                                <h5>Basic Information</h5>
                                <p class="text-muted">Tell us about the course you want to learn.</p>
                                
                                <div class="mb-3">
                                    <label class="form-label">Course Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" 
                                           placeholder="Enter a descriptive title" 
                                           value="<?php echo $_POST['title'] ?? ''; ?>" required>
                                    <small class="text-muted">Be specific about what you want to learn.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Academic Field <span class="text-danger">*</span></label>
                                    <select name="field_id" class="form-select" required>
                                        <option value="">Select a field</option>
                                        <?php foreach ($fields as $field): ?>
                                        <option value="<?php echo $field['id']; ?>" <?php echo ($_POST['field_id'] ?? 0) == $field['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($field['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Level <span class="text-danger">*</span></label>
                                    <select name="level" class="form-select" required>
                                        <option value="beginner" <?php echo ($_POST['level'] ?? '') == 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                        <option value="intermediate" <?php echo ($_POST['level'] ?? '') == 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                        <option value="advanced" <?php echo ($_POST['level'] ?? '') == 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Brief Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="3" 
                                              placeholder="What is this course about?" required><?php echo $_POST['description'] ?? ''; ?></textarea>
                                    <small class="text-muted">Minimum 20 characters. Describe what you want to learn.</small>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                        Next Step <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                                
                                <?php elseif ($step == 2): ?>
                                <!-- Step 2: Course Details -->
                                <h5>Course Details</h5>
                                <p class="text-muted">Provide more details about the course you want.</p>
                                
                                <div class="mb-3">
                                    <label class="form-label">Learning Objectives</label>
                                    <textarea name="objectives" class="form-control" rows="3" 
                                              placeholder="What will you learn? List key objectives..."><?php echo $_POST['objectives'] ?? ''; ?></textarea>
                                    <small class="text-muted">e.g., Master React.js, Build full-stack applications, Understand AI concepts</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Requirements / Prerequisites</label>
                                    <textarea name="requirements" class="form-control" rows="2" 
                                              placeholder="What do you need to know before starting?"><?php echo $_POST['requirements'] ?? ''; ?></textarea>
                                    <small class="text-muted">e.g., Basic Python knowledge, HTML/CSS experience</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estimated Duration <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="duration" class="form-control" 
                                                   value="<?php echo $_POST['duration'] ?? ''; ?>" 
                                                   min="1" max="200" required>
                                            <span class="input-group-text">hours</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Suggested Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="price" class="form-control" 
                                                   value="<?php echo $_POST['price'] ?? '0.00'; ?>" 
                                                   step="0.01" min="0">
                                            <span class="input-group-text">.00</span>
                                        </div>
                                        <small class="text-muted">0 for free courses</small>
                                    </div>
                                </div>
                                
                                <!-- Interest-based suggestions -->
                                <?php if ($interests): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Based on your interests:</strong>
                                    <p class="mb-0"><?php echo htmlspecialchars($interests); ?></p>
                                    <small>We'll match you with mentors who specialize in these areas.</small>
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                        Next Step <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                                
                                <?php else: ?>
                                <!-- Step 3: Review & Submit -->
                                <h5>Review & Submit</h5>
                                <p class="text-muted">Review your course request before submitting.</p>
                                
                                <div class="review-section">
                                    <div class="row mb-3">
                                        <div class="col-md-4"><strong>Title:</strong></div>
                                        <div class="col-md-8" id="reviewTitle"><?php echo htmlspecialchars($_POST['title'] ?? ''); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4"><strong>Field:</strong></div>
                                        <div class="col-md-8" id="reviewField">
                                            <?php 
                                            $field_name = '';
                                            foreach ($fields as $f) {
                                                if ($f['id'] == ($_POST['field_id'] ?? 0)) {
                                                    $field_name = $f['name'];
                                                    break;
                                                }
                                            }
                                            echo htmlspecialchars($field_name);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4"><strong>Level:</strong></div>
                                        <div class="col-md-8" id="reviewLevel"><?php echo ucfirst($_POST['level'] ?? 'Beginner'); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4"><strong>Duration:</strong></div>
                                        <div class="col-md-8" id="reviewDuration"><?php echo ($_POST['duration'] ?? 0) . ' hours'; ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4"><strong>Price:</strong></div>
                                        <div class="col-md-8" id="reviewPrice">$<?php echo number_format($_POST['price'] ?? 0, 2); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4"><strong>Description:</strong></div>
                                        <div class="col-md-8" id="reviewDescription"><?php echo nl2br(htmlspecialchars($_POST['description'] ?? '')); ?></div>
                                    </div>
                                    <?php if (!empty($_POST['objectives'])): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-4"><strong>Objectives:</strong></div>
                                        <div class="col-md-8" id="reviewObjectives"><?php echo nl2br(htmlspecialchars($_POST['objectives'] ?? '')); ?></div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($_POST['requirements'])): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-4"><strong>Requirements:</strong></div>
                                        <div class="col-md-8" id="reviewRequirements"><?php echo nl2br(htmlspecialchars($_POST['requirements'] ?? '')); ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Note:</strong> This is a course <em>request</em>. An administrator will review it and assign a mentor who matches your requirements.
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                    <button type="submit" name="submit_course" class="btn btn-success">
                                        <i class="fas fa-paper-plane"></i> Submit Request
                                    </button>
                                </div>
                                <?php endif; ?>
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
                                    Be specific about what you want to learn
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Mention your current skill level
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Include any deadlines or time constraints
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Consider your learning style preferences
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-1"></i>
                                    The more details, the better match you'll get
                                </li>
                            </ul>
                            
                            <hr>
                            
                            <h6><i class="fas fa-question-circle text-primary"></i> What happens next?</h6>
                            <ol class="small">
                                <li>Your request is sent to administrators</li>
                                <li>They review and find a suitable mentor</li>
                                <li>You'll be notified when the course is ready</li>
                                <li>Start learning with your assigned mentor</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function nextStep(step) {
    // Validate current step
    const form = document.getElementById('courseForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    let isValid = true;
    
    // Basic validation for step 1
    if (step === 2) {
        const title = document.querySelector('input[name="title"]');
        const description = document.querySelector('textarea[name="description"]');
        const field = document.querySelector('select[name="field_id"]');
        
        if (!title.value.trim() || title.value.trim().length < 5) {
            title.classList.add('is-invalid');
            isValid = false;
        } else {
            title.classList.remove('is-invalid');
        }
        
        if (!description.value.trim() || description.value.trim().length < 20) {
            description.classList.add('is-invalid');
            isValid = false;
        } else {
            description.classList.remove('is-invalid');
        }
        
        if (!field.value) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
        
        if (!isValid) {
            showNotification('Please fill in all required fields correctly.', 'error');
            return;
        }
    }
    
    if (step === 3) {
        const duration = document.querySelector('input[name="duration"]');
        if (!duration.value || duration.value < 1) {
            duration.classList.add('is-invalid');
            isValid = false;
        } else {
            duration.classList.remove('is-invalid');
        }
        
        if (!isValid) {
            showNotification('Please enter a valid duration.', 'error');
            return;
        }
    }
    
    // Update step in URL
    window.location.href = 'create.php?step=' + step;
}

function prevStep(step) {
    window.location.href = 'create.php?step=' + step;
}
</script>

<style>
.step {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 0 0 auto;
}

.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    font-weight: bold;
    font-size: 14px;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: #6C63FF;
    color: white;
}

.step-label {
    font-size: 14px;
    color: #6c757d;
}

.step.active .step-label {
    color: #212529;
    font-weight: 500;
}

.step-line {
    flex: 1;
    height: 2px;
    background: #e9ecef;
    margin: 0 8px;
    transition: all 0.3s ease;
}

.step-line.active {
    background: #6C63FF;
}

.review-section {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 8px;
}

.review-section .row {
    margin-bottom: 8px;
}

.review-section .row:last-child {
    margin-bottom: 0;
}
</style>

<?php include '../../includes/footer.php'; ?>