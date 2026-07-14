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
<?php include 'includes/header.php'; ?>

<div class="page-section centered" style="min-height:calc(100vh - 120px);padding:50px 0;">
  <div class="auth-grid">
    <section class="auth-panel card form-card animate">
      <div class="text-center" style="margin-bottom:28px">
        <div class="auth-logo" style="margin-inline:auto;margin-bottom:18px;width:64px;height:64px;">SH</div>
        <h2 class="mb-1">Create your ShareSkill Hub account</h2>
        <p class="text-light-emphasis mb-4">Register as a fresher or graduate mentor to start learning and teaching.</p>
      </div>
      <?php if ($message): ?>
        <div class="alert alert-danger"><?php echo e($message); ?></div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <div class="row g-4">
          <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" placeholder="Rohan Sharma" required /></div>
          <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" placeholder="rohan@gmail.com" required /></div>
          <div class="col-md-6"><label class="form-label">Role</label><select name="role" class="form-control"><option value="fresher">Fresher</option><option value="graduate">Graduate Mentor</option></select></div>
          <div class="col-md-6"><label class="form-label">Field</label><select name="field" class="form-control"><option>Information Technology</option><option>Agriculture</option><option>Engineering</option><option>Medical</option><option>Law</option><option>Management</option></select></div>
          <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" placeholder="********" required /></div>
          <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="confirm_password" class="form-control" placeholder="********" required /></div>
        </div>
        <div class="d-flex align-items-center gap-2 mt-3">
          <input type="checkbox" id="terms" required />
          <label for="terms" class="small">I agree to the <strong>Terms</strong> and <strong>Privacy Policy</strong></label>
        </div>
        <button class="btn btn--primary w-100 mt-3" type="submit">Register</button>
      </form>
      <div class="auth-divider"><span>or continue with</span></div>
      <div class="auth-form-actions">
        <button class="btn btn--outline w-100" type="button">Google</button>
        <button class="btn btn--outline w-100" type="button">Facebook</button>
      </div>
      <p class="text-center mt-3 mb-0 small">Already have an account? <a href="login.php" style="color:var(--primary);font-weight:900">Login</a></p>
    </section>
    <section class="auth-brand">
      <div class="auth-logo">SH</div>
      <div class="auth-eyebrow">Join the community</div>
      <h1>Gain mentorship, assignments, video sessions and placement support</h1>
      <p>Register as a fresher or graduate mentor and unlock courses, sessions, assignments, certificates and placement tools.</p>
      <div class="auth-info-list">
        <div><strong>✔</strong><span>Mentorship bookings and schedules</span></div>
        <div><strong>✔</strong><span>Course recommendations tailored to your field</span></div>
        <div><strong>✔</strong><span>Verified certificates and placement guides</span></div>
      </div>
    </section>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
