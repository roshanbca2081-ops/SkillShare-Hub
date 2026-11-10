<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Referrer-Policy: no-referrer');

$page_title = 'Reset Password';
$token = trim((string)(($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ? ($_POST['token'] ?? '') : ($_GET['token'] ?? '')));
$tokenHash = preg_match('/\A[a-f0-9]{64}\z/i', $token) ? hash('sha256', $token) : '';
$tokenIsValid = false;
if ($tokenHash !== '') {
    try {
        $checkToken = $pdo->prepare('SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() LIMIT 1');
        $checkToken->execute([$tokenHash]);
        $tokenIsValid = (bool)$checkToken->fetchColumn();
    } catch (Throwable $error) {
        error_log('Password reset token check failed: ' . $error->getMessage());
    }
}

$errors = [];
$resetComplete = false;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your request expired. Please reload this page and try again.';
    } elseif (!$tokenIsValid) {
        $errors[] = 'This reset link is invalid or expired. Request a new one to continue.';
    } else {
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $confirmPassword = is_string($_POST['confirm_password'] ?? null) ? $_POST['confirm_password'] : '';
        if (strlen($password) < 8) {
            $errors[] = 'Choose a password with at least 8 characters.';
        } elseif (strlen($password) > 4096) {
            $errors[] = 'The password is too long.';
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'The password and confirmation do not match.';
        }

        if (!$errors) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            if ($passwordHash === false) {
                $errors[] = 'We could not update your password. Please try again.';
            } else {
                try {
                    // This conditional update makes the token single-use, including concurrent submissions.
                    $updatePassword = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ? AND reset_token_expiry > NOW()');
                    $updatePassword->execute([$passwordHash, $tokenHash]);
                    if ($updatePassword->rowCount() === 1) {
                        $resetComplete = true;
                        $_SESSION['alert'] = [
                            'type' => 'success',
                            'icon' => 'check-circle',
                            'message' => 'Your password has been reset. Sign in with your new password.',
                        ];
                        redirect('login.php');
                    }
                    $tokenIsValid = false;
                    $errors[] = 'This reset link is invalid or expired. Request a new one to continue.';
                } catch (Throwable $error) {
                    error_log('Password reset update failed: ' . $error->getMessage());
                    $errors[] = 'We could not update your password. Please try again.';
                }
            }
        }
    }
}

$showForm = $tokenIsValid && !$resetComplete;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer">
    <title>Reset Password - SkillShare Hub</title>
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
            <div class="login-brand-copy"><span class="login-eyebrow">Secure account access</span><h1>A fresh<br><span>start.</span></h1><p>Choose a new password to continue learning, teaching, and sharing skills.</p></div>
            <div class="login-brand-foot"><i class="fas fa-lock"></i> Reset links expire after 15 minutes.</div>
        </aside>
        <section class="login-panel" aria-labelledby="resetTitle">
            <div class="login-panel-inner">
                <div class="login-topline"><span>SET A NEW PASSWORD</span><a href="login.php"><i class="fas fa-arrow-left me-1"></i> Sign in</a></div>
                <div class="login-heading"><h2 id="resetTitle">Reset your password</h2><p>Use at least 8 characters. Your reset link works only once.</p></div>
                <?php if (!$showForm): ?>
                    <div class="alert alert-warning login-alert" role="alert">This reset link is invalid or expired. Request a new password reset link to continue.</div>
                    <a class="btn login-submit w-100" href="forget-password.php">Request a new link</a>
                <?php else: ?>
                    <?php foreach ($errors as $error): ?><div class="alert alert-danger login-alert" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
                    <form method="POST" id="resetForm" class="login-form" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="mb-3"><label class="form-label" for="newPassword">New password</label><input class="form-control" id="newPassword" type="password" name="password" autocomplete="new-password" minlength="8" maxlength="4096" required></div>
                        <div class="mb-3"><label class="form-label" for="confirmPassword">Confirm new password</label><input class="form-control" id="confirmPassword" type="password" name="confirm_password" autocomplete="new-password" minlength="8" maxlength="4096" required><div class="invalid-feedback" id="confirmPasswordError"></div></div>
                        <button class="btn login-submit w-100" type="submit">Update password <i class="fas fa-check ms-2"></i></button>
                    </form>
                <?php endif; ?>
                <p class="login-register mt-4"><a href="login.php">Return to sign in</a></p>
            </div>
        </section>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('resetForm');
    if (!form) return;
    const password = document.getElementById('newPassword');
    const confirmation = document.getElementById('confirmPassword');
    const error = document.getElementById('confirmPasswordError');
    form.addEventListener('submit', function (event) {
        if (password.value !== confirmation.value) {
            confirmation.classList.add('is-invalid');
            error.textContent = 'The password and confirmation do not match.';
            event.preventDefault();
        } else {
            confirmation.classList.remove('is-invalid');
            error.textContent = '';
        }
    });
});
</script>
</body>
</html>