<?php
// SkillShare Hub - Login API Endpoint
require_once __DIR__ . '/config.php';

// Start session for authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request method.', null, 405);
}

$data = getRequestData();

$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$remember = !empty($data['remember']);

// Validate required fields
if ($email === '' || $password === '') {
    respond(false, 'Email and password are required.', null, 422);
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Please enter a valid email address.', null, 422);
}

try {
    $pdo = getDB();

    // Query user by email
    $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role, status FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        respond(false, 'Invalid email or password.', null, 401);
    }

    // Check account status
    if ($user['status'] !== 'active') {
        respond(false, 'Your account is not active. Please contact support.', null, 403);
    }

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        respond(false, 'Invalid email or password.', null, 401);
    }

    // Set session variables
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    // Remember me - set a cookie for 30 days
    if ($remember) {
        setcookie('remember_user', base64_encode($user['email']), time() + (86400 * 30), '/');
    } else {
        setcookie('remember_user', '', time() - 3600, '/');
    }

    // Redirect based on role
    $dashboard = '';
    switch ($user['role']) {
        case 'admin':
            $dashboard = 'dashboard/admin/index.php';
            break;
        case 'mentor':
            $dashboard = 'dashboard/mentor/index.php';
            break;
        case 'fresher':
        default:
            $dashboard = 'dashboard/fresher/index.php';
            break;
    }

    respond(true, 'Login successful! Welcome back.', [
        'user_id'   => $user['id'],
        'name'      => trim($user['firstname'] . ' ' . $user['lastname']),
        'email'     => $user['email'],
        'role'      => $user['role'],
        'redirect'  => $dashboard,
    ]);
} catch (Exception $e) {
    respond(false, 'An unexpected error occurred. Please try again.', null, 500);
}
