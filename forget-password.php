<?php
$page_title = 'Forgot Password';
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'config/functions.php';
require_once 'config/validation.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator
        ->required('email', 'Email is required.')
        ->email('email', 'Please enter a valid email address.')
        ->exists('email', 'users', 'email', 'Email not found.');
    
    if ($validator->passes()) {
        $email = sanitize($_POST['email']);
        $token = generateToken();
        
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->execute([$token, $email]);
        
        // Send email with reset link
        $reset_link = "http://$_SERVER[HTTP_HOST]/reset-password.php?token=$token";
        
        // In production, send actual email
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Password reset link has been sent to your email.'
        ];
        redirect('login.php');
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => $validator->errorsString()
        ];
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/alerts.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-center fw-bold mb-4">Forgot Password</h3>
                    <p class="text-muted text-center mb-4">Enter your email address and we'll send you a password reset link.</p>
                    
                    <form method="POST" id="forgotForm" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" 
                                   value="<?php echo $_POST['email'] ?? ''; ?>" required>
                            <div class="invalid-feedback" id="forgotEmailError"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </form>
                    <hr>
                    <p class="text-center mb-0"><a href="login.php" class="text-decoration-none">Back to Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotForm');
    
    form.addEventListener('submit', function(e) {
        const email = document.querySelector('input[name="email"]');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!email.value.trim()) {
            email.classList.add('is-invalid');
            document.getElementById('forgotEmailError').textContent = 'Email is required.';
            e.preventDefault();
        } else if (!emailRegex.test(email.value)) {
            email.classList.add('is-invalid');
            document.getElementById('forgotEmailError').textContent = 'Please enter a valid email address.';
            e.preventDefault();
        } else {
            email.classList.remove('is-invalid');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>