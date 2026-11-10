<?php
$page_title = 'Register';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';
require_once __DIR__ . '/config/validation.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$old = is_array($_SESSION['old'] ?? null) ? $_SESSION['old'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldFormData = $_POST;
    unset($oldFormData['password'], $oldFormData['confirm_password'], $oldFormData['csrf_token']);
    $requiredScalarFields = ['full_name', 'email', 'phone', 'password', 'confirm_password', 'role'];
    $registrationFieldsValid = true;
    foreach ($requiredScalarFields as $field) {
        if (!is_string($_POST[$field] ?? null)) {
            $registrationFieldsValid = false;
            break;
        }
    }
    if (isset($_POST['location']) && !is_string($_POST['location'])) {
        $registrationFieldsValid = false;
    }
    if (isset($_POST['terms']) && !is_string($_POST['terms'])) {
        $registrationFieldsValid = false;
    }

    if (!$registrationFieldsValid) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Please check the submitted registration details and try again.',
        ];
        $_SESSION['old'] = $oldFormData;
    } elseif (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Your registration request expired. Please try again.',
        ];
        $_SESSION['old'] = $oldFormData;
    } else {
        $validator = new Validator($_POST);
        $validator
            ->required('full_name', 'Full name is required.')
            ->minLength('full_name', 2, 'Full name must be at least 2 characters.')
            ->maxLength('full_name', 100, 'Full name must not exceed 100 characters.')
            ->required('email', 'Email address is required.')
            ->email('email', 'Please enter a valid email address.')
            ->unique('email', 'users', 'email', 'This email is already registered.')
            ->required('phone', 'Contact number is required.')
            ->required('password', 'Password is required.')
            ->password('password', 'Password must be at least 8 characters and contain uppercase, lowercase, number, and special character.')
            ->confirmed('password', 'confirm_password', 'Passwords do not match.')
            ->required('role', 'Please select Fresher or Mentor.')
            ->inArray('role', ['fresher', 'mentor'], 'Invalid role selected.')
            ->required('terms', 'You must agree to the terms and conditions.');

        $phone = trim((string)$_POST['phone']);
        $phoneDigits = preg_replace('/\D+/', '', $phone);
        if ($phone !== '' && (strlen($phone) > 20 || strlen($phoneDigits) < 7 || strlen($phoneDigits) > 15)) {
            $validator->addError('phone', 'Please enter a valid contact number.');
        }

        if ($validator->passes()) {
            $fullName = trim(sanitize($_POST['full_name']));
            $email = trim(sanitize($_POST['email']));
            $role = $_POST['role'];
            $location = trim(sanitize($_POST['location'] ?? ''));
            $passwordHash = password_hash((string)$_POST['password'], PASSWORD_DEFAULT);

            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password, role, phone, location, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)');
                $stmt->execute([$fullName, $email, $passwordHash, $role, $phone, $location !== '' ? $location : null]);
                $userId = (int)$pdo->lastInsertId();
                $pdo->commit();

                // Sign the new account in and send it directly to the correct role dashboard.
                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;
                $_SESSION['full_name'] = $fullName;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                unset($_SESSION['old']);
                redirect($role === 'mentor' ? 'mentor/dashboard.php' : 'fresher/dashboard.php');
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log('Registration failed: ' . $e->getMessage());
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'icon' => 'exclamation-circle',
                    'message' => 'We could not complete your registration. Please check your details and try again.',
                ];
                $_SESSION['old'] = $oldFormData;
            }
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'message' => $validator->errorsString(),
            ];
            $_SESSION['old'] = $oldFormData;
        }
    }
    $old = is_array($_SESSION['old'] ?? null) ? $_SESSION['old'] : [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SkillShare Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/landing.css">
    <link rel="stylesheet" href="assets/css/auth-pages.css?v=20261101-auth-register-v3">
    <link rel="stylesheet" href="assets/css/mobile-responsive.css?v=20261104-responsive-v1">
</head>
<body class="login-body register-body">
<div class="auth-theme-picker dropdown">
    <button class="theme-toggle-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Theme switcher">
        <i class="fas fa-circle-half-stroke" aria-hidden="true"></i>
        <span class="theme-label">System</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end theme-menu">
        <li><button type="button" class="dropdown-item theme-option" data-theme-option="light"><i class="fas fa-sun"></i> Light</button></li>
        <li><button type="button" class="dropdown-item theme-option" data-theme-option="dark"><i class="fas fa-moon"></i> Dark</button></li>
        <li><button type="button" class="dropdown-item theme-option" data-theme-option="system"><i class="fas fa-desktop"></i> System</button></li>
    </ul>
</div>
<main class="login-page register-page">
    <div class="login-shell register-shell">
        <aside class="login-brand-panel register-brand-panel">
            <a class="login-brand register-brand" href="index.php">
                <span class="login-brand-icon register-brand-icon"><img src="assets/images/logo/skillshare-hub-mark.svg" alt=""></span>
                <span>SkillShare <strong>Hub</strong></span>
            </a>
            <div class="login-brand-copy register-brand-copy">
                <span class="login-eyebrow register-eyebrow">Learn. Share. Grow.</span>
                <h1>Build skills.<br><span>Open doors.</span></h1>
                <p>Join a community where freshers and mentors share knowledge and grow together.</p>
            </div>
            <div class="login-brand-foot register-brand-foot"><i class="fas fa-sparkles"></i> Your next chapter starts here.</div>
        </aside>

        <section class="login-panel register-form-panel" aria-labelledby="registerTitle">
            <div class="register-panel-inner">
                <div class="register-topline">
                    <span>CREATE YOUR ACCOUNT</span>
                    <a href="login.php">Already a member? <strong>Sign in</strong></a>
                </div>
                <div class="register-form-scroll">
                    <div class="form-modern">
                        <div class="register-heading">
                            <h2 id="registerTitle">Create your account</h2>
                            <p>Choose your role and get started. Add your academic interests after signup.</p>
                        </div>

                        <?php if (isset($_SESSION['alert'])): ?>
                            <div class="alert alert-<?php echo htmlspecialchars((string)($_SESSION['alert']['type'] ?? 'info'), ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?php echo htmlspecialchars((string)($_SESSION['alert']['icon'] ?? 'info-circle'), ENT_QUOTES, 'UTF-8'); ?> me-1"></i>
                                <?php echo htmlspecialchars((string)($_SESSION['alert']['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['alert']); ?>
                        <?php endif; ?>

                        <form method="POST" id="registerForm" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                            <fieldset class="register-role-picker">
                                <legend class="form-label-modern">Choose your account type <span class="text-danger">*</span></legend>
                                <div class="register-role-options">
                                    <label class="register-role-option <?php echo ($old['role'] ?? '') === 'fresher' ? 'selected' : ''; ?>">
                                        <input type="radio" name="role" value="fresher" <?php echo ($old['role'] ?? '') === 'fresher' ? 'checked' : ''; ?> required>
                                        <i class="fas fa-user-graduate" aria-hidden="true"></i>
                                        <span><strong>Fresher</strong><small>Learn and grow your skills</small></span>
                                    </label>
                                    <label class="register-role-option <?php echo ($old['role'] ?? '') === 'mentor' ? 'selected' : ''; ?>">
                                        <input type="radio" name="role" value="mentor" <?php echo ($old['role'] ?? '') === 'mentor' ? 'checked' : ''; ?> required>
                                        <i class="fas fa-chalkboard-user" aria-hidden="true"></i>
                                        <span><strong>Mentor</strong><small>Teach and share expertise</small></span>
                                    </label>
                                </div>
                                <div class="invalid-feedback" id="registerRoleError"></div>
                            </fieldset>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label-modern" for="registerName">Full Name <span class="text-danger">*</span></label>
                                    <input id="registerName" type="text" name="full_name" class="form-control-modern" autocomplete="name" placeholder="Enter your full name" maxlength="100" value="<?php echo htmlspecialchars((string)($old['full_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                    <div class="invalid-feedback" id="registerNameError"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-modern" for="registerEmail">Email <span class="text-danger">*</span></label>
                                    <input id="registerEmail" type="email" name="email" class="form-control-modern" autocomplete="email" placeholder="you@example.com" maxlength="100" value="<?php echo htmlspecialchars((string)($old['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                    <div class="invalid-feedback" id="registerEmailError"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-modern" for="registerPhone">Contact Number <span class="text-danger">*</span></label>
                                    <input id="registerPhone" type="tel" name="phone" class="form-control-modern" autocomplete="tel" placeholder="+1 555 123 4567" maxlength="20" value="<?php echo htmlspecialchars((string)($old['phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                    <div class="invalid-feedback" id="registerPhoneError"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-modern" for="registerAddress">Address <span class="text-muted fw-normal">(optional)</span></label>
                                    <input id="registerAddress" type="text" name="location" class="form-control-modern" autocomplete="street-address" placeholder="City, Country" maxlength="100" value="<?php echo htmlspecialchars((string)($old['location'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label-modern" for="registerPassword">Password <span class="text-danger">*</span></label>
                                    <div class="register-password-wrap">
                                        <input id="registerPassword" type="password" name="password" class="form-control-modern" autocomplete="new-password" placeholder="At least 8 characters" required>
                                        <button type="button" class="register-password-toggle" data-password-target="registerPassword" aria-label="Show password"><i class="fas fa-eye" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="invalid-feedback" id="registerPasswordError"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-modern" for="registerConfirmPassword">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="register-password-wrap">
                                        <input id="registerConfirmPassword" type="password" name="confirm_password" class="form-control-modern" autocomplete="new-password" placeholder="Confirm your password" required>
                                        <button type="button" class="register-password-toggle" data-password-target="registerConfirmPassword" aria-label="Show confirm password"><i class="fas fa-eye" aria-hidden="true"></i></button>
                                    </div>
                                    <div class="invalid-feedback" id="registerConfirmPasswordError"></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="terms" class="form-check-input" id="registerTerms" value="1" <?php echo !empty($old['terms']) ? 'checked' : ''; ?> required>
                                        <label class="form-check-label" for="registerTerms">I agree to the <a href="terms.php" target="_blank" rel="noopener">Terms of Service</a> and <a href="privacy-policy.php" target="_blank" rel="noopener">Privacy Policy</a>.</label>
                                        <div class="invalid-feedback" id="registerTermsError"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn login-submit w-100">Create account <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </form>
                        <p class="login-register mt-4">Already have an account? <a href="login.php">Sign in</a></p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<script>
document.querySelectorAll('.register-password-toggle').forEach(function (button) {
    button.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.passwordTarget);
        const icon = this.querySelector('i');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.setAttribute('aria-label', (show ? 'Hide ' : 'Show ') + (input.id === 'registerPassword' ? 'password' : 'confirm password'));
        icon.classList.toggle('fa-eye', !show);
        icon.classList.toggle('fa-eye-slash', show);
    });
});
document.querySelectorAll('.register-role-option input').forEach(function (radio) {
    radio.addEventListener('change', function () {
        document.querySelectorAll('.register-role-option').forEach(label => label.classList.toggle('selected', label.contains(radio) && radio.checked));
        document.querySelector('.register-role-picker').classList.remove('is-invalid');
        document.getElementById('registerRoleError').textContent = '';
    });
});

const registerForm = document.getElementById('registerForm');
const registerName = document.getElementById('registerName');
const registerEmail = document.getElementById('registerEmail');
const registerPhone = document.getElementById('registerPhone');
const registerPassword = document.getElementById('registerPassword');
const registerConfirmPassword = document.getElementById('registerConfirmPassword');
const registerTerms = document.getElementById('registerTerms');

function setRegistrationError(input, errorId, message) {
    input.classList.toggle('is-invalid', Boolean(message));
    document.getElementById(errorId).textContent = message;
}

function validateRegistrationForm() {
    let valid = true;
    const rolePicker = document.querySelector('.register-role-picker');
    const roleSelected = Boolean(document.querySelector('input[name="role"]:checked'));
    rolePicker.classList.toggle('is-invalid', !roleSelected);
    document.getElementById('registerRoleError').textContent = roleSelected ? '' : 'Choose Fresher or Mentor.';
    valid = roleSelected && valid;

    const fullName = registerName.value.trim();
    const nameError = fullName.length < 2 ? 'Enter your full name (at least 2 characters).' : (fullName.length > 100 ? 'Name must be 100 characters or fewer.' : '');
    setRegistrationError(registerName, 'registerNameError', nameError);
    valid = !nameError && valid;

    const emailError = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(registerEmail.value.trim()) ? '' : 'Enter a valid email address.';
    setRegistrationError(registerEmail, 'registerEmailError', emailError);
    valid = !emailError && valid;

    const phoneDigits = registerPhone.value.replace(/\D/g, '');
    const phoneError = registerPhone.value.trim().length > 20 || phoneDigits.length < 7 || phoneDigits.length > 15
        ? 'Enter a valid contact number with 7–15 digits.'
        : '';
    setRegistrationError(registerPhone, 'registerPhoneError', phoneError);
    valid = !phoneError && valid;

    const passwordStrong = registerPassword.value.length >= 8
        && /[A-Z]/.test(registerPassword.value)
        && /[a-z]/.test(registerPassword.value)
        && /\d/.test(registerPassword.value)
        && /[^A-Za-z0-9]/.test(registerPassword.value);
    const passwordError = passwordStrong ? '' : 'Use at least 8 characters with uppercase, lowercase, a number, and a symbol.';
    setRegistrationError(registerPassword, 'registerPasswordError', passwordError);
    valid = !passwordError && valid;

    const confirmError = registerConfirmPassword.value && registerConfirmPassword.value === registerPassword.value
        ? ''
        : 'Passwords do not match.';
    setRegistrationError(registerConfirmPassword, 'registerConfirmPasswordError', confirmError);
    valid = !confirmError && valid;

    const termsError = registerTerms.checked ? '' : 'You must agree to the Terms of Service and Privacy Policy.';
    registerTerms.classList.toggle('is-invalid', Boolean(termsError));
    document.getElementById('registerTermsError').textContent = termsError;
    valid = !termsError && valid;

    return valid;
}

registerForm.addEventListener('submit', function (event) {
    if (!validateRegistrationForm()) {
        event.preventDefault();
        const firstInvalid = registerForm.querySelector('input.is-invalid') || registerForm.querySelector('.register-role-picker.is-invalid input');
        if (firstInvalid) firstInvalid.focus();
    }
});

registerName.addEventListener('input', () => setRegistrationError(registerName, 'registerNameError', ''));
registerEmail.addEventListener('input', () => setRegistrationError(registerEmail, 'registerEmailError', ''));
registerPhone.addEventListener('input', () => setRegistrationError(registerPhone, 'registerPhoneError', ''));
registerPassword.addEventListener('input', function () {
    setRegistrationError(registerPassword, 'registerPasswordError', '');
    if (registerConfirmPassword.value) setRegistrationError(registerConfirmPassword, 'registerConfirmPasswordError', '');
});
registerConfirmPassword.addEventListener('input', () => setRegistrationError(registerConfirmPassword, 'registerConfirmPasswordError', ''));
registerTerms.addEventListener('change', function () {
    this.classList.remove('is-invalid');
    document.getElementById('registerTermsError').textContent = '';
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
