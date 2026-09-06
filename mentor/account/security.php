<?php
$page_title = 'Security';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();

// Get login history
$stmt = $pdo->prepare("SELECT * FROM system_logs WHERE user_id = ? AND action = 'login' ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user_id]);
$login_history = $stmt->fetchAll();

// Get security settings
$stmt = $pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'security'");
$stmt->execute();
$security_settings = [];
while ($row = $stmt->fetch()) {
    $security_settings[$row['setting_key']] = $row['setting_value'];
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
                <h1 class="h2">Security</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5>Two-Factor Authentication</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="twoFactorToggle" 
                                       <?php echo ($security_settings['two_factor_enabled'] ?? 0) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="twoFactorToggle">Enable Two-Factor Authentication</label>
                            </div>
                            <p class="text-muted small">Add an extra layer of security to your account.</p>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5>Login History</h5>
                            <?php if (!empty($login_history)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>IP Address</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($login_history as $log): ?>
                                        <tr>
                                            <td><?php echo formatDateTime($log['created_at']); ?></td>
                                            <td><?php echo htmlspecialchars($log['ip_address'] ?? 'Unknown'); ?></td>
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
                            <p class="text-muted">No login history available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-4">
                            <h6>Security Tips</h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Use a strong password
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Enable 2FA for extra security
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-1"></i>
                                    Review login history regularly
                                </li>
                                <li>
                                    <i class="fas fa-check text-success me-1"></i>
                                    Logout from shared devices
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>