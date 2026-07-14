<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
ensure_database_schema();
global $conn;

$message = '';
$stage = 'request';
$resetEmail = $_SESSION['reset_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $action = $_POST['action'] ?? 'request_otp';

    if ($action === 'request_otp') {
        $email = trim($_POST['email'] ?? '');
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $code = rand(100000, 999999);
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code'] = (string)$code;
            $_SESSION['reset_expires'] = time() + 900;
            $message = 'OTP sent to your email. Use code ' . $code . ' to continue.';
            $stage = 'verify_otp';
            $resetEmail = $email;
        } else {
            $message = 'If the email exists, you will receive an OTP shortly.';
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code'] = (string)rand(100000, 999999);
            $_SESSION['reset_expires'] = time() + 900;
            $stage = 'verify_otp';
            $resetEmail = $email;
        }

        $stmt->close();
    } elseif ($action === 'verify_otp') {
        $otp = trim($_POST['otp'] ?? '');
        if (!empty($_SESSION['reset_code']) && !empty($_SESSION['reset_expires']) && time() < $_SESSION['reset_expires'] && $otp === $_SESSION['reset_code']) {
            $stage = 'reset_password';
            $message = 'OTP verified. Please choose your new password.';
        } else {
            $message = 'Invalid or expired OTP. Please try again.';
            $stage = 'verify_otp';
        }
    } elseif ($action === 'reset_password') {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $email = $_SESSION['reset_email'] ?? '';

        if ($password === '' || $confirmPassword === '') {
            $message = 'Please fill in both password fields.';
            $stage = 'reset_password';
        } elseif ($password !== $confirmPassword) {
            $message = 'Passwords do not match.';
            $stage = 'reset_password';
        } elseif ($email === '') {
            $message = 'Reset session expired. Please request OTP again.';
            $stage = 'request';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ? LIMIT 1");
            $stmt->bind_param('ss', $passwordHash, $email);
            if ($stmt->execute()) {
                $message = 'Your password has been updated successfully. Please login.';
                unset($_SESSION['reset_email'], $_SESSION['reset_code'], $_SESSION['reset_expires']);
                $stage = 'completed';
            } else {
                $message = 'Unable to reset your password. Please try again later.';
                $stage = 'reset_password';
            }
            $stmt->close();
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="page-section centered" style="min-height:calc(100vh - 120px);padding:50px 0;">
  <div class="form-card card animate" style="max-width:540px;margin:auto;">
    <div class="text-center mb-4">
      <div class="auth-logo" style="margin-inline:auto;margin-bottom:18px;width:64px;height:64px;">SH</div>
      <h2 class="mb-1">Forgot Password</h2>
      <p class="text-light-emphasis">Securely reset your password with a one-time OTP.</p>
    </div>
    <?php if ($message): ?>
      <div class="alert alert-danger"><?php echo e($message); ?></div>
    <?php endif; ?>

    <?php if ($stage === 'request'): ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <input type="hidden" name="action" value="request_otp" />
        <div class="mb-3">
          <label class="form-label">Email address</label>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required />
        </div>
        <button class="btn btn--primary w-100" type="submit">Send OTP</button>
      </form>
    <?php elseif ($stage === 'verify_otp'): ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <input type="hidden" name="action" value="verify_otp" />
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?php echo e($resetEmail); ?>" disabled />
        </div>
        <div class="mb-3">
          <label class="form-label">Enter OTP</label>
          <input type="text" name="otp" class="form-control" placeholder="123456" required />
        </div>
        <button class="btn btn--primary w-100" type="submit">Verify OTP</button>
      </form>
    <?php elseif ($stage === 'reset_password'): ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <input type="hidden" name="action" value="reset_password" />
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" name="password" class="form-control" placeholder="********" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="********" required />
        </div>
        <button class="btn btn--primary w-100" type="submit">Reset Password</button>
      </form>
    <?php else: ?>
      <div class="text-center">
        <p class="text-light-emphasis">Your password reset is complete.</p>
        <a href="login.php" class="btn btn--primary">Return to Login</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
