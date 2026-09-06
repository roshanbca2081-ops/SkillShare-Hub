<?php
$page_title = 'Security';
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

// Handle two-factor authentication
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_2fa'])) {
    $enabled = isset($_POST['two_factor_enabled']) ? 1 : 0;
    
    // In a real implementation, you would use a library like Google Authenticator
    // For demonstration, we'll just store the preference
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) 
                           VALUES ('two_factor_enabled', ?, 'security') 
                           ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$enabled, $enabled]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Two-factor authentication settings updated!'
    ];
    redirect('security.php');
}

// Get current security settings
$security_settings = [];
$stmt = $pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'security'");
$stmt->execute();
while ($row = $stmt->fetch()) {
    $security_settings[$row['setting_key']] = $row['setting_value'];
}

// Get login history
$stmt = $pdo->prepare("SELECT * FROM system_logs WHERE user_id = ? AND action = 'login' ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$login_history = $stmt->fetchAll();

// Handle session management - logout all devices
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_all_devices'])) {
    // Clear all sessions (in production, you'd clear from a sessions table)
    $_SESSION = [];
    session_destroy();
    
    // Redirect to login
    header('Location: ../../login.php?logout_all=1');
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
                <h1 class="h2">Security</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Two-Factor Authentication -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-mobile-alt text-primary"></i> Two-Factor Authentication</h5>
                        </div>
                        <div class="card-body p-4">
                            <p>Add an extra layer of security to your account by requiring both your password and a verification code from your authenticator app.</p>
                            
                            <form method="POST">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="two_factor_enabled" id="twoFactorToggle" 
                                           <?php echo ($security_settings['two_factor_enabled'] ?? 0) ? 'checked' : ''; ?>
                                           onchange="document.getElementById('twoFactorSubmit').click()">
                                    <label class="form-check-label" for="twoFactorToggle">
                                        <strong>Enable Two-Factor Authentication</strong>
                                        <br><small class="text-muted">Use authenticator app for secure login</small>
                                    </label>
                                </div>
                                
                                <?php if ($security_settings['two_factor_enabled'] ?? 0): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Scan the QR code with your authenticator app:</strong>
                                    <div class="text-center my-3">
                                        <!-- In production, generate actual QR code -->
                                        <div style="width: 200px; height: 200px; background: #e9ecef; margin: 0 auto; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                            <span class="text-muted">QR Code Placeholder</span>
                                        </div>
                                    </div>
                                    <p class="small">Or enter this code manually: <code>JBSWY3DPEHPK3PXP</code></p>
                                </div>
                                <?php endif; ?>
                                
                                <button type="submit" name="toggle_2fa" id="twoFactorSubmit" class="d-none"></button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Session Management -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-desktop text-primary"></i> Active Sessions</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                You are currently logged in on this device.
                            </div>
                            
                            <div class="session-item d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <div>
                                    <strong>This Device</strong>
                                    <br><small class="text-muted"><?php echo $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Device'; ?></small>
                                    <br><small class="text-muted"><i class="fas fa-clock"></i> Active now</small>
                                </div>
                                <span class="badge bg-success">Current Session</span>
                            </div>
                            
                            <div class="session-item d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <div>
                                    <strong>Chrome - Windows</strong>
                                    <br><small class="text-muted">IP: 192.168.1.100</small>
                                    <br><small class="text-muted"><i class="fas fa-clock"></i> Last active 2 hours ago</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger">Revoke</button>
                            </div>
                            
                            <div class="session-item d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                <div>
                                    <strong>Safari - iPhone</strong>
                                    <br><small class="text-muted">IP: 192.168.1.101</small>
                                    <br><small class="text-muted"><i class="fas fa-clock"></i> Last active 1 day ago</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger">Revoke</button>
                            </div>
                            
                            <form method="POST">
                                <button type="submit" name="logout_all_devices" class="btn btn-danger mt-3" 
                                        onclick="return confirm('This will log you out from all devices including this one. Continue?')">
                                    <i class="fas fa-sign-out-alt"></i> Logout All Devices
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Login History -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0"><i class="fas fa-history text-primary"></i> Login History</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($login_history)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>IP Address</th>
                                                <th>Browser</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($login_history as $log): ?>
                                            <tr>
                                                <td><?php echo formatDateTime($log['created_at']); ?></td>
                                                <td><?php echo htmlspecialchars($log['ip_address'] ?? 'Unknown'); ?></td>
                                                <td><?php echo htmlspecialchars(substr($log['user_agent'] ?? '', 0, 30) . '...'); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo strpos($log['description'], 'success') !== false ? 'success' : 'danger'; ?>">
                                                        <?php echo strpos($log['description'], 'success') !== false ? 'Success' : 'Failed'; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No login history available</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Security Tips -->
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h6><i class="fas fa-shield-alt text-primary"></i> Security Tips</h6>
                            
                            <div class="security-tip mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-primary rounded-circle me-2">1</span>
                                    <div>
                                        <strong>Use a Strong Password</strong>
                                        <p class="small text-muted">Use at least 8 characters with a mix of letters, numbers, and symbols.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="security-tip mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-primary rounded-circle me-2">2</span>
                                    <div>
                                        <strong>Enable Two-Factor Authentication</strong>
                                        <p class="small text-muted">Add an extra layer of security to your account.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="security-tip mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-primary rounded-circle me-2">3</span>
                                    <div>
                                        <strong>Regularly Review Active Sessions</strong>
                                        <p class="small text-muted">Check and revoke any suspicious active sessions.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="security-tip">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-primary rounded-circle me-2">4</span>
                                    <div>
                                        <strong>Beware of Phishing</strong>
                                        <p class="small text-muted">Never share your password or verification codes with anyone.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="security-status">
                                <h6>Security Score</h6>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 85%;"></div>
                                </div>
                                <small class="text-muted">Your account security is <strong>Good</strong></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Alert for suspicious login attempts (simulated)
document.addEventListener('DOMContentLoaded', function() {
    // Check if there were any suspicious login attempts
    const suspicious = <?php echo rand(0, 1); ?>;
    if (suspicious) {
        showNotification('⚠️ Suspicious login attempt detected from an unrecognized device', 'warning');
    }
});
</script>

<?php include '../../includes/footer.php'; ?>