<?php
$page_title = 'Account Settings';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/user-preferences.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

$userId = (int)getUserId();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session token expired. Reload the page and try again.';
    } elseif (isset($_POST['change_password'])) {
        $current = (string)($_POST['current_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $hash = (string)$stmt->fetchColumn();
        if (!$hash || !password_verify($current, $hash)) $errors[] = 'Current password is incorrect.';
        if (strlen($new) < 8 || !preg_match('/[A-Z]/', $new) || !preg_match('/[a-z]/', $new) || !preg_match('/[0-9]/', $new) || !preg_match('/[^A-Za-z0-9]/', $new)) $errors[] = 'New password needs at least 8 characters, uppercase and lowercase letters, a number, and a symbol.';
        if ($new !== $confirm) $errors[] = 'New passwords do not match.';
        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $userId]);
            $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Password changed successfully.'];
            header('Location: ' . appUrl('admin/account/settings.php#security'));
            exit;
        }
    } elseif (isset($_POST['save_preferences'])) {
        saveUserPreferences($pdo, $userId, [
            'email_notifications' => isset($_POST['email_notifications']) ? 1 : 0,
            'system_alerts' => isset($_POST['system_alerts']) ? 1 : 0,
        ]);
        $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Notification preferences saved.'];
        header('Location: ' . appUrl('admin/account/settings.php#notifications'));
        exit;
    }
}
$preferences = getUserPreferences($pdo, $userId);
$themePreference = getUserThemePreference($pdo, $userId);
$stmt = $pdo->prepare("SELECT created_at, ip_address, description FROM system_logs WHERE user_id = ? AND action = 'login' ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$userId]);
$loginHistory = $stmt->fetchAll();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-topbar"><div><h1 class="admin-title">Account settings</h1><p class="admin-subtitle">Personal appearance, account security, and notification preferences.</p></div></div>
        <?php foreach ($errors as $error): ?><div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
        <?php if (isset($_SESSION['alert'])): ?><div class="alert alert-<?php echo htmlspecialchars((string)$_SESSION['alert']['type'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert"><?php echo htmlspecialchars((string)$_SESSION['alert']['message'], ENT_QUOTES, 'UTF-8'); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php unset($_SESSION['alert']); ?><?php endif; ?>
        <div class="row g-4">
            <div class="col-xl-8">
                <section class="admin-card mb-4" id="appearance">
                    <h2 class="h5"><i class="fas fa-palette text-primary me-2"></i>Appearance</h2>
                    <p class="text-muted">Theme preference is saved to your account and applied across devices.</p>
                    <label class="form-label" for="accountTheme">Color theme</label>
                    <select class="form-select" id="accountTheme" data-theme-select><option value="system" <?php echo $themePreference === 'system' ? 'selected' : ''; ?>>Use device setting</option><option value="light" <?php echo $themePreference === 'light' ? 'selected' : ''; ?>>Light</option><option value="dark" <?php echo $themePreference === 'dark' ? 'selected' : ''; ?>>Dark</option></select>
                    <small class="text-muted">Changes are applied immediately and saved automatically.</small>
                </section>
                <section class="admin-card mb-4" id="security">
                    <h2 class="h5"><i class="fas fa-shield-halved text-primary me-2"></i>Security</h2>
                    <form method="POST" autocomplete="off">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="mb-3"><label class="form-label" for="currentPassword">Current password</label><input id="currentPassword" class="form-control" type="password" name="current_password" autocomplete="current-password" required></div>
                        <div class="mb-3"><label class="form-label" for="newPassword">New password</label><input id="newPassword" class="form-control" type="password" name="new_password" autocomplete="new-password" minlength="8" required><small class="text-muted">At least 8 characters with uppercase, lowercase, a number, and a symbol.</small></div>
                        <div class="mb-3"><label class="form-label" for="confirmPassword">Confirm new password</label><input id="confirmPassword" class="form-control" type="password" name="confirm_password" autocomplete="new-password" required></div>
                        <button class="btn btn-primary" type="submit" name="change_password" value="1"><i class="fas fa-key me-1"></i>Update password</button>
                    </form>
                </section>
                <section class="admin-card mb-4" id="notifications">
                    <h2 class="h5"><i class="fas fa-bell text-primary me-2"></i>Notifications</h2>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                        <?php foreach (['email_notifications' => 'Email notifications', 'system_alerts' => 'Platform and security alerts'] as $key => $label): ?><div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" <?php echo (int)($preferences[$key] ?? 1) ? 'checked' : ''; ?>><label class="form-check-label" for="<?php echo $key; ?>"><?php echo $label; ?></label></div><?php endforeach; ?>
                        <button class="btn btn-primary" type="submit" name="save_preferences" value="1"><i class="fas fa-save me-1"></i>Save preferences</button>
                    </form>
                </section>
                <section class="admin-card" id="login-history">
                    <h2 class="h5"><i class="fas fa-clock-rotate-left text-primary me-2"></i>Recent sign-ins</h2>
                    <?php if ($loginHistory): ?><div class="table-responsive"><table class="admin-table"><thead><tr><th>Date</th><th>IP address</th><th>Details</th></tr></thead><tbody><?php foreach ($loginHistory as $login): ?><tr><td><?php echo htmlspecialchars(formatDateTime($login['created_at']), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($login['ip_address'] ?: 'Unavailable', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($login['description'] ?: 'Sign-in recorded', ENT_QUOTES, 'UTF-8'); ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p class="text-muted mb-0">No sign-in history is available.</p><?php endif; ?>
                </section>
            </div>
            <aside class="col-xl-4"><section class="admin-card"><h2 class="h6">Account protection</h2><p class="text-muted small">Your administrator account cannot be deactivated from this screen. Ask another administrator to review account access.</p><p class="text-muted small mb-0">Two-factor authentication controls are omitted because this application does not currently enforce 2FA.</p></section></aside>
        </div>
    </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
