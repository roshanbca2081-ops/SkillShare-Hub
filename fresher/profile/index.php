<?php
$page_title = 'Profile';
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
    $interests = sanitize($_POST['interests']);
    $phone = sanitize($_POST['phone']);
    $location = sanitize($_POST['location']);
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, bio = ?, interests = ?, phone = ?, location = ? WHERE id = ?");
    $stmt->execute([$full_name, $bio, $interests, $phone, $location, $user_id]);
    
    $_SESSION['full_name'] = $full_name;
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Profile updated successfully!'
    ];
    redirect('index.php');
}

// Handle avatar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_name = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
            $avatar_path = 'uploads/profiles/' . $file_name;
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$avatar_path, $user_id]);
            
            $_SESSION['alert'] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'message' => 'Avatar updated successfully!'
            ];
            redirect('index.php');
        }
    }
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
                <h1 class="h2">My Profile</h1>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
                    <!-- Avatar -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body text-center p-4">
                            <img src="<?php echo getAvatar($user); ?>" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <h5><?php echo htmlspecialchars($user['full_name']); ?></h5>
                            <p class="text-muted"><?php echo ucfirst($user['role']); ?></p>
                            <?php if ($user['is_verified']): ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Verified</span>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data" class="mt-3">
                                <div class="mb-2">
                                    <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <button type="submit" name="upload_avatar" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-upload"></i> Update Avatar
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6>Statistics</h6>
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE fresher_id = ? AND status = 'active'");
                            $stmt->execute([$user_id]);
                            $active = $stmt->fetchColumn();
                            
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE fresher_id = ? AND status = 'completed'");
                            $stmt->execute([$user_id]);
                            $completed = $stmt->fetchColumn();
                            
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE fresher_id = ? AND status = 'approved'");
                            $stmt->execute([$user_id]);
                            $sessions = $stmt->fetchColumn();
                            ?>
                            <div class="row text-center">
                                <div class="col-4">
                                    <h5><?php echo $active; ?></h5>
                                    <small class="text-muted">Active Courses</small>
                                </div>
                                <div class="col-4">
                                    <h5><?php echo $completed; ?></h5>
                                    <small class="text-muted">Completed</small>
                                </div>
                                <div class="col-4">
                                    <h5><?php echo $sessions; ?></h5>
                                    <small class="text-muted">Sessions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <!-- Profile Form -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>Edit Profile</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Bio</label>
                                    <textarea name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Interests</label>
                                    <textarea name="interests" class="form-control" rows="2" placeholder="e.g. Web Development, Data Science, AI"><?php echo htmlspecialchars($user['interests'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profile
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