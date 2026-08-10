<?php
/**
 * Authentication Controller
 * Handles user authentication, registration, and password management
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/validator.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../config/mail.php';

class AuthController {
    private $userModel;
    private $authService;
    private $mailer;

    public function __construct() {
        $this->userModel = new User();
        $this->authService = new AuthService();
        $this->mailer = new MailConfig();
    }

    /**
     * Show login form
     */
    public function showLoginForm() {
        // If user is already logged in, redirect to dashboard
        if (AuthMiddleware::check()) {
            header('Location: /dashboard');
            exit;
        }

        // Include login form view
        include __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm() {
        // If user is already logged in, redirect to dashboard
        if (AuthMiddleware::check()) {
            header('Location: /dashboard');
            exit;
        }

        // Include registration form view
        include __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Handle user login
     */
    public function login() {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['errors']['general'] = 'Invalid request. Please try again.';
            header('Location: /login');
            exit;
        }

        // Get form data
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) ? true : false;

        // Validate input
        $errors = [];

        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /login');
            exit;
        }

        // Attempt login
        $user = $this->authService->login($email, $password, $remember);

        if ($user) {
            // Set flash message
            setFlashMessage('success', 'Welcome back, ' . htmlspecialchars($user->name) . '!');

            // Redirect to dashboard
            header('Location: /dashboard');
            exit;
        } else {
            // Set error message
            $_SESSION['errors']['general'] = 'Invalid email or password';
            $_SESSION['old_input'] = $_POST;
            header('Location: /login');
            exit;
        }
    }

    /**
     * Handle user registration
     */
    public function register() {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['errors']['general'] = 'Invalid request. Please try again.';
            header('Location: /register');
            exit;
        }

        // Get form data
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'fresher';

        // Validate input
        $errors = validateRegistrationInput($name, $email, $password, $confirm_password, $role);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /register');
            exit;
        }

        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            $_SESSION['errors']['email'] = 'This email is already registered';
            $_SESSION['old_input'] = $_POST;
            header('Location: /register');
            exit;
        }

        // Create user
        $user = $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'status' => 'pending',
            'email_verified' => false
        ]);

        if ($user) {
            // Send verification email
            $token = $this->authService->generateVerificationToken($user->id);
            $this->mailer->sendVerificationEmail($email, $token);

            // Set success message
            setFlashMessage('success', 'Registration successful! Please check your email to verify your account.');

            // Redirect to login page
            header('Location: /login');
            exit;
        } else {
            // Set error message
            $_SESSION['errors']['general'] = 'Registration failed. Please try again.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /register');
            exit;
        }
    }

    /**
     * Handle user logout
     */
    public function logout() {
        // Clear session data
        $this->authService->logout();

        // Set success message
        setFlashMessage('success', 'You have been logged out successfully.');

        // Redirect to login page
        header('Location: /login');
        exit;
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm() {
        // If user is already logged in, redirect to dashboard
        if (AuthMiddleware::check()) {
            header('Location: /dashboard');
            exit;
        }

        // Include forgot password form view
        include __DIR__ . '/../views/auth/forgot-password.php';
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword() {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['errors']['general'] = 'Invalid request. Please try again.';
            header('Location: /forgot-password');
            exit;
        }

        // Get form data
        $email = $_POST['email'] ?? '';

        // Validate input
        $errors = [];

        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /forgot-password');
            exit;
        }

        // Check if user exists
        $user = $this->userModel->findByEmail($email);

        if ($user) {
            // Generate reset token
            $token = $this->authService->generateResetToken($user->id);

            // Send reset email
            $this->mailer->sendPasswordResetEmail($email, $token);

            // Set success message
            setFlashMessage('success', 'Password reset instructions have been sent to your email.');
        } else {
            // Set success message (don't reveal if email exists or not)
            setFlashMessage('success', 'Password reset instructions have been sent to your email.');
        }

        // Redirect to login page
        header('Location: /login');
        exit;
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm() {
        // If user is already logged in, redirect to dashboard
        if (AuthMiddleware::check()) {
            header('Location: /dashboard');
            exit;
        }

        // Check if token is provided
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            setFlashMessage('error', 'Invalid reset token.');
            header('Location: /forgot-password');
            exit;
        }

        // Validate token
        $token = $_GET['token'];
        $userId = $this->authService->validateResetToken($token);

        if (!$userId) {
            setFlashMessage('error', 'Invalid or expired reset token.');
            header('Location: /forgot-password');
            exit;
        }

        // Include reset password form view
        include __DIR__ . '/../views/auth/reset-password.php';
    }

    /**
     * Handle password reset
     */
    public function resetPassword() {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['errors']['general'] = 'Invalid request. Please try again.';
            header('Location: /forgot-password');
            exit;
        }

        // Get form data
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate input
        $errors = validatePasswordResetInput($password, $confirm_password);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /reset-password?token=' . $token);
            exit;
        }

        // Validate token
        $userId = $this->authService->validateResetToken($token);

        if (!$userId) {
            setFlashMessage('error', 'Invalid or expired reset token.');
            header('Location: /forgot-password');
            exit;
        }

        // Update password
        $result = $this->userModel->updatePassword($userId, password_hash($password, PASSWORD_DEFAULT));

        if ($result) {
            // Invalidate the token
            $this->authService->invalidateResetToken($token);

            // Set success message
            setFlashMessage('success', 'Your password has been reset successfully. Please login.');

            // Redirect to login page
            header('Location: /login');
            exit;
        } else {
            // Set error message
            $_SESSION['errors']['general'] = 'Password reset failed. Please try again.';
            $_SESSION['old_input'] = $_POST;
            header('Location: /reset-password?token=' . $token);
            exit;
        }
    }

    /**
     * Verify user email
     */
    public function verifyEmail() {
        // Check if token is provided
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            setFlashMessage('error', 'Invalid verification token.');
            header('Location: /login');
            exit;
        }

        // Validate token
        $token = $_GET['token'];
        $userId = $this->authService->validateVerificationToken($token);

        if (!$userId) {
            setFlashMessage('error', 'Invalid or expired verification token.');
            header('Location: /login');
            exit;
        }

        // Mark email as verified
        $result = $this->userModel->markEmailAsVerified($userId);

        if ($result) {
            // Invalidate the token
            $this->authService->invalidateVerificationToken($token);

            // Set success message
            setFlashMessage('success', 'Your email has been verified successfully. Please login.');

            // Redirect to login page
            header('Location: /login');
            exit;
        } else {
            // Set error message
            setFlashMessage('error', 'Email verification failed. Please try again.');
            header('Location: /login');
            exit;
        }
    }

    /**
     * API: Get current user
     */
    public function getCurrentUser() {
        // Check if user is authenticated
        if (!AuthMiddleware::check()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // Get current user
        $user = AuthMiddleware::user();

        // Remove sensitive data
        unset($user->password);
        unset($user->remember_token);

        // Return user data
        echo json_encode(['user' => $user]);
        exit;
    }

    /**
     * API: Handle user login
     */
    public function apiLogin() {
        // Get JSON data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Validate input
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            exit;
        }

        // Attempt login
        $user = $this->authService->login($data['email'], $data['password'], false);

        if ($user) {
            // Remove sensitive data
            unset($user->password);
            unset($user->remember_token);

            // Return user data
            echo json_encode(['user' => $user]);
            exit;
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid email or password']);
            exit;
        }
    }

    /**
     * API: Handle user registration
     */
    public function apiRegister() {
        // Get JSON data
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Validate input
        $errors = validateRegistrationInput(
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['confirm_password'] ?? '',
            $data['role'] ?? 'fresher'
        );

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            exit;
        }

        // Check if email already exists
        if ($this->userModel->emailExists($data['email'])) {
            http_response_code(422);
            echo json_encode(['errors' => ['email' => 'This email is already registered']]);
            exit;
        }

        // Create user
        $user = $this->userModel->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'status' => 'pending',
            'email_verified' => false
        ]);

        if ($user) {
            // Remove sensitive data
            unset($user->password);
            unset($user->remember_token);

            // Return user data
            echo json_encode(['user' => $user]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Registration failed']);
            exit;
        }
    }
}
