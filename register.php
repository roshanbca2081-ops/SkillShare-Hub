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
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($fullName === '' || $email === '' || $password === '') {
        $message = 'Please fill in all required fields.';
    } elseif ($password !== $confirmPassword) {
        $message = 'Password and confirm password must match.';
    } elseif (!in_array($role, ['fresher', 'graduate'], true)) {
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
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <main class="auth-page">
    <div class="auth-grid">
      <section class="card form-card animate">
        <h2 class="text-center mb-1">Create Your Account</h2>
        <p class="text-center text-light-emphasis mb-4">Join SkillShare Hub and start your learning journey</p>
        <?php if ($message): ?>
          <div class="alert alert-danger"><?php echo e($message); ?></div>
        <?php endif; ?>
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
          <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="Rohan Sharma" required />
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" placeholder="rohan@gmail.com" required />
          </div>
          <div class="mb-3">
            <label>Address</label>
            <input type="text" name="address" placeholder="Kathmandu, Nepal" />
          </div>
          <div class="mb-3">
            <label>Phone</label>
            <input type="tel" name="phone" placeholder="9800000000" />
          </div>
          <div class="mb-3">
            <label>Role</label>
            <select name="role">
              <option value="fresher">Fresher</option>
              <option value="graduate">Graduate Mentor</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Field</label>
            <select name="field">
              <option>Information Technology</option>
              <option>Agriculture</option>
              <option>Engineering</option>
              <option>Medical</option>
              <option>Law</option>
              <option>Management</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" placeholder="********" required />
          </div>
          <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="********" required />
          </div>
          <button class="btn btn-primary w-100" type="submit">Register</button>
        </form>
        <div class="d-flex gap-2 mt-3">
          <button class="btn btn--outline w-100" type="button">Google</button>
          <button class="btn btn--outline w-100" type="button">Facebook</button>
        </div>
        <p class="text-center mt-3 mb-0 small">Already have an account? <a href="login.php" style="color:var(--primary);font-weight:900">Login</a></p>
      </section>
      <section class="auth-brand">
        <div class="auth-logo">SH</div>
        <h1>SkillShare Hub</h1>
        <p>Register as a fresher or graduate mentor and unlock courses, sessions, assignments, payments and certificate workflows.</p>
      </section>
    </div>
  </main>
</body>
</html>
