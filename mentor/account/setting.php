<?php
$page_title = 'Settings';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/user-preferences.php';
require_once '../../config/auth.php';
requireMentor();

$userId = (int)getUserId();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session token expired. Reload the page and try again.';
    } elseif (isset($_POST['change_password'])) {
        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $passwordHash = (string)$stmt->fetchColumn();

        if (!$passwordHash || !password_verify($currentPassword, $passwordHash)) {
            $errors[] = 'Current password is incorrect.';
        }
        if (strlen($newPassword) < 8 || !preg_match('/[A-Z]/', $newPassword) || !preg_match('/[a-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[^A-Za-z0-9]/', $newPassword)) {
            $errors[] = 'New password needs at least 8 characters, uppercase and lowercase letters, a number, and a symbol.';
        }
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match.';
        }
        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
            $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Password changed successfully.'];
            redirect('setting.php#security');
        }
    } elseif (isset($_POST['save_preferences'])) {
        saveUserPreferences($pdo, $userId, [
            'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
            'booking_alerts' => isset($_POST['booking_alerts']) ? 1 : 0,
            'student_messages' => isset($_POST['student_messages']) ? 1 : 0,
            'course_updates' => isset($_POST['course_updates']) ? 1 : 0,
        ]);
        $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Notification preferences saved.'];
        redirect('setting.php#notifications');
    }
}

$preferences = getUserPreferences($pdo, $userId);
$themePreference = getUserThemePreference($pdo, $userId);
$stmt = $pdo->prepare("SELECT created_at, ip_address, description FROM system_logs WHERE user_id = ? AND action = 'login' ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$userId]);
$loginHistory = $stmt->fetchAll();
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0"><?php include '../../includes/mentor-sidebar.php'; ?></div>
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 pb-2 mb-4 border-bottom">
                <div><h1 class="h2 mb-1">Settings</h1><p class="text-muted mb-0">Manage appearance, account security, and notifications.</p></div>
                <a href="profile.php" class="btn btn-outline-secondary"><i class="fas fa-user me-1"></i>Profile</a>
            </div>
            <?php foreach ($errors as $error): ?><div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
            <div class="row g-4">
                <div class="col-xl-8">
                    <section class="card border-0 shadow-sm mb-4" id="appearance">
                        <div class="card-body p-4">
                            <h2 class="h5"><i class="fas fa-palette text-primary me-2"></i>Appearance</h2>
                            <p class="text-muted">Your theme is saved to your account and follows you across devices.</p>
                            <label class="form-label" for="accountTheme">Color theme</label>
                            <select class="form-select" id="accountTheme" data-theme-select>
                                <option value="system" <?php echo $themePreference === 'system' ? 'selected' : ''; ?>>Use device setting</option>
                                <option value="light" <?php echo $themePreference === 'light' ? 'selected' : ''; ?>>Light</option>
                                <option value="dark" <?php echo $themePreference === 'dark' ? 'selected' : ''; ?>>Dark</option>
                            </select>
                            <small class="text-muted">Changes are applied immediately and saved automatically.</small>
                        </div>
                    </section>
                    <section class="card border-0 shadow-sm mb-4" id="security">
                        <div class="card-header bg-transparent py-3"><h2 class="h5 mb-0"><i class="fas fa-shield-halved text-primary me-2"></i>Security</h2></div>
                        <div class="card-body p-4">
                            <form method="POST" autocomplete="off">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="mb-3"><label class="form-label" for="currentPassword">Current password</label><input id="currentPassword" class="form-control" type="password" name="current_password" autocomplete="current-password" required></div>
                                <div class="mb-3"><label class="form-label" for="newPassword">New password</label><input id="newPassword" class="form-control" type="password" name="new_password" autocomplete="new-password" minlength="8" required><small class="text-muted">At least 8 characters with uppercase, lowercase, a number, and a symbol.</small></div>
                                <div class="mb-3"><label class="form-label" for="confirmPassword">Confirm new password</label><input id="confirmPassword" class="form-control" type="password" name="confirm_password" autocomplete="new-password" required></div>
                                <button class="btn btn-primary" type="submit" name="change_password" value="1"><i class="fas fa-key me-1"></i>Update password</button>
                            </form>
                        </div>
                    </section>
                    <section class="card border-0 shadow-sm mb-4" id="notifications">
                        <div class="card-header bg-transparent py-3"><h2 class="h5 mb-0"><i class="fas fa-bell text-primary me-2"></i>Notifications</h2></div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php foreach (['email_notifications' => 'Email notifications', 'booking_alerts' => 'Booking alerts', 'student_messages' => 'Student messages', 'course_updates' => 'Course and assignment updates'] as $key => $label): ?>
                                    <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" <?php echo (int)($preferences[$key] ?? 1) ? 'checked' : ''; ?>><label class="form-check-label" for="<?php echo $key; ?>"><?php echo $label; ?></label></div>
                                <?php endforeach; ?>
                                <button class="btn btn-primary" type="submit" name="save_preferences" value="1"><i class="fas fa-save me-1"></i>Save notification preferences</button>
                            </form>
                        </div>
                    </section>
                    <section class="card border-0 shadow-sm" id="login-history">
                        <div class="card-header bg-transparent py-3"><h2 class="h5 mb-0"><i class="fas fa-clock-rotate-left text-primary me-2"></i>Recent sign-ins</h2></div>
                        <div class="card-body p-0">
                            <?php if ($loginHistory): ?><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Date</th><th>IP address</th><th>Details</th></tr></thead><tbody><?php foreach ($loginHistory as $login): ?><tr><td><?php echo htmlspecialchars(formatDateTime($login['created_at']), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($login['ip_address'] ?: 'Unavailable', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($login['description'] ?: 'Sign-in recorded', ENT_QUOTES, 'UTF-8'); ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="text-muted p-4 mb-0">No sign-in history is available.</p><?php endif; ?>
                        </div>
                    </section>
                </div>
                <aside class="col-xl-4"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h2 class="h6">Account</h2><p class="text-muted small">Update your public teaching details from your profile.</p><a class="btn btn-outline-primary w-100" href="profile.php">Open mentor profile</a><hr><p class="small text-muted mb-0">For account access assistance, contact a platform administrator. Two-factor authentication controls are not shown because this app does not currently enforce 2FA.</p></div></div></aside>
            </div>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
