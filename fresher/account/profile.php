<?php
$page_title = 'My Profile';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $bio = sanitize($_POST['bio']);
    $title = sanitize($_POST['title']);
    $phone = sanitize($_POST['phone']);
    $location = sanitize($_POST['location']);
    $website = sanitize($_POST['website']);
    $interests = sanitize($_POST['interests']);
    
    // Validation
    $errors = [];
    if (strlen($full_name) < 2) $errors[] = 'Full name must be at least 2 characters.';
    if (!empty($phone) && !preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $phone)) {
        $errors[] = 'Please enter a valid phone number.';
    }
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        $errors[] = 'Please enter a valid URL.';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET 
                               full_name = ?, bio = ?, title = ?, phone = ?, 
                               location = ?, website = ?, interests = ? 
                               WHERE id = ?");
        $stmt->execute([$full_name, $bio, $title, $phone, $location, $website, $interests, $user_id]);
        
        $_SESSION['full_name'] = $full_name;
        
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Profile updated successfully!'
        ];
        redirect('profile.php');
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => implode('<br>', $errors)
        ];
    }
}

// Handle avatar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['avatar']['type'], $allowed_types)) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'message' => 'Please upload a valid image (JPEG, PNG, GIF, WEBP).'
            ];
            redirect('profile.php');
        }
        
        if ($_FILES['avatar']['size'] > $max_size) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'message' => 'Image size must be less than 2MB.'
            ];
            redirect('profile.php');
        }
        
        $upload_dir = '../../uploads/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_name = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
            // Delete old avatar if exists
            if ($user['avatar'] && file_exists('../../' . $user['avatar'])) {
                unlink('../../' . $user['avatar']);
            }
            
            $avatar_path = 'uploads/profiles/' . $file_name;
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$avatar_path, $user_id]);
            
            $_SESSION['alert'] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'message' => 'Profile picture updated successfully!'
            ];
            redirect('profile.php');
        }
    }
}

// Handle social links update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_social'])) {
    $social_links = [
        'facebook' => sanitize($_POST['facebook'] ?? ''),
        'twitter' => sanitize($_POST['twitter'] ?? ''),
        'linkedin' => sanitize($_POST['linkedin'] ?? ''),
        'youtube' => sanitize($_POST['youtube'] ?? ''),
        'instagram' => sanitize($_POST['instagram'] ?? ''),
        'github' => sanitize($_POST['github'] ?? '')
    ];
    
    // Remove empty values
    $social_links = array_filter($social_links);
    
    $stmt = $pdo->prepare("UPDATE users SET social_links = ? WHERE id = ?");
    $stmt->execute([json_encode($social_links), $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Social links updated successfully!'
    ];
    redirect('profile.php');
}

// Get social links
$social_links = $user['social_links'] ? json_decode($user['social_links'], true) : [];
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
                <h1 class="h2">My Profile</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <div class="row">
                <!-- Left Column - Avatar & Stats -->
                <div class="col-lg-4">
                    <!-- Avatar Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body text-center p-4">
                            <div class="position-relative d-inline-block">
                                <img src="<?php echo getAvatar($user); ?>" 
                                     class="rounded-circle border border-3 border-primary" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                <button class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0" 
                                        onclick="document.getElementById('avatarInput').click()" 
                                        style="width: 36px; height: 36px;">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <h5 class="mt-3"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($user['title'] ?? 'Student'); ?></p>
                            <p class="text-muted small">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?>
                            </p>
                            <?php if ($user['is_verified']): ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Verified Account</span>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data" class="d-none">
                                <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="this.form.submit()">
                                <button type="submit" name="upload_avatar"></button>
                            </form>
                            
                            <div class="mt-3">
                                <small class="text-muted">Member since <?php echo formatDate($user['created_at']); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="mb-3">Learning Statistics</h6>
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE fresher_id = ? AND status = 'active'");
                            $stmt->execute([$user_id]);
                            $active_courses = $stmt->fetchColumn();
                            
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE fresher_id = ? AND status = 'completed'");
                            $stmt->execute([$user_id]);
                            $completed_courses = $stmt->fetchColumn();
                            
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings b JOIN sessions s ON b.session_id = s.id WHERE b.fresher_id = ? AND b.status = 'approved'");
                            $stmt->execute([$user_id]);
                            $total_sessions = $stmt->fetchColumn();
                            
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM certificates WHERE fresher_id = ? AND is_valid = 1");
                            $stmt->execute([$user_id]);
                            $certificates = $stmt->fetchColumn();
                            ?>
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <h4 class="text-primary"><?php echo $active_courses; ?></h4>
                                    <small class="text-muted">Active Courses</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-success"><?php echo $completed_courses; ?></h4>
                                    <small class="text-muted">Completed</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-info"><?php echo $total_sessions; ?></h4>
                                    <small class="text-muted">Sessions Attended</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-warning"><?php echo $certificates; ?></h4>
                                    <small class="text-muted">Certificates</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Profile Forms -->
                <div class="col-lg-8">
                    <!-- Profile Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-user-edit text-primary"></i> Profile Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Title / Headline</label>
                                        <input type="text" name="title" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['title'] ?? ''); ?>" 
                                               placeholder="e.g., Student, Developer, Designer">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Bio</label>
                                        <textarea name="bio" class="form-control" rows="3" 
                                                  placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Interests</label>
                                        <textarea name="interests" class="form-control" rows="2" 
                                                  placeholder="e.g., Web Development, Data Science, AI, Design"><?php echo htmlspecialchars($user['interests'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" name="phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                               placeholder="+1 (555) 123-4567">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="location" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" 
                                               placeholder="City, Country">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Website</label>
                                        <input type="url" name="website" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>" 
                                               placeholder="https://yourwebsite.com">
                                    </div>
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Social Links -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-share-alt text-primary"></i> Social Links</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fab fa-facebook text-primary"></i> Facebook</label>
                                        <input type="url" name="facebook" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['facebook'] ?? ''); ?>" 
                                               placeholder="https://facebook.com/username">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fab fa-twitter text-info"></i> Twitter</label>
                                        <input type="url" name="twitter" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['twitter'] ?? ''); ?>" 
                                               placeholder="https://twitter.com/username">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fab fa-linkedin text-primary"></i> LinkedIn</label>
                                        <input type="url" name="linkedin" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['linkedin'] ?? ''); ?>" 
                                               placeholder="https://linkedin.com/in/username">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fab fa-github text-dark"></i> GitHub</label>
                                        <input type="url" name="github" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['github'] ?? ''); ?>" 
                                               placeholder="https://github.com/username">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fab fa-youtube text-danger"></i> YouTube</label>
                                        <input type="url" name="youtube" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['youtube'] ?? ''); ?>" 
                                               placeholder="https://youtube.com/c/username">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fab fa-instagram text-danger"></i> Instagram</label>
                                        <input type="url" name="instagram" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['instagram'] ?? ''); ?>" 
                                               placeholder="https://instagram.com/username">
                                    </div>
                                </div>
                                
                                <button type="submit" name="update_social" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Social Links
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Account Info -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-info-circle text-primary"></i> Account Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                    <p><strong>Role:</strong> <?php echo ucfirst($user['role']); ?></p>
                                    <p><strong>Account Status:</strong> 
                                        <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Member Since:</strong> <?php echo formatDateTime($user['created_at']); ?></p>
                                    <p><strong>Last Updated:</strong> <?php echo formatDateTime($user['updated_at']); ?></p>
                                    <?php if ($user['last_login']): ?>
                                        <p><strong>Last Login:</strong> <?php echo formatDateTime($user['last_login']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('avatarInput').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('.rounded-circle');
            img.src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>

<?php include '../../includes/footer.php'; ?>