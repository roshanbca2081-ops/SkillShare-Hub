<?php
$page_title = 'Forgot Password';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';
$mailConfig = require __DIR__ . '/config/mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Your request expired. Please try again.',
        ];
    } else {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) <= 255) {
            try {
                $stmt = $pdo->prepare('SELECT id, full_name, email FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $token);
                    $saveToken = $pdo->prepare('UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?');
                    $saveToken->execute([$tokenHash, $user['id']]);
                    $resetUrl = $mailConfig['base_url'] . '/reset-password.php?token=' . rawurlencode($token);
                    try {
                        sendPasswordResetEmail($user['email'], $user['full_name'], $resetUrl);
                    } catch (Throwable $mailError) {
                        // Do not leave an unusable reset token behind if email delivery failed.
                        $clearToken = $pdo->prepare('UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE id = ? AND reset_token = ?');
                        $clearToken->execute([$user['id'], $tokenHash]);
                        error_log('Password reset email delivery failed: ' . $mailError->getMessage());
                    }
                }
            } catch (Throwable $error) {
                error_log('Password reset request failed: ' . $error->getMessage());
            }
        }

        // The same response is shown for known, unknown, and malformed email addresses.
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'If an account exists for that email, a password reset link has been sent.',
        ];
    }
    redirect('forget-password.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer">
    <title>Forgot Password - SkillShare Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth-pages.css?v=20261105-password-reset-v1">
    <link rel="stylesheet" href="assets/css/mobile-responsive.css?v=20261104-responsive-v1">
</head>
<body class="login-body">
<div class="auth-theme-picker dropdown">
    <button class="theme-toggle-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Theme switcher">
        <i class="fas fa-circle-half-stroke" aria-hidden="true"></i>
        <span class="theme-label">System</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end theme-menu">
        <li><button type="button" class="dropdown-item theme-option" data-theme-option="light"><i class="fas fa-sun"></i> Light</button></li>
        <li><button type="button" class="dropdown-item theme-option" data-theme-option="dark"><i class="fas fa-moon"></i> Dark</button></li>
        <li><button type="button" class="dropdown-item theme-option" data-theme-option="system"><i class="fas fa-desktop"></i> System</button></li>
    </ul>
</div>
<main class="login-page">
    <div class="login-shell">
        <aside class="login-brand-panel">
            <a class="login-brand" href="index.php"><span class="login-brand-icon"><img src="assets/images/logo/skillshare-hub-mark.svg" alt=""></span><span>SkillShare <strong>Hub</strong></span></a>
            <div class="login-brand-copy"><span class="login-eyebrow">Account recovery</span><h1>Back to<br><span>learning.</span></h1><p>We’ll help you securely get back into your SkillShare Hub account.</p></div>
            <div class="login-brand-foot"><i class="fas fa-shield-halved"></i> Secure, one-time password reset.</div>
        </aside>
        <section class="login-panel" aria-labelledby="recoveryTitle">
            <div class="login-panel-inner">
                <div class="login-topline"><span>ACCOUNT RECOVERY</span><a href="login.php"><i class="fas fa-arrow-left me-1"></i> Sign in</a></div>
                <div class="login-heading"><h2 id="recoveryTitle">Forgot your password?</h2><p>Enter your account email and we’ll send a reset link if it matches an account.</p></div>
                <?php if (isset($_SESSION['alert'])): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['alert']['type'] ?? 'info', ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show login-alert" role="status">
                        <i class="fas fa-<?php echo htmlspecialchars($_SESSION['alert']['icon'] ?? 'info-circle', ENT_QUOTES, 'UTF-8'); ?> me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['alert']['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['alert']); ?>
                <?php endif; ?>
                <form method="POST" class="login-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3"><label class="form-label" for="recoveryEmail">Email address</label><input class="form-control" id="recoveryEmail" type="email" name="email" autocomplete="email" maxlength="255" required></div>
                    <button class="btn login-submit w-100" type="submit">Send reset link <i class="fas fa-arrow-right ms-2"></i></button>
                </form>
                <p class="login-register mt-4"><a href="login.php">Return to sign in</a></p>
            </div>
        </section>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>