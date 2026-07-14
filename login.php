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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | ShareSkill Hub</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <main class="auth-page">
    <div class="auth-grid">
      <section class="auth-brand">
        <div class="auth-logo">SH</div>
        <h1>Welcome Back!</h1>
        <p>Login to continue your learning, mentorship sessions, assignments and certificates.</p>
      </section>
      <section class="card form-card animate">
        <h2 class="text-center mb-1">Welcome Back!</h2>
        <p class="text-center text-light-emphasis mb-4">Login to continue your learning</p>
        <?php if ($message): ?>
          <div class="alert alert-danger"><?php echo e($message); ?></div>
        <?php endif; ?>
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="rohan@gmail.com" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="********" required />
          </div>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <label class="small"><input type="checkbox" style="width:auto;min-height:auto" /> Remember me</label>
            <a href="forgot-password.php" class="small" style="color:var(--primary);font-weight:800">Forgot Password?</a>
          </div>
          <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>
        <div class="d-flex gap-2 mt-3">
          <button class="btn btn--outline w-100" type="button">Google</button>
          <button class="btn btn--outline w-100" type="button">Facebook</button>
        </div>
        <p class="text-center mt-3 mb-0 small">Don't have an account? <a href="register.php" style="color:var(--primary);font-weight:900">Register</a></p>
      </section>
    </div>
  </main>
</body>
</html>
