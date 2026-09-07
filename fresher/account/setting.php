<?php
$page_title = 'Settings';
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

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate
    $errors = [];
    if (empty($current_password)) $errors[] = 'Current password is required.';
    if (strlen($new_password) < 8) $errors[] = 'New password must be at least 8 characters.';
    if (!preg_match('/[A-Z]/', $new_password)) $errors[] = 'Password must contain at least one uppercase letter.';
    if (!preg_match('/[a-z]/', $new_password)) $errors[] = 'Password must contain at least one lowercase letter.';
    if (!preg_match('/[0-9]/', $new_password)) $errors[] = 'Password must contain at least one number.';
    if (!preg_match('/[^A-Za-z0-9]/', $new_password)) $errors[] = 'Password must contain at least one special character.';
    if ($new_password !== $confirm_password) $errors[] = 'New passwords do not match.';
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch();
        
        if (password_verify($current_password, $user_data['password'])) {
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
                'message' => 'Current password is incorrect.'
            ];
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => implode('<br>', $errors)
        ];
    }
    redirect('setting.php');
}

// Handle notification preferences
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_preferences'])) {
    $preferences = [
        'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
        'session_reminders' => isset($_POST['session_reminders']) ? 1 : 0,
        'course_updates' => isset($_POST['course_updates']) ? 1 : 0,
        'marketing_emails' => isset($_POST['marketing_emails']) ? 1 : 0,
        'assignment_reminders' => isset($_POST['assignment_reminders']) ? 1 : 0
    ];
    
    // Save to settings table
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) 
                           VALUES (?, ?, 'notifications') 
                           ON DUPLICATE KEY UPDATE setting_value = ?");
    
    foreach ($preferences as $key => $value) {
        $stmt->execute([$key, $value, $value]);
    }
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Preferences updated successfully!'
    ];
    redirect('setting.php');
}

// Get current preferences
$preferences = [];
$stmt = $pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'notifications'");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $preferences[$row['setting_key']] = $row['setting_value'];
}

// Handle account deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deactivate_account'])) {
    $reason = sanitize($_POST['deactivate_reason'] ?? '');
    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
    $stmt->execute([$user_id]);
    
    // Log deactivation
    $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, description, data) 
                           VALUES (?, 'deactivate', ?, ?)");
    $stmt->execute([$user_id, 'Account deactivated', json_encode(['reason' => $reason])]);
    
    $_SESSION = [];
    session_destroy();
    header('Location: ../../login.php?deactivated=1');
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
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-4">
                    <!-- Quick Navigation -->
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="list-group list-group-flush">
                            <a href="#security" class="list-group-item list-group-item-action active">
                                <i class="fas fa-shield-alt"></i> Security
                            </a>
                            <a href="#notifications" class="list-group-item list-group-item-action">
                                <i class="fas fa-bell"></i> Notifications
                            </a>
                            <a href="#privacy" class="list-group-item list-group-item-action">
                                <i class="fas fa-lock"></i> Privacy
                            </a>
                            <a href="#account" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-cog"></i> Account
                            </a>
                            <a href="#danger" class="list-group-item list-group-item-action text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Danger Zone
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="col-lg-8">
                    <!-- Security Section -->
                    <div id="security" class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-shield-alt text-primary"></i> Security</h5>
                        </div>
                        <div class="card-body p-4">
                            <h6>Change Password</h6>
                            <p class="text-muted small">Choose a strong password with at least 8 characters, including uppercase, lowercase, numbers, and special characters.</p>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" class="form-control" id="currentPassword" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="currentPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="new_password" class="form-control" id="newPassword" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="newPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar" id="passwordStrength" style="width: 0%;"></div>
                                        </div>
                                        <small class="text-muted" id="passwordStrengthText">Password strength</small>
                                    </div>
                                    <ul class="password-requirements small text-muted mt-1">
                                        <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                                        <li id="req-upper"><i class="fas fa-circle"></i> One uppercase letter</li>
                                        <li id="req-lower"><i class="fas fa-circle"></i> One lowercase letter</li>
                                        <li id="req-number"><i class="fas fa-circle"></i> One number</li>
                                        <li id="req-special"><i class="fas fa-circle"></i> One special character</li>
                                    </ul>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="confirm_password" class="form-control" id="confirmPassword" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <button type="submit" name="change_password" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Notifications Section -->
                    <div id="notifications" class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-bell text-primary"></i> Notification Preferences</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" 
                                           <?php echo ($preferences['email_notifications'] ?? 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="emailNotifications">
                                        <strong>Email Notifications</strong>
                                        <br><small class="text-muted">Receive notifications via email</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="session_reminders" id="sessionReminders" 
                                           <?php echo ($preferences['session_reminders'] ?? 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="sessionReminders">
                                        <strong>Session Reminders</strong>
                                        <br><small class="text-muted">Get reminders before upcoming sessions</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="course_updates" id="courseUpdates" 
                                           <?php echo ($preferences['course_updates'] ?? 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="courseUpdates">
                                        <strong>Course Updates</strong>
                                        <br><small class="text-muted">Get notified about course updates and new content</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="assignment_reminders" id="assignmentReminders" 
                                           <?php echo ($preferences['assignment_reminders'] ?? 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="assignmentReminders">
                                        <strong>Assignment Reminders</strong>
                                        <br><small class="text-muted">Get reminders for upcoming assignment deadlines</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="marketing_emails" id="marketingEmails" 
                                           <?php echo ($preferences['marketing_emails'] ?? 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="marketingEmails">
                                        <strong>Marketing Emails</strong>
                                        <br><small class="text-muted">Receive promotional offers and newsletters</small>
                                    </label>
                                </div>
                                
                                <button type="submit" name="update_preferences" class="btn btn-primary mt-3">
                                    <i class="fas fa-save"></i> Save Preferences
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Privacy Section -->
                    <div id="privacy" class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-lock text-primary"></i> Privacy Settings</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label">Profile Visibility</label>
                                <select class="form-select">
                                    <option value="public">Public - Visible to everyone</option>
                                    <option value="mentors" selected>Mentors Only - Visible to mentors</option>
                                    <option value="private">Private - Visible only to you</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Show Email in Profile</label>
                                <select class="form-select">
                                    <option value="yes">Yes, show my email</option>
                                    <option value="no" selected>No, hide my email</option>
                                </select>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="showProgress" checked>
                                <label class="form-check-label" for="showProgress">
                                    Show my course progress to mentors
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="allowMessages" checked>
                                <label class="form-check-label" for="allowMessages">
                                    Allow mentors to send me messages
                                </label>
                            </div>
                            
                            <button class="btn btn-primary mt-3" onclick="savePrivacy()">
                                <i class="fas fa-save"></i> Save Privacy Settings
                            </button>
                        </div>
                    </div>
                    
                    <!-- Danger Zone -->
                    <div id="danger" class="card border border-danger shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h5>
                        </div>
                        <div class="card-body p-4">
                            <h6>Deactivate Account</h6>
                            <p class="text-muted small">Deactivating your account will temporarily hide your profile and suspend your access. Your data will be preserved and can be reactivated by contacting support.</p>
                            
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                                <i class="fas fa-user-slash"></i> Deactivate Account
                            </button>
                            
                            <hr>
                            
                            <h6>Delete Account</h6>
                            <p class="text-muted small text-danger">Permanently delete your account and all associated data. This action cannot be undone.</p>
                            
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash-alt"></i> Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate Modal -->
<div class="modal fade" id="deactivateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deactivate Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <p>Are you sure you want to deactivate your account?</p>
                    <p class="text-muted small">Your profile will be hidden and you won't be able to access your courses until reactivation.</p>
                    <div class="mb-3">
                        <label class="form-label">Reason (Optional)</label>
                        <textarea name="deactivate_reason" class="form-control" rows="3" 
                                  placeholder="Tell us why you're leaving..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="deactivate_account" class="btn btn-danger">Deactivate Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="delete-account.php">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Warning:</strong> This action is permanent and cannot be undone.
                    </div>
                    <p>All your data including courses, certificates, and progress will be permanently deleted.</p>
                    <div class="mb-3">
                        <label class="form-label">Type "DELETE" to confirm</label>
                        <input type="text" name="confirm_delete" class="form-control" placeholder="Type DELETE" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_account" class="btn btn-danger" id="deleteBtn" disabled>
                        <i class="fas fa-trash-alt"></i> Permanently Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Password strength checker
document.getElementById('newPassword').addEventListener('input', function() {
    const password = this.value;
    const requirements = {
        length: password.length >= 8,
        upper: /[A-Z]/.test(password),
        lower: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };
    
    // Update requirements list
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
    
    // Calculate strength
    const score = Object.values(requirements).filter(Boolean).length;
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    const strengths = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['#dc3545', '#dc3545', '#ffc107', '#17a2b8', '#28a745'];
    
    strengthBar.style.width = (score / 5 * 100) + '%';
    strengthBar.style.background = colors[score];
    strengthText.textContent = strengths[score] || 'Very Weak';
});

// Password visibility toggle
document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function() {
        const targetId = this.dataset.target;
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

// Delete confirmation
document.querySelector('input[name="confirm_delete"]').addEventListener('input', function() {
    const deleteBtn = document.getElementById('deleteBtn');
    deleteBtn.disabled = this.value !== 'DELETE';
});

// Save privacy settings
function savePrivacy() {
    showNotification('Privacy settings saved successfully!', 'success');
}

// Smooth scroll to section
document.querySelectorAll('.list-group-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>