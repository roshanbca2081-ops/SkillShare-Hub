<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Admin Profile';
$adminUser = getAdminUser();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_csrf_check();
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $fullName = sanitize($_POST['full_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        
        if (empty($fullName) || empty($email)) {
            $error = 'Name and email are required';
        } else {
            $pdo = getDB();
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$fullName, $email, $phone, getUserId()]);
            $message = 'Profile updated successfully!';
        }
    }
    
    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'All password fields are required';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            $pdo = getDB();
            $user = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $user->execute([getUserId()]);
            $userData = $user->fetch();
            
            if (!password_verify($currentPassword, $userData['password_hash'])) {
                $error = 'Current password is incorrect';
            } else {
                $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$newHash, getUserId()]);
                $message = 'Password changed successfully!';
            }
        }
    }
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Admin Profile</h3>
    </div>

    <?php if ($message): ?>
    <div class="admin-card" style="padding:16px 20px;background:#dcfce7;border:1px solid #bbf7d0;margin-bottom:24px;border-radius:12px;color:#16a34a;">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="admin-card" style="padding:16px 20px;background:#fee2e2;border:1px solid #fecaca;margin-bottom:24px;border-radius:12px;color:#dc2626;">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h5><i class="fas fa-user" style="color:#3b82f6;"></i> Profile Information</h5>
        </div>
        <div class="admin-card-body" style="padding:20px;">
            <form method="POST">
                <?php echo admin_csrf_field(); ?>
                <input type="hidden" name="action" value="update_profile">
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Full Name</label>
                        <input type="text" name="full_name" class="admin-form-control" value="<?php echo htmlspecialchars($adminUser['full_name']); ?>" required>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Email</label>
                        <input type="email" name="email" class="admin-form-control" value="<?php echo htmlspecialchars($adminUser['email']); ?>" required>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Phone</label>
                    <input type="text" name="phone" class="admin-form-control" value="<?php echo htmlspecialchars($adminUser['phone'] ?? ''); ?>">
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Role</label>
                        <input type="text" class="admin-form-control" value="<?php echo htmlspecialchars($adminUser['role']); ?>" disabled>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Status</label>
                        <input type="text" class="admin-form-control" value="<?php echo htmlspecialchars($adminUser['status']); ?>" disabled>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Member Since</label>
                    <input type="text" class="admin-form-control" value="<?php echo admin_format_date($adminUser['created_at']); ?>" disabled>
                </div>
                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Update Profile</button>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h5><i class="fas fa-lock" style="color:#ef4444;"></i> Change Password</h5>
        </div>
        <div class="admin-card-body" style="padding:20px;">
            <form method="POST">
                <?php echo admin_csrf_field(); ?>
                <input type="hidden" name="action" value="change_password">
                <div class="admin-form-group">
                    <label class="admin-form-label">Current Password</label>
                    <input type="password" name="current_password" class="admin-form-control" required>
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-form-label">New Password</label>
                        <input type="password" name="new_password" class="admin-form-control" required minlength="6">
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="admin-form-control" required minlength="6">
                    </div>
                </div>
                <button type="submit" class="admin-btn admin-btn-danger"><i class="fas fa-key"></i> Change Password</button>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

