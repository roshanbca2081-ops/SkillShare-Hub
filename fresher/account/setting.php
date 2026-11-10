<?php
$page_title = 'Settings';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';
require_once '../../config/user-preferences.php';

requireFresher();

$user_id = getUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'Your session token expired. Please reload settings and try again.'];
    redirect('setting.php');
}

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
    saveUserPreferences($pdo, $user_id, $preferences);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Preferences updated successfully!'
    ];
    redirect('setting.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_privacy'])) {
    $visibility = (string)($_POST['profile_visibility'] ?? 'mentors');
    if (!in_array($visibility, ['public', 'mentors', 'private'], true)) {
        $visibility = 'mentors';
    }
    saveUserPreferences($pdo, $user_id, [
        'profile_visibility' => $visibility,
        'show_email' => isset($_POST['show_email']) ? 1 : 0,
        'show_progress' => isset($_POST['show_progress']) ? 1 : 0,
        'allow_messages' => isset($_POST['allow_messages']) ? 1 : 0,
    ]);
    $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Privacy preferences saved.'];
    redirect('setting.php#privacy');
}

// Get current preferences
$preferences = getUserPreferences($pdo, $user_id);
$stmt = $pdo->prepare("SELECT created_at, ip_address, description FROM system_logs WHERE user_id = ? AND action = 'login' ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$login_history = $stmt->fetchAll();

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

// Get user theme preference
$theme_preference = getUserThemePreference($pdo, $user_id);

// Handle theme preference
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_theme'])) {
    $theme = (string)($_POST['theme_preference'] ?? 'system');
    if (!in_array($theme, ['light', 'dark', 'system'], true)) {
        $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'Choose a valid theme.'];
        redirect('setting.php');
    }
    saveUserThemePreference($pdo, $user_id, $theme);
    $theme_preference = $theme;
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Theme preference updated successfully!'
    ];
    redirect('setting.php');
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
                            <a href="#theme" class="list-group-item list-group-item-action active">
                                <i class="fas fa-palette"></i> Theme
                            </a>
                            <a href="#security" class="list-group-item list-group-item-action">
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
                    <!-- Theme Section -->
                    <div id="theme" class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-palette text-primary"></i> Theme Preference</h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small">Choose your preferred color theme. This will be applied across the platform.</p>
                            
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="update_theme" value="1">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="theme-option card h-100 border-2 <?php echo $theme_preference === 'light' ? 'border-primary' : ''; ?>" onclick="document.getElementById('themeLight').checked = true; this.form.submit();" style="cursor: pointer;">
                                            <div class="card-body text-center p-4">
                                                <div class="theme-preview bg-light border rounded mb-3" style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-sun fa-2x text-warning"></i>
                                                </div>
                                                <h6 class="mb-1">Light</h6>
                                                <small class="text-muted">Bright and clean</small>
                                                <input type="radio" name="theme_preference" id="themeLight" value="light" class="d-none" <?php echo $theme_preference === 'light' ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="theme-option card h-100 border-2 <?php echo $theme_preference === 'dark' ? 'border-primary' : ''; ?>" onclick="document.getElementById('themeDark').checked = true; this.form.submit();" style="cursor: pointer;">
                                            <div class="card-body text-center p-4">
                                                <div class="theme-preview bg-dark border rounded mb-3" style="height: 80px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-moon fa-2x text-info"></i>
                                                </div>
                                                <h6 class="mb-1">Dark</h6>
                                                <small class="text-muted">Easy on the eyes</small>
                                                <input type="radio" name="theme_preference" id="themeDark" value="dark" class="d-none" <?php echo $theme_preference === 'dark' ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="theme-option card h-100 border-2 <?php echo $theme_preference === 'system' ? 'border-primary' : ''; ?>" onclick="document.getElementById('themeSystem').checked = true; this.form.submit();" style="cursor: pointer;">
                                            <div class="card-body text-center p-4">
                                                <div class="theme-preview bg-gradient border rounded mb-3" style="height: 80px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8f9fa 50%, #212529 50%);">
                                                    <i class="fas fa-desktop fa-2x text-secondary"></i>
                                                </div>
                                                <h6 class="mb-1">System</h6>
                                                <small class="text-muted">Match your OS</small>
                                                <input type="radio" name="theme_preference" id="themeSystem" value="system" class="d-none" <?php echo $theme_preference === 'system' ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="update_theme" class="btn btn-primary mt-3">
                                    <i class="fas fa-save"></i> Save Theme
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent"><h5 class="mb-0"><i class="fas fa-clock-rotate-left text-primary"></i> Recent sign-ins</h5></div>
                        <div class="card-body p-0">
                            <?php if ($login_history): ?><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Date</th><th>IP address</th><th>Details</th></tr></thead><tbody><?php foreach ($login_history as $login): ?><tr><td><?php echo htmlspecialchars(formatDateTime($login['created_at']), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($login['ip_address'] ?: 'Unavailable', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($login['description'] ?: 'Sign-in recorded', ENT_QUOTES, 'UTF-8'); ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="text-muted p-4 mb-0">No sign-in history is available.</p><?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Security Section -->
                    <div id="security" class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-shield-alt text-primary"></i> Security</h5>
                        </div>
                        <div class="card-body p-4">
                            <h6>Change Password</h6>
                            <p class="text-muted small">Choose a strong password with at least 8 characters, including uppercase, lowercase, numbers, and special characters.</p>
                            
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
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
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
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
                            <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="mb-3">
                                <label class="form-label" for="profileVisibility">Profile visibility</label>
                                <select class="form-select" id="profileVisibility" name="profile_visibility">
                                    <option value="public" <?php echo ($preferences['profile_visibility'] ?? 'mentors') === 'public' ? 'selected' : ''; ?>>Public - Visible to everyone</option>
                                    <option value="mentors" <?php echo ($preferences['profile_visibility'] ?? 'mentors') === 'mentors' ? 'selected' : ''; ?>>Mentors only</option>
                                    <option value="private" <?php echo ($preferences['profile_visibility'] ?? 'mentors') === 'private' ? 'selected' : ''; ?>>Private - Visible only to you</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="show_email" id="showEmail" <?php echo !empty($preferences['show_email']) ? 'checked' : ''; ?>><label class="form-check-label" for="showEmail">Show my email on my profile</label></div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="show_progress" id="showProgress" <?php echo !isset($preferences['show_progress']) || (int)$preferences['show_progress'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="showProgress">
                                    Show my course progress to mentors
                                </label>
                            </div>
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="allow_messages" id="allowMessages" <?php echo !isset($preferences['allow_messages']) || (int)$preferences['allow_messages'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="allowMessages">
                                    Allow mentors to send me messages
                                </label>
                            </div>
                            
                            <button class="btn btn-primary mt-3" type="submit" name="update_privacy" value="1">
                                <i class="fas fa-save"></i> Save Privacy Settings
                            </button>
                            </form>
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
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
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
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
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