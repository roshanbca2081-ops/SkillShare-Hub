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
    if (!is_string($_POST['email'] ?? null) || !is_string($_POST['password'] ?? null)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Please enter a valid email address and password.',
        ];
    } elseif (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Your sign-in request expired. Please try again.',
        ];
    } else {
        $validator = new Validator($_POST);
        $validator
            ->required('email', 'Email is required.')
            ->email('email', 'Please enter a valid email.')
            ->required('password', 'Password is required.');
    
        if ($validator->passes()) {
            $email = trim((string)$_POST['email']);
            $password = (string)$_POST['password'];
            $loginQueryFailed = false;
        
            try {
                $stmt = $pdo->prepare('SELECT id, full_name, email, password, role, is_active FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                $user = $stmt->fetch();
            } catch (PDOException $e) {
                error_log('Login query failed: ' . $e->getMessage());
                $loginQueryFailed = true;
                $user = null;
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'icon' => 'exclamation-circle',
                    'message' => 'Sign-in is temporarily unavailable. Please try again later.',
                ];
            }
        
            if ($loginQueryFailed) {
                // Keep the generic service-unavailable message set in the catch block.
            } elseif ($user && password_verify($password, $user['password'])) {
                if ((int)$user['is_active'] !== 1) {
                    $_SESSION['alert'] = [
                        'type' => 'danger',
                        'icon' => 'exclamation-circle',
                        'message' => 'This account is disabled. Contact support for help.',
                    ];
                } else {
                    session_regenerate_id(true);
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
                        case 'admin': redirect('admin/dashboard.php');
                        case 'mentor': redirect('mentor/dashboard.php');
                        case 'fresher': redirect('fresher/dashboard.php');
                        default:
                            unset($_SESSION['user_id'], $_SESSION['full_name'], $_SESSION['role'], $_SESSION['email']);
                            $_SESSION['alert'] = [
                                'type' => 'danger',
                                'icon' => 'exclamation-circle',
                                'message' => 'This account has an invalid role. Contact support for help.',
                            ];
                            redirect('login.php');
                    }
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SkillShare Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/auth-pages.css?v=20261031-auth-v3">
    <style>
        :root { --auth-ink: #172033; --auth-muted: #667085; --auth-primary: #4f46e5; }
        * { box-sizing: border-box; }
        body.login-body { width: 100%; height: 100vh; height: 100dvh; margin: 0; overflow: hidden; color: var(--auth-ink); background: #f3f5ff; font-family: 'DM Sans', sans-serif; }
        .login-page { display: grid; width: 100%; height: 100%; place-items: center; padding: 24px; background: radial-gradient(circle at 8% 12%, rgba(124,58,237,.13), transparent 30%), radial-gradient(circle at 93% 90%, rgba(6,182,212,.12), transparent 27%), linear-gradient(145deg,#f8faff,#eef2ff 52%,#ecfeff); }
        .login-shell { display: grid; width: min(1000px,100%); height: min(680px,100%); min-height: 500px; grid-template-columns: minmax(290px,.85fr) minmax(0,1.15fr); overflow: hidden; background: #fff; border: 1px solid rgba(255,255,255,.92); border-radius: 28px; box-shadow: 0 28px 90px rgba(34,41,95,.18); }
        .login-brand-panel { position: relative; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; padding: clamp(30px,4vw,50px); color: #fff; background: radial-gradient(circle at 88% 18%,rgba(34,211,238,.26),transparent 30%),radial-gradient(circle at 12% 90%,rgba(167,139,250,.38),transparent 34%),linear-gradient(145deg,#28246f,#4f46e5 58%,#6d42c7); }
        .login-brand-panel::after { position: absolute; right: -110px; bottom: 60px; width: 280px; height: 280px; border: 1px solid rgba(255,255,255,.16); border-radius: 50%; content: ''; box-shadow: 0 0 0 36px rgba(255,255,255,.035),0 0 0 72px rgba(255,255,255,.025); }
        .login-brand { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 12px; width: fit-content; color: #fff; font: 700 1.16rem 'Space Grotesk',sans-serif; text-decoration: none; }
        .login-brand:hover { color: #fff; }
        .login-brand strong { color: #a5f3fc; }
        .login-brand-icon { display: grid; width: 44px; height: 44px; place-items: center; color: var(--auth-primary); background: #fff; border-radius: 14px; font-size: 1.2rem; }
        .login-brand-copy,.login-brand-foot { position: relative; z-index: 1; }
        .login-eyebrow { color: #a5f3fc; font-size: .73rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .login-brand-copy h1 { margin: 18px 0; color: #fff; font: 700 clamp(2.4rem,4vw,3.55rem)/1.06 'Space Grotesk',sans-serif; letter-spacing: -.05em; }
        .login-brand-copy h1 span { color: #a5f3fc; }
        .login-brand-copy p { max-width: 350px; margin: 0; color: rgba(255,255,255,.8); font-size: .96rem; line-height: 1.8; }
        .login-brand-foot { color: rgba(255,255,255,.84); font-size: .82rem; font-weight: 600; }
        .login-brand-foot i { margin-right: 7px; color: #a5f3fc; }
        .login-panel { display: flex; min-width: 0; flex-direction: column; justify-content: center; overflow-y: auto; padding: clamp(28px,5vw,64px); overscroll-behavior: contain; }
        .login-panel-inner { width: 100%; max-width: 410px; margin: auto; }
        .login-topline { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 30px; color: #98a2b3; font-size: .68rem; font-weight: 800; letter-spacing: .1em; }
        .login-topline a { color: #667085; font-size: .76rem; font-weight: 600; letter-spacing: 0; text-decoration: none; }
        .login-topline a:hover { color: var(--auth-primary); }
        .login-heading h2 { margin: 0 0 7px; color: var(--auth-ink); font: 700 clamp(1.8rem,3vw,2.25rem) 'Space Grotesk',sans-serif; letter-spacing: -.045em; }
        .login-heading p { margin: 0 0 28px; color: var(--auth-muted); font-size: .9rem; }
        .login-form .form-label { margin-bottom: 8px; color: #344054; font-size: .82rem; font-weight: 700; }
        .login-form .form-control { min-height: 48px; padding: 11px 14px; color: var(--auth-ink); background: #f9faff; border: 1px solid #e1e5f2; border-radius: 11px; font-size: .9rem; }
        .login-form .form-control:focus { background: #fff; border-color: var(--auth-primary); box-shadow: 0 0 0 4px rgba(79,70,229,.1); }
        .login-form .input-group .form-control { border-radius: 11px 0 0 11px; }
        .login-form .input-group .toggle-password { min-width: 48px; color: #667085; background: #f9faff; border: 1px solid #e1e5f2; border-left: 0; border-radius: 0 11px 11px 0; }
        .login-form .form-check-label,.login-form .forgot-link { color: #667085; font-size: .78rem; }
        .login-form .forgot-link { color: var(--auth-primary); font-weight: 700; text-decoration: none; }
        .login-submit { min-height: 49px; margin-top: 7px; color: #fff; background: linear-gradient(135deg,#4f46e5,#7c3aed); border: 0; border-radius: 11px; box-shadow: 0 9px 22px rgba(79,70,229,.23); font-weight: 700; transition: transform .18s ease, box-shadow .18s ease; }
        .login-submit:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 12px 26px rgba(79,70,229,.32); }
        .login-register { margin: 0; color: var(--auth-muted); font-size: .84rem; text-align: center; }
        .login-register a { color: var(--auth-primary); font-weight: 700; text-decoration: none; }
        .login-alert { margin-bottom: 18px; border-radius: 11px; font-size: .83rem; }
        .invalid-feedback { font-size: .78rem; }
        .login-panel,.login-panel::-webkit-scrollbar { scrollbar-width: none; }
        .login-panel::-webkit-scrollbar { display: none; width: 0; }
        @media (max-width: 760px) {
            .login-page { padding: 14px; }
            .login-shell { grid-template-columns: minmax(215px,.72fr) minmax(0,1.28fr); }
            .login-brand-panel { padding: 27px 22px; }
            .login-panel { padding: 30px; }
        }
        @media (max-width: 600px) {
            .login-page { padding: 0; place-items: stretch; }
            .login-shell { width: 100%; height: 100%; min-height: 0; display: block; border: 0; border-radius: 0; }
            .login-brand-panel { display: none; }
            .login-panel { width: 100%; height: 100%; padding: max(24px,env(safe-area-inset-top)) 24px max(24px,env(safe-area-inset-bottom)); }
            .login-panel-inner { max-width: 430px; }
            .login-topline { margin-bottom: 26px; }
        }
        @media (max-height: 620px) and (min-width: 601px) {
            .login-page { padding-top: 12px; padding-bottom: 12px; }
            .login-shell { height: 100%; min-height: 0; }
            .login-panel { padding-top: 22px; padding-bottom: 22px; }
            .login-topline { margin-bottom: 16px; }
            .login-heading p { margin-bottom: 18px; }
        }
        @media (max-height: 560px) and (max-width: 600px) {
            .login-panel { justify-content: flex-start; }
            .login-topline { margin-bottom: 18px; }
            .login-heading p { margin-bottom: 18px; }
        }
        @media (prefers-reduced-motion: reduce) { *,*::before,*::after { scroll-behavior: auto !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body class="login-body">
<main class="login-page">
    <div class="login-shell">
        <aside class="login-brand-panel">
            <a class="login-brand" href="index.php">
                <span class="login-brand-icon"><img src="assets/images/logo/skillshare-hub-mark.svg" alt=""></span>
                <span>SkillShare <strong>Hub</strong></span>
            </a>
            <div class="login-brand-copy">
                <span class="login-eyebrow">Learn. Share. Grow.</span>
                <h1>Welcome<br><span>back.</span></h1>
                <p>Pick up where you left off. Your next lesson, a new mentor, and fresh opportunities are waiting.</p>
            </div>
            <div class="login-brand-foot"><i class="fas fa-sparkles"></i> Keep growing, one skill at a time.</div>
        </aside>
        <section class="login-panel" aria-labelledby="loginTitle">
            <div class="login-panel-inner">
                <div class="login-topline"><span>MEMBER SIGN IN</span><a href="index.php"><i class="fas fa-arrow-left me-1"></i> Home</a></div>
                <div class="login-heading">
                    <h2 id="loginTitle">Welcome back</h2>
                    <p>Sign in to continue your learning journey.</p>
                </div>

                <?php if (isset($_SESSION['alert'])): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['alert']['type'] ?? 'info', ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show login-alert" role="alert">
                        <i class="fas fa-<?php echo htmlspecialchars($_SESSION['alert']['icon'] ?? 'info-circle', ENT_QUOTES, 'UTF-8'); ?> me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['alert']['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['alert']); ?>
                <?php endif; ?>

                <?php if (isset($_GET['registered']) && $_GET['registered'] === 'success'): ?>
                    <div class="alert alert-success login-alert" role="status"><i class="fas fa-check-circle me-1"></i> Registration successful! Please sign in.</div>
                <?php endif; ?>

                <form method="POST" id="loginForm" class="login-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="loginEmail">Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="loginEmail" name="email" class="form-control" placeholder="you@example.com" autocomplete="username" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                        <div class="invalid-feedback" id="loginEmailError"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="loginPassword">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
                            <button type="button" class="btn toggle-password" aria-label="Show password"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="invalid-feedback" id="loginPasswordError"></div>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center gap-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <a href="forget-password.php" class="forgot-link">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn login-submit w-100">Sign in <i class="fas fa-arrow-right ms-2"></i></button>
                </form>

                <p class="login-register mt-4">Don't have an account? <a href="register.php">Create one</a></p>
            </div>
        </section>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const email = document.getElementById('loginEmail');
    const password = document.getElementById('loginPassword');
    const emailError = document.getElementById('loginEmailError');
    const passwordError = document.getElementById('loginPasswordError');

    function setFieldError(field, errorElement, message) {
        field.classList.toggle('is-invalid', Boolean(message));
        field.setAttribute('aria-invalid', message ? 'true' : 'false');
        errorElement.textContent = message;
    }

    function validateEmail() {
        email.value = email.value.trim();
        if (!email.value) {
            setFieldError(email, emailError, 'Email is required.');
            return false;
        }
        if (email.validity.typeMismatch || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            setFieldError(email, emailError, 'Please enter a valid email address.');
            return false;
        }
        setFieldError(email, emailError, '');
        return true;
    }

    function validatePassword() {
        if (!password.value.trim()) {
            setFieldError(password, passwordError, 'Password is required.');
            return false;
        }
        setFieldError(password, passwordError, '');
        return true;
    }

    // Password toggle
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const icon = this.querySelector('i');
        if (password.type === 'password') {
            password.type = 'text';
            this.setAttribute('aria-label', 'Hide password');
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            this.setAttribute('aria-label', 'Show password');
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    email.addEventListener('blur', validateEmail);
    password.addEventListener('blur', validatePassword);
    email.addEventListener('input', function() {
        if (email.classList.contains('is-invalid')) validateEmail();
    });
    password.addEventListener('input', function() {
        if (password.classList.contains('is-invalid')) validatePassword();
    });

    form.addEventListener('submit', function(e) {
        const emailIsValid = validateEmail();
        const passwordIsValid = validatePassword();
        if (!emailIsValid || !passwordIsValid) {
            e.preventDefault();
            (emailIsValid ? password : email).focus();
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>