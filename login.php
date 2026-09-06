<?php
$page_title = 'Login';
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'config/functions.php';
require_once 'config/validation.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator
        ->required('email', 'Email is required.')
        ->email('email', 'Please enter a valid email.')
        ->required('password', 'Password is required.');
    
    if ($validator->passes()) {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            $_SESSION['alert'] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'message' => 'Welcome back, ' . $user['full_name'] . '!'
            ];
            
            switch ($user['role']) {
                case 'admin': redirect('admin/dashboard.php'); break;
                case 'mentor': redirect('mentor/dashboard.php'); break;
                default: redirect('fresher/dashboard.php');
            }
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'message' => 'Invalid email or password.'
            ];
        }
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
                    <h3 class="text-center fw-bold mb-4">Welcome Back</h3>
                    
                    <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Registration successful! Please login.
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="loginForm" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" 
                                   value="<?php echo $_POST['email'] ?? ''; ?>" required>
                            <div class="invalid-feedback" id="loginEmailError"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="loginPasswordError"></div>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="forget-password.php" class="text-decoration-none">Forgot Password?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    
                    <!-- Login with Google (placeholder) -->
                    <div class="mt-3">
                        <button class="btn btn-outline-danger w-100">
                            <i class="fab fa-google"></i> Sign in with Google
                        </button>
                    </div>
                    
                    <hr>
                    <p class="text-center mb-0">Don't have an account? <a href="register.php" class="text-decoration-none">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    
    // Password toggle
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const input = document.querySelector('input[name="password"]');
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate email
        const email = document.querySelector('input[name="email"]');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            email.classList.add('is-invalid');
            document.getElementById('loginEmailError').textContent = 'Email is required.';
            isValid = false;
        } else if (!emailRegex.test(email.value)) {
            email.classList.add('is-invalid');
            document.getElementById('loginEmailError').textContent = 'Please enter a valid email address.';
            isValid = false;
        } else {
            email.classList.remove('is-invalid');
        }
        
        // Validate password
        const password = document.querySelector('input[name="password"]');
        if (!password.value.trim()) {
            password.classList.add('is-invalid');
            document.getElementById('loginPasswordError').textContent = 'Password is required.';
            isValid = false;
        } else {
            password.classList.remove('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>