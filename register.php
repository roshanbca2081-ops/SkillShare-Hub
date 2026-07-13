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
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'fresher';
    $password = $_POST['password'] ?? '';

    if ($fullName === '' || $email === '' || $password === '') {
        $message = 'Please fill in all required fields.';
    } else if (!in_array($role, ['fresher', 'graduate'], true)) {
        $message = 'Invalid role selected.';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param('s', $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = 'An account with that email already exists.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, 'active')");
            $stmt->bind_param('ssss', $fullName, $email, $passwordHash, $role);
            if ($stmt->execute()) {
                $newUserId = $stmt->insert_id;
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_role'] = $role;
                $_SESSION['user_email'] = $email;
                add_notification($newUserId, 'Welcome to ShareSkill Hub! Explore courses, submit assignments, and book mentorship.');
                redirect('dashboard.php');
            } else {
                $message = 'Registration failed. Please try again.';
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register | ShareSkill Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <div class="container py-5">
    <div class="card form-card animate">
      <h2 class="text-center mb-3"><i class="fa-solid fa-user-plus me-2"></i>Create Account</h2>
      <p class="text-center text-light-emphasis mb-4">Choose your path and start building skills.</p>
      <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo e($message); ?></div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <div class="mb-3">
          <label class="form-label">Full name</label>
          <input type="text" name="full_name" class="form-control" placeholder="Your name" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Email address</label>
          <input type="email" name="email" class="form-control" placeholder="you@example.com" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Role</label>
          <select class="form-select" name="role">
            <option value="fresher">Fresher</option>
            <option value="graduate">Graduate</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="********" required />
        </div>
        <button class="btn btn-primary w-100" type="submit">Register</button>
      </form>
      <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>
</body>
</html>
