<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | SkillShare Hub</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="frontend/assets/css/login.css">

    <style>
        /* So the login page fills the viewport centered */
        html, body { height: 100%; }
    </style>
</head>
<body class="auth-body">

<!-- ============================================
   BLURRED LEARNING BACKGROUND
   ============================================ -->
<div class="auth-bg">
    <div class="bg-image"></div>
    <div class="bg-animated"></div>
    <div class="bg-floating" style="top:8%;left:6%;animation-delay:0s;"><i class="fas fa-robot"></i></div>
    <div class="bg-floating" style="top:20%;right:10%;animation-delay:3s;"><i class="fas fa-laptop-code"></i></div>
    <div class="bg-floating" style="bottom:25%;left:8%;animation-delay:6s;"><i class="fas fa-flask"></i></div>
    <div class="bg-floating" style="bottom:15%;right:12%;animation-delay:9s;"><i class="fas fa-graduation-cap"></i></div>
    <div class="bg-floating" style="top:55%;left:20%;animation-delay:12s;"><i class="fas fa-microscope"></i></div>
</div>
<div class="floating-logo">SHARE SKILL HUB</div>

<!-- ============================================
   TOAST CONTAINER
   ============================================ -->
<div class="toast-container" id="toastContainer"></div>

<!-- ============================================
   AUTH CONTAINER (CENTERED)
   ============================================ -->
<div class="auth-container">
    <div class="auth-wrapper">

        <a href="index.php" class="auth-home-link"><i class="fas fa-arrow-left"></i> Back to Home</a>

        <!-- LOGIN FORM -->
        <div id="loginForm" class="auth-card">
            <div class="auth-brand">
                <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                <h1>ShareSkill Hub</h1>
                <p>Bridging Education with Industry</p>
                <div class="tagline">
                    <span>Learn</span><span>•</span><span>Connect</span><span>•</span><span>Grow</span>
                </div>
            </div>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['login_error']); ?></div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <div id="loginAlert"></div>

            <form id="loginFormElement" novalidate>
                <div class="form-group">
                    <label for="loginEmail">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="loginEmail" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="loginPassword" placeholder="Enter your password" required>
                        <span class="toggle-password" onclick="togglePassword('loginPassword', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" id="rememberMe"> Remember Me
                    </label>
                    <a href="forget-password.php" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-primary-auth">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="auth-divider"><span>OR</span></div>

            <div class="social-login">
                <button type="button" class="btn-social google" onclick="showToast('Coming Soon', 'Google login coming soon!', 'info')">
                    <span class="social-icon"><i class="fab fa-google"></i></span>
                    Continue with Google
                </button>
                <button type="button" class="btn-social facebook" onclick="showToast('Coming Soon', 'Facebook login coming soon!', 'info')">
                    <span class="social-icon"><i class="fab fa-facebook-f"></i></span>
                    Continue with Facebook
                </button>
            </div>

            <div class="auth-footer">
                Don't have an account? <a href="register.php">Create Account</a>
            </div>
        </div>

    </div>
</div>

<script src="frontend/assets/js/auth.js"></script>
</body>
</html>
