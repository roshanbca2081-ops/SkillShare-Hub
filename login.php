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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card form-card animate">
      <h2 class="text-center mb-3"><i class="fa-solid fa-right-to-bracket me-2"></i>Welcome Back</h2>
      <p class="text-center text-light-emphasis mb-4">Sign in to continue your journey.</p>
      <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo e($message); ?></div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <div class="mb-3">
          <label class="form-label">Email address</label>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="********" required />
        </div>
        <div class="d-flex justify-content-between mb-3">
          <div><input type="checkbox" /> <small>Remember me</small></div>
          <a href="forgot-password.php" class="small">Forgot password?</a>
        </div>
        <button class="btn btn-primary w-100" type="submit">Login</button>
      </form>
      <p class="text-center mt-3 mb-0">New here? <a href="register.php">Create account</a></p>
    </div>
  </div>
</body>
</html>
