<?php
/**
 * Authentication Controller
 * Handles user authentication, registration, and password management
 */

class AuthController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function loginForm()
    {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
        }
        $data = [
            'title' => 'Login',
            'csrf_token' => generateCSRFToken()
        ];
        $this->render('auth/login', $data);
    }

    public function login()
    {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
        }

        checkCSRF();

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';

        // Validate
        $errors = [];
        if (empty($email) || !validateEmail($email)) {
            $errors['email'] = 'Valid email is required';
        }
        if (empty($password) || !validatePassword($password)) {
            $errors['password'] = 'Password is required';
        }

        if (!empty($errors)) {
            setFlash('errors', $errors);
            setFlash('old', ['email' => $email]);
            redirectBack();
            return;
        }

        // Find user
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [$email]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            setFlash('error', 'Invalid email or password');
            redirectBack();
            return;
        }

        if ($user['is_verified'] == 0) {
            setFlash('error', 'Please verify your email first');
            redirectBack();
            return;
        }

        // Login
        $this->loginUser($user, $remember);
        $this->redirectToDashboard($user['role']);
    }

    public function registerForm()
    {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
        }
        $data = [
            'title' => 'Register',
            'csrf_token' => generateCSRFToken(),
            'fields' => $this->db->fetchAll("SELECT * FROM academic_fields WHERE status = 'active' ORDER BY name")
        ];
        $this->render('auth/register', $data);
    }

    public function register()
    {
        if (isLoggedIn()) {
            $this->redirectToDashboard();
        }

        checkCSRF();

        $data = [
            'full_name' => sanitize($_POST['full_name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'role' => sanitize($_POST['role'] ?? 'fresher'),
            'academic_field' => sanitize($_POST['academic_field'] ?? ''),
            'course' => sanitize($_POST['course'] ?? ''),
            'agree_terms' => isset($_POST['agree_terms']) && $_POST['agree_terms'] === 'on'
        ];

        // Validate
        $errors = [];
        if (empty($data['full_name']) || strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Full name is required';
        }
        if (empty($data['email']) || !validateEmail($data['email'])) {
            $errors['email'] = 'Valid email is required';
        } elseif ($this->db->fetch("SELECT id FROM users WHERE email = ?", [$data['email']])) {
            $errors['email'] = 'Email already registered';
        }
        if (!empty($data['phone']) && !validatePhone($data['phone'])) {
            $errors['phone'] = 'Valid phone number required';
        }
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
        if (empty($data['academic_field'])) {
            $errors['academic_field'] = 'Academic field is required';
        }
        if (empty($data['course'])) {
            $errors['course'] = 'Course is required';
        }
        if (!$data['agree_terms']) {
            $errors['agree_terms'] = 'You must agree to the terms';
        }

        if (!empty($errors)) {
            setFlash('errors', $errors);
            setFlash('old', $data);
            redirectBack();
            return;
        }

        // Create user
        $verificationToken = bin2hex(random_bytes(32));
        $userId = $this->db->insert('users', [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => PASSWORD_BCRYPT_ROUNDS]),
            'role' => $data['role'],
            'academic_field' => $data['academic_field'],
            'course' => $data['course'],
            'is_verified' => 0,
            'verification_token' => $verificationToken,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($userId) {
            // Create role specific record
            if ($data['role'] === 'mentor') {
                $this->db->insert('mentors', ['user_id' => $userId]);
            } elseif ($data['role'] === 'fresher') {
                $this->db->insert('freshers', ['user_id' => $userId]);
            }

            // Send verification email (placeholder)
            // $this->sendVerificationEmail($data['email'], $verificationToken);

            setFlash('success', 'Registration successful! Please check your email.');
            redirect(APP_URL . 'login');
        } else {
            setFlash('error', 'Registration failed. Please try again.');
            redirectBack();
        }
    }

    public function logout()
    {
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        session_destroy();
        setFlash('success', 'Logged out successfully');
        redirect(APP_URL . 'login');
    }

    public function verify($token)
    {
        $user = $this->db->fetch(
            "SELECT id FROM users WHERE verification_token = ? AND is_verified = 0",
            [$token]
        );

        if ($user) {
            $this->db->update('users', [
                'is_verified' => 1,
                'verification_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$user['id']]);
            setFlash('success', 'Email verified successfully! Please login.');
        } else {
            setFlash('error', 'Invalid verification token');
        }

        redirect(APP_URL . 'login');
    }

    public function forgotPasswordForm()
    {
        $this->render('auth/forgot-password', ['title' => 'Forgot Password']);
    }

    public function forgotPassword()
    {
        $email = sanitize($_POST['email'] ?? '');
        if (empty($email) || !validateEmail($email)) {
            setFlash('error', 'Valid email is required');
            redirectBack();
            return;
        }

        $user = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->db->update('users', [
                'reset_token' => $token,
                'reset_token_expiry' => date('Y-m-d H:i:s', time() + 3600)
            ], 'id = ?', [$user['id']]);

            // Send reset email (placeholder)
            // $this->sendPasswordResetEmail($email, $token);

            setFlash('success', 'Password reset link sent to your email');
        } else {
            setFlash('success', 'If your email is registered, you will receive a reset link');
        }

        redirect(APP_URL . 'login');
    }

    public function resetPasswordForm($token)
    {
        $user = $this->db->fetch(
            "SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()",
            [$token]
        );

        if (!$user) {
            setFlash('error', 'Invalid or expired reset token');
            redirect(APP_URL . 'forgot-password');
            return;
        }

        $this->render('auth/reset-password', ['title' => 'Reset Password', 'token' => $token]);
    }

    public function resetPassword()
    {
        $token = sanitize($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (empty($password) || strlen($password) < 6) {
            setFlash('error', 'Password must be at least 6 characters');
            redirectBack();
            return;
        }

        if ($password !== $password_confirm) {
            setFlash('error', 'Passwords do not match');
            redirectBack();
            return;
        }

        $user = $this->db->fetch(
            "SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()",
            [$token]
        );

        if (!$user) {
            setFlash('error', 'Invalid or expired reset token');
            redirect(APP_URL . 'forgot-password');
            return;
        }

        $this->db->update('users', [
            'password_hash' => password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_BCRYPT_ROUNDS]),
            'reset_token' => null,
            'reset_token_expiry' => null
        ], 'id = ?', [$user['id']]);

        setFlash('success', 'Password reset successfully. Please login.');
        redirect(APP_URL . 'login');
    }

    // Alias for existing API endpoint compatibility
    public function apiLogin()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true) ?: $_POST;

        $email = sanitize($data['email'] ?? '');
        $password = $data['password'] ?? '';

        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND status = 'active' AND is_verified = 1",
            [$email]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            errorResponse('Invalid email or password', 401);
            return;
        }

        $this->loginUser($user, false);
        unset($user['password_hash'], $user['verification_token'], $user['reset_token'], $user['remember_token']);
        successResponse($user, 'Login successful');
    }

    // Alias for existing API endpoint compatibility
    public function apiRegister()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true) ?: $_POST;

        $fullName = sanitize($data['full_name'] ?? $data['name'] ?? '');
        $email = sanitize($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $role = sanitize($data['role'] ?? 'fresher');

        if (empty($fullName) || empty($email) || strlen($password) < 6) {
            errorResponse('Invalid registration data', 422);
            return;
        }

        if ($this->db->fetch("SELECT id FROM users WHERE email = ?", [$email])) {
            errorResponse('Email already registered', 422);
            return;
        }

        $verificationToken = bin2hex(random_bytes(32));
        $userId = $this->db->insert('users', [
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_BCRYPT_ROUNDS]),
            'role' => $role,
            'is_verified' => 1,
            'verification_token' => $verificationToken,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if (!$userId) {
            errorResponse('Registration failed', 500);
            return;
        }

        if ($role === 'mentor') {
            $this->db->insert('mentors', ['user_id' => $userId]);
        } elseif ($role === 'fresher') {
            $this->db->insert('freshers', ['user_id' => $userId]);
        }

        successResponse(['user_id' => $userId], 'Registration successful');
    }

    // Alias for existing API endpoint compatibility
    public function getCurrentUser()
    {
        if (!isLoggedIn()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [getUserId()]);
        if (!$user) {
            errorResponse('User not found', 404);
            return;
        }

        unset($user['password_hash'], $user['verification_token'], $user['reset_token'], $user['remember_token']);
        successResponse($user);
    }

    private function loginUser($user, $remember = false)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['profile_picture'] ?? 'default.png';

        $this->db->update('users', [
            'last_login' => date('Y-m-d H:i:s'),
            'last_ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ], 'id = ?', [$user['id']]);

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->db->update('users', [
                'remember_token' => $token,
                'remember_expiry' => date('Y-m-d H:i:s', time() + REMEMBER_ME_LIFETIME)
            ], 'id = ?', [$user['id']]);
            setcookie('remember_token', $token, time() + REMEMBER_ME_LIFETIME, '/');
        }
    }

    private function redirectToDashboard($role = null)
    {
        if (!$role) {
            $role = getUserRole();
        }
        $url = APP_URL . 'dashboard';
        if ($role === 'admin') $url = APP_URL . 'admin';
        elseif ($role === 'mentor') $url = APP_URL . 'mentor/dashboard';
        elseif ($role === 'fresher') $url = APP_URL . 'fresher/dashboard';
        redirect($url);
    }

    private function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}
