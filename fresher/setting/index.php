<?php
$page_title = 'Settings';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 8) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $user_id]);
                
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'icon' => 'check-circle',
                    'message' => 'Password changed successfully!'
                ];
            } else {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'icon' => 'exclamation-circle',
                    'message' => 'Password must be at least 8 characters.'
                ];
            }
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'message' => 'New passwords do not match.'
            ];
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Current password is incorrect.'
        ];
    }
    redirect('index.php');
}

// Handle account deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deactivate'])) {
    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
    $stmt->execute([$user_id]);
    
    session_destroy();
    header('Location: ../../login.php');
    exit();
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
                <h1 class="h2">Settings</h1>
            </div>
            
            <div class="row">
                <div class="col-lg-6">
                    <!-- Change Password -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5>Change Password</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                    <small class="text-muted">Must be at least 8 characters</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <!-- Notification Settings -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5>Notification Preferences</h5>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                <label class="form-check-label" for="emailNotifications">Email Notifications</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="sessionReminders" checked>
                                <label class="form-check-label" for="sessionReminders">Session Reminders</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="courseUpdates" checked>
                                <label class="form-check-label" for="courseUpdates">Course Updates</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="marketingEmails">
                                <label class="form-check-label" for="marketingEmails">Marketing Emails</label>
                            </div>
                            <button class="btn btn-primary mt-3" onclick="savePreferences()">
                                <i class="fas fa-save"></i> Save Preferences
                            </button>
                        </div>
                    </div>
                    
                    <!-- Account Management -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>Account Management</h5>
                            <p class="text-muted">Deactivate your account temporarily or permanently.</p>
                            <form method="POST" onsubmit="return confirm('Are you sure you want to deactivate your account? This action can be reversed by contacting support.')">
                                <button type="submit" name="deactivate" class="btn btn-danger">
                                    <i class="fas fa-user-slash"></i> Deactivate Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function savePreferences() {
    showNotification('Preferences saved successfully!', 'success');
}
</script>

<?php include '../../includes/footer.php'; ?>