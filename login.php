<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

ensure_database_schema();

if (is_logged_in()) {
    redirect('dashboard.php');
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $conn;
    verify_csrf_token();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email !== '' && $password !== '') {
        $stmt = $conn->prepare("SELECT id, full_name, password_hash, role, status FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash']) && $user['status'] === 'active') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $email;
            redirect('dashboard.php');
        } else {
            $message = 'Invalid login credentials.';
        }
    } else {
        $message = 'Please enter both email and password.';
    }
}
?>
<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="assets/css/auth-premium.css" />

<div class="page-section centered" style="min-height:calc(100vh - 120px);padding:50px 0;">
  <div class="auth-grid" style="position:relative;">
    <div class="auth-bg-logo" aria-hidden="true"></div>
    <div class="auth-particles" aria-hidden="true" style="--d:14s;">
      <span style="--x:12%;--y:28%;--dx:18px;"></span>
      <span style="--x:26%;--y:18%;--dx:-14px;--d:17s;"></span>
      <span style="--x:54%;--y:24%;--dx:16px;--d:12s;"></span>
      <span style="--x:72%;--y:34%;--dx:-10px;--d:15s;"></span>
      <span style="--x:84%;--y:18%;--dx:14px;--d:19s;"></span>
      <span style="--x:38%;--y:50%;--dx:-18px;--d:16s;"></span>
    </div>
    <section class="auth-brand">
      <div class="site-logo site-logo--auth" aria-hidden="true"></div>

      <div class="auth-eyebrow">Secure Access</div>
      <h1>Welcome Back to ShareSkill Hub</h1>
      <p>Login to continue your learning, mentorship sessions, assignments and certificates.</p>
      <div class="auth-info-list">
        <div><strong>✔</strong><span>Fast access to courses and mentors</span></div>
        <div><strong>✔</strong><span>Track your assignments and progress</span></div>
        <div><strong>✔</strong><span>Secure dashboard for freshers and graduates</span></div>
      </div>
    </section>
    <section class="auth-panel form-card animate">
      <div class="text-center" style="margin-bottom:28px">
        <div class="site-logo site-logo--auth" aria-hidden="true" style="margin-bottom:18px;"></div>
        <h2 class="mb-1">Welcome Back!</h2>
        <p class="text-light-emphasis mb-4">Login to continue your journey.</p>
      </div>
      <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo e($message); ?></div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <div class="input-icon"><i class="fa-regular fa-envelope"></i><input type="email" name="email" class="form-control" placeholder="Email Address" required /></div>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-icon"><i class="fa-solid fa-lock"></i><input type="password" name="password" class="form-control" placeholder="Password" required data-password-field /><button class="password-toggle" type="button" data-password-toggle aria-label="Show password"><i class="fa-regular fa-eye"></i></button></div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <label class="small" style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="remember_me" value="1" style="width:auto;min-height:auto" /> Remember me
          </label>

          <a href="forgot-password.php" class="small" style="color:var(--primary);font-weight:800">Forgot Password?</a>
        </div>

        <button class="btn btn--primary w-100" type="submit">Login</button>
      </form>
      <div class="auth-divider"><span>or continue with</span></div>
      <div class="auth-form-actions">
        <button class="btn btn--outline w-100" type="button"><i class="fa-brands fa-google"></i> Login with Google</button>
        <button class="btn btn--outline w-100" type="button"><i class="fa-brands fa-facebook"></i> Login with Facebook</button>
      </div>
      <p class="text-center mt-3 mb-0 small">Don't have an account? <a href="register.php" style="color:var(--primary);font-weight:900">Register</a></p>
    </section>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
