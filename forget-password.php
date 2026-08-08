<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | SkillShare Hub</title>

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/main.css">
    <link rel="stylesheet" href="frontend/assets/css/login.css">
    <link rel="stylesheet" href="frontend/assets/css/responsive.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="auth-body">

    <?php include 'frontend/components/loader.php'; ?>

    <div class="auth-wrap">
        <div class="auth-card center-card">
            <div class="auth-right" style="max-width:520px;margin:0 auto;width:100%;">
                <div class="auth-form-wrap">
                    <a href="index.php" class="auth-brand" style="justify-content:center;margin-bottom:var(--spacing-5);">
                        <div class="brand-logo"><i class="fa-solid fa-graduation-cap"></i></div>
                        <span>SkillShare <span>Hub</span></span>
                    </a>
                    <div style="text-align:center;margin-bottom:var(--spacing-5);">
                        <div class="forgot-icon" style="width:70px;height:70px;margin:0 auto var(--spacing-4);border-radius:50%;background:var(--primary-soft);display:flex;align-items:center;justify-content:center;font-size:1.8rem;color:var(--primary);">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <h3>Forgot Password?</h3>
                        <p class="auth-sub">No worries! Enter your email and we'll send you a reset link.</p>
                    </div>

                    <form action="#" method="post" data-validate>
                        <div class="form-group">
                            <label class="form-label" for="fpEmail">Email Address</label>
                            <div class="input-group">
                                <i class="fa-solid fa-envelope input-icon"></i>
                                <input type="email" class="form-control" id="fpEmail" name="email" placeholder="you@example.com" data-validate="required|email" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fa-solid fa-paper-plane"></i> Send Reset Link
                        </button>
                    </form>

                    <p class="auth-switch" style="text-align:center;margin-top:var(--spacing-5);">
                        <a href="login.php"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/assets/js/main.js"></script>
    <script src="frontend/assets/js/validation.js"></script>

</body>

</html>
