<?php
$page_title = 'Register';
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'config/functions.php';
require_once 'config/validation.php';

if (isLoggedIn()) {
    redirect('index.php');
}

// Fetch academic fields for dropdown
$stmt = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name");
$academic_fields = $stmt->fetchAll();

// Fetch all courses with field association
$stmt = $pdo->query("SELECT c.*, f.name as field_name 
                     FROM courses c 
                     LEFT JOIN academic_fields f ON c.field_id = f.id 
                     WHERE c.status = 'active' 
                     ORDER BY f.name, c.title");
$all_courses = $stmt->fetchAll();

// Group courses by field
$courses_by_field = [];
foreach ($all_courses as $course) {
    $field_id = $course['field_id'] ?? 0;
    if (!isset($courses_by_field[$field_id])) {
        $courses_by_field[$field_id] = [];
    }
    $courses_by_field[$field_id][] = $course;
}

// Fetch skills grouped by field
$skills_by_field = [];
foreach ($academic_fields as $field) {
        // Skills are associated with a field through the mentor's courses.
        $stmt = $pdo->prepare("SELECT DISTINCT u.skills
                                                     FROM users u
                                                     INNER JOIN courses c ON c.mentor_id = u.id
                                                     WHERE c.field_id = ?
                                                         AND u.skills IS NOT NULL
                                                         AND u.skills != ''");
    $stmt->execute([$field['id']]);
    $field_skills = $stmt->fetchAll();
    
    $skills = [];
    foreach ($field_skills as $fs) {
        $user_skills = array_map('trim', explode(',', $fs['skills']));
        foreach ($user_skills as $skill) {
            if (!empty($skill) && !in_array($skill, $skills)) {
                $skills[] = $skill;
            }
        }
    }
    
    // If no skills found, add default skills based on field
    if (empty($skills)) {
        $default_skills = [
            'Computer Science' => ['PHP', 'JavaScript', 'Python', 'Java', 'C++', 'React', 'Node.js', 'MySQL', 'Data Structures'],
            'Data Science' => ['Python', 'R', 'SQL', 'Machine Learning', 'Statistics', 'TensorFlow', 'Pandas', 'NumPy'],
            'Artificial Intelligence' => ['Python', 'TensorFlow', 'PyTorch', 'Deep Learning', 'NLP', 'Computer Vision', 'Reinforcement Learning'],
            'Web Development' => ['HTML', 'CSS', 'JavaScript', 'React', 'Vue.js', 'Angular', 'Node.js', 'PHP', 'Laravel'],
            'Mobile Development' => ['Java', 'Kotlin', 'Swift', 'React Native', 'Flutter', 'Android', 'iOS'],
            'Cloud Computing' => ['AWS', 'Azure', 'GCP', 'Docker', 'Kubernetes', 'Jenkins', 'Terraform', 'Linux'],
            'Cybersecurity' => ['Network Security', 'Penetration Testing', 'OWASP', 'Firewall', 'Encryption', 'Security Auditing'],
            'Blockchain' => ['Solidity', 'Ethereum', 'Web3', 'Smart Contracts', 'DeFi', 'Cryptography'],
            'UI/UX Design' => ['Figma', 'Adobe XD', 'Photoshop', 'User Research', 'Prototyping', 'Design Thinking'],
            'Digital Marketing' => ['SEO', 'SEM', 'Content Marketing', 'Social Media', 'Google Analytics', 'Email Marketing']
        ];
        $skills = $default_skills[$field['name']] ?? ['Programming', 'Communication', 'Problem Solving', 'Team Work'];
    }
    
    $skills_by_field[$field['id']] = $skills;
}

// Get all skills for fallback
$all_skills = [];
foreach ($skills_by_field as $skills) {
    foreach ($skills as $skill) {
        if (!in_array($skill, $all_skills)) {
            $all_skills[] = $skill;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create validator instance
    $validator = new Validator($_POST);
    
    // Validate all fields
    $validator
        ->required('full_name', 'Full name is required.')
        ->minLength('full_name', 2, 'Full name must be at least 2 characters.')
        ->maxLength('full_name', 100, 'Full name must not exceed 100 characters.')
        
        ->required('email', 'Email address is required.')
        ->email('email', 'Please enter a valid email address.')
        ->unique('email', 'users', 'email', 'This email is already registered.')
        
        ->required('password', 'Password is required.')
        ->password('password', 'Password must be at least 8 characters and contain uppercase, lowercase, number, and special character.')
        ->confirmed('password', 'confirm_password', 'Passwords do not match.')
        
        ->required('role', 'Please select a role.')
        ->inArray('role', ['fresher', 'mentor'], 'Invalid role selected.')
        
        ->required('terms', 'You must agree to the terms and conditions.');
    
    if ($validator->passes()) {
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize($_POST['role']);
        $bio = sanitize($_POST['bio'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $location = sanitize($_POST['location'] ?? '');
        $academic_field_id = !empty($_POST['academic_field']) ? (int)$_POST['academic_field'] : null;
        $interests = !empty($_POST['interests']) ? sanitize($_POST['interests']) : '';
        
        // Get selected skills
        $selected_skills = isset($_POST['skills']) ? array_map('sanitize', $_POST['skills']) : [];
        $skills_string = !empty($selected_skills) ? implode(', ', $selected_skills) : '';
        
        // Get selected courses based on role
        if ($role === 'fresher') {
            $selected_courses = isset($_POST['learn_courses']) ? array_map('intval', $_POST['learn_courses']) : [];
        } else {
            $selected_courses = isset($_POST['teach_courses']) ? array_map('intval', $_POST['teach_courses']) : [];
        }
        
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, bio, phone, location, skills, interests) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $email, $hashed, $role, $bio, $phone, $location, $skills_string, $interests]);
        $user_id = $pdo->lastInsertId();
        
        // Handle course enrollments based on role
        if (!empty($selected_courses)) {
            if ($role === 'fresher') {
                foreach ($selected_courses as $course_id) {
                    $stmt = $pdo->prepare("INSERT INTO enrollments (fresher_id, course_id, status) VALUES (?, ?, 'active')");
                    $stmt->execute([$user_id, $course_id]);
                }
            } else {
                foreach ($selected_courses as $course_id) {
                    $stmt = $pdo->prepare("UPDATE courses SET mentor_id = ? WHERE id = ? AND mentor_id IS NULL");
                    $stmt->execute([$user_id, $course_id]);
                }
            }
        }
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Registration successful! Please login.'
        ];
        redirect('login.php');
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => $validator->errorsString()
        ];
        $_SESSION['old'] = $_POST;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SkillShare Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

<!-- Modern Navbar -->
<nav class="navbar-modern" style="position: relative;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <a class="navbar-brand-modern" href="index.php">
                <i class="fas fa-graduation-cap"></i>
                <span>SkillShare Hub</span>
            </a>
            <div>
                <a href="index.php" class="text-muted me-3">Home</a>
                <a href="login.php" class="btn-modern btn-modern-outline">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Registration Form -->
<section class="section-modern register-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-modern">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus" style="font-size: 2.5rem; background: var(--primary-gradient); background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        <h3 style="font-family: var(--font-secondary); font-weight: 700; margin-top: 12px;">Create Account</h3>
                        <p class="text-muted">Start your learning journey today</p>
                    </div>
                    
                    <?php if (isset($_SESSION['alert'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?> alert-dismissible fade show">
                            <i class="fas fa-<?php echo $_SESSION['alert']['icon'] ?? 'info-circle'; ?>"></i>
                            <?php echo $_SESSION['alert']['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['alert']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" id="registerForm" novalidate>
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control-modern" 
                                           placeholder="Enter your full name" 
                                           value="<?php echo $_SESSION['old']['full_name'] ?? ''; ?>" required>
                                    <div class="invalid-feedback" id="fullNameError"></div>
                                </div>
                                
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control-modern" 
                                           placeholder="Enter your email" 
                                           value="<?php echo $_SESSION['old']['email'] ?? ''; ?>" required>
                                    <div class="invalid-feedback" id="emailError"></div>
                                </div>
                                
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control-modern" 
                                           placeholder="Enter your phone number" 
                                           value="<?php echo $_SESSION['old']['phone'] ?? ''; ?>">
                                </div>
                                
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Location</label>
                                    <input type="text" name="location" class="form-control-modern" 
                                           placeholder="City, Country" 
                                           value="<?php echo $_SESSION['old']['location'] ?? ''; ?>">
                                </div>
                                
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Bio / About You</label>
                                    <textarea name="bio" class="form-control-modern" rows="2" 
                                              placeholder="Tell us a bit about yourself"><?php echo $_SESSION['old']['bio'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Interests / Hobbies</label>
                                    <input type="text" name="interests" class="form-control-modern" 
                                           placeholder="e.g., Web Development, Data Science, AI" 
                                           value="<?php echo $_SESSION['old']['interests'] ?? ''; ?>"
                                           id="interestsInput">
                                    <small class="text-muted">Separate interests with commas</small>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Password <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" name="password" class="form-control-modern" id="password" 
                                               placeholder="At least 8 characters" required>
                                        <button type="button" class="btn btn-link position-absolute end-0 top-0 mt-2 me-2" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar" id="passwordStrength" style="width: 0%;"></div>
                                        </div>
                                        <small class="text-muted" id="passwordStrengthText">Weak</small>
                                    </div>
                                    <div class="invalid-feedback" id="passwordError"></div>
                                    <ul class="password-requirements small text-muted mt-1">
                                        <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                                        <li id="req-upper"><i class="fas fa-circle"></i> One uppercase letter</li>
                                        <li id="req-lower"><i class="fas fa-circle"></i> One lowercase letter</li>
                                        <li id="req-number"><i class="fas fa-circle"></i> One number</li>
                                        <li id="req-special"><i class="fas fa-circle"></i> One special character</li>
                                    </ul>
                                </div>
                                
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" name="confirm_password" class="form-control-modern" 
                                           placeholder="Confirm your password" required>
                                    <div class="invalid-feedback" id="confirmPasswordError"></div>
                                </div>
                                
                                <div class="form-group-modern">
                                    <label class="form-label-modern">I am a <span class="text-danger">*</span></label>
                                    <select name="role" class="form-control-modern" id="roleSelect" required>
                                        <option value="">Select...</option>
                                        <option value="fresher" <?php echo (($_SESSION['old']['role'] ?? '') === 'fresher') ? 'selected' : ''; ?>>Fresher / Student</option>
                                        <option value="mentor" <?php echo (($_SESSION['old']['role'] ?? '') === 'mentor') ? 'selected' : ''; ?>>Mentor / Professional</option>
                                    </select>
                                    <div class="invalid-feedback" id="roleError"></div>
                                </div>
                                
                                <div class="form-group-modern" id="academicFieldGroup">
                                    <label class="form-label-modern">Academic Field <span class="text-danger">*</span></label>
                                    <select name="academic_field" class="form-control-modern" id="academicFieldSelect" required>
                                        <option value="">Select your field</option>
                                        <?php foreach ($academic_fields as $field): ?>
                                        <option value="<?php echo $field['id']; ?>" <?php echo (($_SESSION['old']['academic_field'] ?? '') == $field['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($field['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback" id="academicFieldError">Please select your academic field.</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Skills Selection (Checkboxes) - Dynamic based on field -->
                        <div class="form-group-modern" id="skillsGroup">
                            <label class="form-label-modern">Select Your Skills <span class="text-danger">*</span></label>
                            <small class="text-muted d-block mb-2">Select all skills you have or want to learn</small>
                            <div class="skills-search mb-2">
                                <input type="text" class="form-control-modern" id="skillSearch" placeholder="Search skills...">
                            </div>
                            <div class="row g-2 skills-grid" id="skillsGrid">
                                <!-- Skills will be populated dynamically -->
                                <div class="col-12 text-center text-muted py-3" id="skillsLoading">
                                    <i class="fas fa-spinner fa-spin"></i> Loading skills...
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllSkills()">
                                    <i class="fas fa-check-double"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllSkills()">
                                    <i class="fas fa-times"></i> Deselect All
                                </button>
                                <span class="badge bg-primary ms-2" id="selectedSkillCount">0 selected</span>
                            </div>
                        </div>
                        
                        <!-- Course Selection for Fresher (Learn Courses) -->
                        <div class="form-group-modern" id="learnCourseGroup" style="display: none;">
                            <label class="form-label-modern">Select Courses You Want to Learn</label>
                            <small class="text-muted d-block mb-2">Select all courses you're interested in learning</small>
                            <div class="row g-2" id="learnCoursesGrid">
                                <!-- Courses will be populated dynamically -->
                                <div class="col-12 text-center text-muted py-3" id="learnCoursesLoading">
                                    <i class="fas fa-spinner fa-spin"></i> Loading courses...
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllLearnCourses()">
                                    <i class="fas fa-check-double"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllLearnCourses()">
                                    <i class="fas fa-times"></i> Deselect All
                                </button>
                            </div>
                        </div>
                        
                        <!-- Course Selection for Mentor (Teach Courses) -->
                        <div class="form-group-modern" id="teachCourseGroup" style="display: none;">
                            <label class="form-label-modern">Select Courses You Can Teach</label>
                            <small class="text-muted d-block mb-2">Select all courses you have expertise in</small>
                            <div class="row g-2" id="teachCoursesGrid">
                                <!-- Courses will be populated dynamically -->
                                <div class="col-12 text-center text-muted py-3" id="teachCoursesLoading">
                                    <i class="fas fa-spinner fa-spin"></i> Loading courses...
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllTeachCourses()">
                                    <i class="fas fa-check-double"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllTeachCourses()">
                                    <i class="fas fa-times"></i> Deselect All
                                </button>
                            </div>
                        </div>
                        
                        <!-- Terms -->
                        <div class="form-group-modern">
                            <div class="form-check">
                                <input type="checkbox" name="terms" class="form-check-input" id="terms" required
                                       <?php echo isset($_SESSION['old']['terms']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="terms.php" target="_blank">Terms of Service</a> and 
                                    <a href="privacy-policy.php" target="_blank">Privacy Policy</a>
                                </label>
                                <div class="invalid-feedback" id="termsError">You must agree to the terms.</div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-modern btn-modern-primary w-100" style="justify-content: center; padding: 14px; margin-top: 12px;">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 600;">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// JavaScript data for dynamic filtering
const coursesData = <?php echo json_encode($courses_by_field); ?>;
const skillsData = <?php echo json_encode($skills_by_field); ?>;
const allSkills = <?php echo json_encode($all_skills); ?>;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const passwordInput = document.getElementById('password');
    const roleSelect = document.getElementById('roleSelect');
    const learnGroup = document.getElementById('learnCourseGroup');
    const teachGroup = document.getElementById('teachCourseGroup');
    const academicFieldSelect = document.getElementById('academicFieldSelect');
    const skillsGrid = document.getElementById('skillsGrid');
    const learnCoursesGrid = document.getElementById('learnCoursesGrid');
    const teachCoursesGrid = document.getElementById('teachCoursesGrid');
    const skillsLoading = document.getElementById('skillsLoading');
    const learnCoursesLoading = document.getElementById('learnCoursesLoading');
    const teachCoursesLoading = document.getElementById('teachCoursesLoading');
    const selectedSkillCount = document.getElementById('selectedSkillCount');
    
    // Populate skills based on selected academic field
    function populateSkills(fieldId) {
        const skills = skillsData[fieldId] || allSkills;
        skillsGrid.innerHTML = '';
        
        if (skills.length === 0) {
            skillsGrid.innerHTML = '<div class="col-12 text-center text-muted">No skills available for this field.</div>';
            return;
        }
        
        skills.forEach(function(skill) {
            const col = document.createElement('div');
            col.className = 'col-md-3 col-4 skill-item';
            col.innerHTML = `
                <div class="form-check">
                    <input type="checkbox" name="skills[]" class="form-check-input skill-checkbox" 
                           id="skill_${skill.replace(/[^a-zA-Z0-9]/g, '_')}" 
                           value="${skill}">
                    <label class="form-check-label" for="skill_${skill.replace(/[^a-zA-Z0-9]/g, '_')}">
                        ${skill}
                    </label>
                </div>
            `;
            skillsGrid.appendChild(col);
        });
        
        // Re-attach event listeners
        document.querySelectorAll('.skill-checkbox').forEach(function(cb) {
            cb.addEventListener('change', updateSkillCount);
        });
        updateSkillCount();
    }
    
    // Populate courses based on selected academic field
    function populateCourses(fieldId) {
        const courses = coursesData[fieldId] || [];
        
        // Populate learn courses
        learnCoursesGrid.innerHTML = '';
        if (courses.length === 0) {
            learnCoursesGrid.innerHTML = '<div class="col-12 text-center text-muted">No courses available for this field.</div>';
        } else {
            courses.forEach(function(course) {
                const col = document.createElement('div');
                col.className = 'col-md-4';
                col.innerHTML = `
                    <div class="form-check course-check">
                        <input type="checkbox" name="learn_courses[]" class="form-check-input learn-course-checkbox" 
                               id="learn_course_${course.id}" 
                               value="${course.id}">
                        <label class="form-check-label" for="learn_course_${course.id}">
                            ${course.title}
                            <br><small class="text-muted">${course.level.charAt(0).toUpperCase() + course.level.slice(1)}</small>
                        </label>
                    </div>
                `;
                learnCoursesGrid.appendChild(col);
            });
        }
        
        // Populate teach courses
        teachCoursesGrid.innerHTML = '';
        if (courses.length === 0) {
            teachCoursesGrid.innerHTML = '<div class="col-12 text-center text-muted">No courses available for this field.</div>';
        } else {
            courses.forEach(function(course) {
                const col = document.createElement('div');
                col.className = 'col-md-4';
                col.innerHTML = `
                    <div class="form-check course-check">
                        <input type="checkbox" name="teach_courses[]" class="form-check-input teach-course-checkbox" 
                               id="teach_course_${course.id}" 
                               value="${course.id}">
                        <label class="form-check-label" for="teach_course_${course.id}">
                            ${course.title}
                            <br><small class="text-muted">${course.level.charAt(0).toUpperCase() + course.level.slice(1)}</small>
                        </label>
                    </div>
                `;
                teachCoursesGrid.appendChild(col);
            });
        }
    }
    
    // Update skill count
    function updateSkillCount() {
        const selected = document.querySelectorAll('.skill-checkbox:checked').length;
        if (selectedSkillCount) {
            selectedSkillCount.textContent = selected + ' selected';
        }
    }
    
    // Handle academic field change
    academicFieldSelect.addEventListener('change', function() {
        const fieldId = this.value;
        if (fieldId) {
            populateSkills(fieldId);
            populateCourses(fieldId);
        } else {
            skillsGrid.innerHTML = '<div class="col-12 text-center text-muted">Please select an academic field first.</div>';
            learnCoursesGrid.innerHTML = '<div class="col-12 text-center text-muted">Please select an academic field first.</div>';
            teachCoursesGrid.innerHTML = '<div class="col-12 text-center text-muted">Please select an academic field first.</div>';
        }
    });
    
    // Show/hide fields based on role
    roleSelect.addEventListener('change', function() {
        if (this.value === 'fresher') {
            learnGroup.style.display = 'block';
            teachGroup.style.display = 'none';
            document.getElementById('academicFieldGroup').style.display = 'block';
            document.getElementById('skillsGroup').style.display = 'block';
        } else if (this.value === 'mentor') {
            learnGroup.style.display = 'none';
            teachGroup.style.display = 'block';
            document.getElementById('academicFieldGroup').style.display = 'block';
            document.getElementById('skillsGroup').style.display = 'block';
        } else {
            learnGroup.style.display = 'none';
            teachGroup.style.display = 'none';
            document.getElementById('academicFieldGroup').style.display = 'none';
            document.getElementById('skillsGroup').style.display = 'none';
        }
    });
    
    // Trigger on load
    if (roleSelect.value === 'fresher') {
        learnGroup.style.display = 'block';
        teachGroup.style.display = 'none';
        document.getElementById('academicFieldGroup').style.display = 'block';
        document.getElementById('skillsGroup').style.display = 'block';
    } else if (roleSelect.value === 'mentor') {
        learnGroup.style.display = 'none';
        teachGroup.style.display = 'block';
        document.getElementById('academicFieldGroup').style.display = 'block';
        document.getElementById('skillsGroup').style.display = 'block';
    }
    
    // If academic field is pre-selected, load data
    if (academicFieldSelect.value) {
        populateSkills(academicFieldSelect.value);
        populateCourses(academicFieldSelect.value);
    }
    
    // Skill search filter
    document.getElementById('skillSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.skill-item').forEach(function(item) {
            const label = item.querySelector('.form-check-label');
            if (label) {
                const text = label.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            }
        });
    });
    
    // Password toggle
    window.togglePassword = function() {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    };
    
    // Password strength validation
    passwordInput.addEventListener('input', function() {
        validatePasswordStrength(this.value);
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate full name
        const fullName = document.querySelector('input[name="full_name"]');
        if (!fullName.value.trim() || fullName.value.trim().length < 2) {
            fullName.classList.add('is-invalid');
            document.getElementById('fullNameError').textContent = 'Full name must be at least 2 characters.';
            isValid = false;
        } else {
            fullName.classList.remove('is-invalid');
        }
        
        // Validate email
        const email = document.querySelector('input[name="email"]');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            email.classList.add('is-invalid');
            document.getElementById('emailError').textContent = 'Please enter a valid email address.';
            isValid = false;
        } else {
            email.classList.remove('is-invalid');
        }
        
        // Validate password
        const password = passwordInput.value;
        if (!validatePasswordStrength(password)) {
            passwordInput.classList.add('is-invalid');
            document.getElementById('passwordError').textContent = 'Password does not meet requirements.';
            isValid = false;
        } else {
            passwordInput.classList.remove('is-invalid');
        }
        
        // Validate confirm password
        const confirmPassword = document.querySelector('input[name="confirm_password"]');
        if (confirmPassword.value !== password) {
            confirmPassword.classList.add('is-invalid');
            document.getElementById('confirmPasswordError').textContent = 'Passwords do not match.';
            isValid = false;
        } else {
            confirmPassword.classList.remove('is-invalid');
        }
        
        // Validate role
        const role = document.querySelector('select[name="role"]');
        if (!role.value) {
            role.classList.add('is-invalid');
            document.getElementById('roleError').textContent = 'Please select a role.';
            isValid = false;
        } else {
            role.classList.remove('is-invalid');
        }
        
        // Validate academic field
        const academicField = document.querySelector('select[name="academic_field"]');
        if (!academicField.value) {
            academicField.classList.add('is-invalid');
            document.getElementById('academicFieldError').textContent = 'Please select an academic field.';
            isValid = false;
        } else {
            academicField.classList.remove('is-invalid');
        }
        
        // Validate skills
        const selectedSkills = document.querySelectorAll('.skill-checkbox:checked');
        if (selectedSkills.length === 0) {
            document.getElementById('skillsGroup').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('skillsGroup').classList.remove('is-invalid');
        }
        
        // Validate terms
        const terms = document.getElementById('terms');
        if (!terms.checked) {
            terms.classList.add('is-invalid');
            document.getElementById('termsError').textContent = 'You must agree to the terms.';
            isValid = false;
        } else {
            terms.classList.remove('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});

function validatePasswordStrength(password) {
    const requirements = {
        length: password.length >= 8,
        upper: /[A-Z]/.test(password),
        lower: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };
    
    document.getElementById('req-length').innerHTML = 
        `<i class="fas fa-${requirements.length ? 'check-circle text-success' : 'circle'}"></i> At least 8 characters`;
    document.getElementById('req-upper').innerHTML = 
        `<i class="fas fa-${requirements.upper ? 'check-circle text-success' : 'circle'}"></i> One uppercase letter`;
    document.getElementById('req-lower').innerHTML = 
        `<i class="fas fa-${requirements.lower ? 'check-circle text-success' : 'circle'}"></i> One lowercase letter`;
    document.getElementById('req-number').innerHTML = 
        `<i class="fas fa-${requirements.number ? 'check-circle text-success' : 'circle'}"></i> One number`;
    document.getElementById('req-special').innerHTML = 
        `<i class="fas fa-${requirements.special ? 'check-circle text-success' : 'circle'}"></i> One special character`;
    
    const score = Object.values(requirements).filter(Boolean).length;
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    if (score === 0) {
        strengthBar.style.width = '0%';
        strengthBar.className = 'progress-bar bg-danger';
        strengthText.textContent = 'Very Weak';
    } else if (score <= 2) {
        strengthBar.style.width = '25%';
        strengthBar.className = 'progress-bar bg-danger';
        strengthText.textContent = 'Weak';
    } else if (score <= 3) {
        strengthBar.style.width = '50%';
        strengthBar.className = 'progress-bar bg-warning';
        strengthText.textContent = 'Fair';
    } else if (score <= 4) {
        strengthBar.style.width = '75%';
        strengthBar.className = 'progress-bar bg-info';
        strengthText.textContent = 'Good';
    } else {
        strengthBar.style.width = '100%';
        strengthBar.className = 'progress-bar bg-success';
        strengthText.textContent = 'Strong';
    }
    
    return score === 5;
}

// Select/Deselect functions
function selectAllSkills() {
    document.querySelectorAll('.skill-checkbox').forEach(cb => cb.checked = true);
    document.querySelectorAll('.skill-item').forEach(item => item.style.display = 'block');
    updateSkillCount();
}

function deselectAllSkills() {
    document.querySelectorAll('.skill-checkbox').forEach(cb => cb.checked = false);
    updateSkillCount();
}

function selectAllLearnCourses() {
    document.querySelectorAll('.learn-course-checkbox').forEach(cb => cb.checked = true);
}

function deselectAllLearnCourses() {
    document.querySelectorAll('.learn-course-checkbox').forEach(cb => cb.checked = false);
}

function selectAllTeachCourses() {
    document.querySelectorAll('.teach-course-checkbox').forEach(cb => cb.checked = true);
}

function deselectAllTeachCourses() {
    document.querySelectorAll('.teach-course-checkbox').forEach(cb => cb.checked = false);
}

function updateSkillCount() {
    const selected = document.querySelectorAll('.skill-checkbox:checked').length;
    const countEl = document.getElementById('selectedSkillCount');
    if (countEl) {
        countEl.textContent = selected + ' selected';
    }
}
</script>

<style>
.form-check-label small {
    font-size: 0.7rem;
}

.invalid-feedback {
    display: block;
}

.form-control-modern.is-invalid {
    border-color: #dc3545;
}

.form-check-input.is-invalid {
    border-color: #dc3545;
}

#skillsGroup {
    background: rgba(108, 99, 255, 0.05);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid rgba(108, 99, 255, 0.1);
}

#skillsGroup.is-invalid {
    border-color: #dc3545;
    background: rgba(220, 53, 69, 0.05);
}

#learnCourseGroup, #teachCourseGroup {
    background: rgba(81, 207, 102, 0.05);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid rgba(81, 207, 102, 0.1);
}

.skill-item {
    transition: all 0.3s ease;
}

.skill-item:hover {
    transform: scale(1.02);
}

.skill-search {
    position: relative;
}

.skill-search input {
    padding-left: 36px;
}

.skill-search::before {
    content: '\f002';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #adb5bd;
}

.course-check {
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.05);
}

.course-check:hover {
    background: rgba(108, 99, 255, 0.05);
    transform: translateX(4px);
}

#academicFieldGroup {
    transition: all 0.3s ease;
}

#academicFieldGroup.is-invalid select {
    border-color: #dc3545;
}
</style>

<?php include 'includes/footer.php'; ?>