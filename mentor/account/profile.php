<?php
$page_title = 'My Profile';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    $bio = sanitize($_POST['bio']);
    $title = sanitize($_POST['title']);
    $skills = sanitize($_POST['skills']);
    $phone = sanitize($_POST['phone']);
    $location = sanitize($_POST['location']);
    $website = sanitize($_POST['website']);
    
    $stmt = $pdo->prepare("UPDATE users SET 
                           full_name = ?, bio = ?, title = ?, skills = ?, 
                           phone = ?, location = ?, website = ? 
                           WHERE id = ?");
    $stmt->execute([$full_name, $bio, $title, $skills, $phone, $location, $website, $user_id]);
    
    $_SESSION['full_name'] = $full_name;
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Profile updated successfully!'
    ];
    redirect('profile.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/profiles/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_name = 'mentor_' . $user_id . '_' . time() . '.' . $ext;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
            if ($user['avatar'] && file_exists('../../' . $user['avatar'])) {
                unlink('../../' . $user['avatar']);
            }
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute(['uploads/profiles/' . $file_name, $user_id]);
            
            $_SESSION['alert'] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'message' => 'Profile picture updated!'
            ];
            redirect('profile.php');
        }
    }
}

// Get social links
$social_links = $user['social_links'] ? json_decode($user['social_links'], true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_social'])) {
    $social_links = [
        'facebook' => sanitize($_POST['facebook'] ?? ''),
        'twitter' => sanitize($_POST['twitter'] ?? ''),
        'linkedin' => sanitize($_POST['linkedin'] ?? ''),
        'youtube' => sanitize($_POST['youtube'] ?? ''),
        'instagram' => sanitize($_POST['instagram'] ?? ''),
        'github' => sanitize($_POST['github'] ?? '')
    ];
    $social_links = array_filter($social_links);
    
    $stmt = $pdo->prepare("UPDATE users SET social_links = ? WHERE id = ?");
    $stmt->execute([json_encode($social_links), $user_id]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Social links updated!'
    ];
    redirect('profile.php');
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
                <h1 class="h2">My Profile</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4">
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
                            <p class="text-muted"><?php echo htmlspecialchars($user['title'] ?? 'Mentor'); ?></p>
                            <?php if ($user['is_verified']): ?>
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Verified</span>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data" class="d-none">
                                <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="this.form.submit()">
                                <button type="submit" name="upload_avatar"></button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h6>Mentor Statistics</h6>
                            <?php
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE mentor_id = ? AND status = 'active'");
                            $stmt->execute([$user_id]);
                            $courses = $stmt->fetchColumn();
                            
                            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT fresher_id) FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.mentor_id = ?");
                            $stmt->execute([$user_id]);
                            $students = $stmt->fetchColumn();
                            
                            $stmt = $pdo->prepare("SELECT AVG(rating) FROM ratings WHERE mentor_id = ?");
                            $stmt->execute([$user_id]);
                            $rating = $stmt->fetchColumn();
                            ?>
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="text-primary"><?php echo $courses; ?></h4>
                                    <small class="text-muted">Courses</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-success"><?php echo $students; ?></h4>
                                    <small class="text-muted">Students</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-warning"><?php echo number_format($rating ?: 0, 1); ?></h4>
                                    <small class="text-muted">Rating</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5>Profile Information</h5>
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="full_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Title / Headline</label>
                                        <input type="text" name="title" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['title'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Bio</label>
                                        <textarea name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Skills & Expertise</label>
                                        <textarea name="skills" class="form-control" rows="2" 
                                                  placeholder="PHP, JavaScript, React, Python..."><?php echo htmlspecialchars($user['skills'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" name="phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="location" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Website</label>
                                        <input type="url" name="website" class="form-control" 
                                               value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>">
                                    </div>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>Social Links</h5>
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-facebook text-primary"></i> Facebook</label>
                                        <input type="url" name="facebook" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['facebook'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-twitter text-info"></i> Twitter</label>
                                        <input type="url" name="twitter" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['twitter'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-linkedin text-primary"></i> LinkedIn</label>
                                        <input type="url" name="linkedin" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['linkedin'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label><i class="fab fa-github"></i> GitHub</label>
                                        <input type="url" name="github" class="form-control" 
                                               value="<?php echo htmlspecialchars($social_links['github'] ?? ''); ?>">
                                    </div>
                                </div>
                                <button type="submit" name="update_social" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Social Links
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