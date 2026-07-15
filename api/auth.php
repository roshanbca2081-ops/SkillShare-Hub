<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// JSON API auth router (Phase 1)
// Supported actions via query param: ?action=register|login|logout|forgot|reset|verify-email|refresh-token
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

if (!in_array($action, ['register','login','logout','forgot','reset','verify-email','refresh-token'], true)) {
    json_response('error', 'Invalid or missing action. Use ?action=register|login|logout|forgot|reset|verify-email|refresh-token', [], 400);
}

$input = get_json_input();

function require_field($arr, $key, $message) {
    if (!isset($arr[$key]) || trim((string)$arr[$key]) === '') {
        json_response('error', $message, [], 422);
    }
}

function normalize_email($email) {
    return mb_strtolower(trim((string)$email));
}

// Use global $conn from includes/functions.php
global $conn;
if (!$conn) {
    json_response('error', 'Database not connected', [], 500);
}

// Common session helper
function set_login_session($user) {
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $user['email'];
}

function log_login_event($userId, $ok, $reason = null) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO login_history (user_id, login_ip, user_agent, is_success, reason) VALUES (?, ?, ?, ?, ?)");
    $ip = get_request_ip();
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $isSuccess = $ok ? 1 : 0;
    $stmt->bind_param('issis', $userId, $ip, $ua, $isSuccess, $reason);
    $stmt->execute();
    $stmt->close();
}

// REGISTER
if ($action === 'register') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response('error', 'Method not allowed', [], 405);
    }

    require_field($input, 'full_name', 'full_name is required');
    require_field($input, 'email', 'email is required');
    require_field($input, 'password', 'password is required');
    require_field($input, 'confirm_password', 'confirm_password is required');
    // role optional
    $role = isset($input['role']) ? (string)$input['role'] : 'fresher';
    if (!in_array($role, ['admin','graduate','fresher'], true)) {
        json_response('error', 'Invalid role', [], 422);
    }

    $fullName = trim((string)$input['full_name']);
    $email = normalize_email($input['email']);
    $password = (string)$input['password'];
    $confirm = (string)$input['confirm_password'];

    if ($password !== $confirm) {
        json_response('error', 'Password and confirm_password do not match', [], 422);
    }
    if (strlen($password) < 8) {
        json_response('error', 'Password must be at least 8 characters', [], 422);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response('error', 'Invalid email', [], 422);
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $stmt->close();
        json_response('error', 'Email already exists', [], 409);
    }
    $stmt->close();

    $hash = password_hash($password, PASSWORD_DEFAULT);

    // By spec: send email verification link (optional). For now: mark verified=0.
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, role, status, is_verified) VALUES (?, ?, ?, ?, 'active', 0)");
    $stmt->bind_param('ssss', $fullName, $email, $hash, $role);
    $ok = $stmt->execute();
    $userId = $stmt->insert_id;
    $stmt->close();

    // backfill user_roles
    $stmt = $conn->prepare("INSERT IGNORE INTO user_roles (user_id, role) VALUES (?, ?)");
    $stmt->bind_param('is', $userId, $role);
    $stmt->execute();
    $stmt->close();

    // (Email verification optional) - create a response indicating verification required
    json_response('success', 'User registered successfully. Please verify your email to login.' , [
        'user_id' => (int)$userId,
        'email' => $email,
        'role' => $role
    ], 201);
}

// LOGIN
if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response('error', 'Method not allowed', [], 405);
    }

    require_field($input, 'email', 'email is required');
    require_field($input, 'password', 'password is required');

    $email = normalize_email($input['email']);
    $password = (string)$input['password'];
    $remember = !empty($input['remember_me']);

    $stmt = $conn->prepare("SELECT id, full_name, email, password_hash, role, status, is_verified FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    if (!$user || $user['status'] !== 'active' || !password_verify($password, $user['password_hash'])) {
        $uid = $user ? (int)$user['id'] : 0;
        if ($uid) log_login_event($uid, false, 'invalid_credentials');
        json_response('error', 'Invalid login credentials', [], 401);
    }

    if ((int)$user['is_verified'] !== 1) {
        log_login_event((int)$user['id'], false, 'email_not_verified');
        json_response('error', 'Email not verified', [], 403);
    }

    set_login_session($user);

    // Update last login
    $stmt = $conn->prepare("UPDATE users SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?");
    $ip = get_request_ip();
    $uid = (int)$user['id'];
    $stmt->bind_param('si', $ip, $uid);
    $stmt->execute();
    $stmt->close();

    log_login_event($uid, true, null);

    $token = null;
    if ($remember) {
        $rememberToken = generate_token(32);
        $expires = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 30)); // 30 days
        $stmt = $conn->prepare("INSERT INTO user_sessions (user_id, remember_token, expires_at, ip_address, user_agent, last_seen_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $stmt->bind_param('issss', $uid, $rememberToken, $expires, $ip, $ua);
        $stmt->execute();
        $stmt->close();

        // set cookie (httpOnly)
        setcookie('remember_token', $rememberToken, time() + (60 * 60 * 24 * 30), '/', '', false, true);
        $token = $rememberToken;
    }

    json_response('success', 'Login successful', [
        'user' => [
            'id' => $uid,
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role']
        ],
        'remember_token' => $token
    ]);
}

// LOGOUT
if ($action === 'logout') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response('error', 'Method not allowed', [], 405);
    }

    $uid = $_SESSION['user_id'] ?? null;

    if (!empty($_COOKIE['remember_token']) && $uid) {
        $stmt = $conn->prepare("DELETE FROM user_sessions WHERE user_id = ? AND remember_token = ?");
        $token = (string)$_COOKIE['remember_token'];
        $stmt->bind_param('is', $uid, $token);
        $stmt->execute();
        $stmt->close();
    }

    if ($uid) {
        log_login_event((int)$uid, true, 'logout');
    }

    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    if (!empty($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }

    json_response('success', 'Logout successful');
}

// FORGOT PASSWORD (token generation)
if ($action === 'forgot') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response('error', 'Method not allowed', [], 405);
    }

    require_field($input, 'email', 'email is required');
    $email = normalize_email($input['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response('error', 'Invalid email', [], 422);
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $exists = $res && $res->num_rows === 1;
    $stmt->close();

    // Do not reveal whether email exists
    $token = generate_token(16);
    $expires = date('Y-m-d H:i:s', time() + 900);

    if ($exists) {
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $email, $token, $expires);
        $stmt->execute();
        $stmt->close();

        // Email sending is not implemented here; return token for now.
        // In production, send email with link containing token.
        json_response('success', 'If the email exists, you will receive password reset instructions.', [
            'reset_token' => $token
        ]);
    }

    json_response('success', 'If the email exists, you will receive password reset instructions.', []);
}

// RESET PASSWORD
if ($action === 'reset') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response('error', 'Method not allowed', [], 405);
    }

    require_field($input, 'token', 'token is required');
    require_field($input, 'password', 'password is required');
    require_field($input, 'confirm_password', 'confirm_password is required');

    $token = (string)$input['token'];
    $password = (string)$input['password'];
    $confirm = (string)$input['confirm_password'];

    if ($password !== $confirm) {
        json_response('error', 'Password and confirm_password do not match', [], 422);
    }
    if (strlen($password) < 8) {
        json_response('error', 'Password must be at least 8 characters', [], 422);
    }

    $stmt = $conn->prepare("SELECT email, token, expires_at FROM password_resets WHERE token = ? LIMIT 1");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    if (!$row) {
        json_response('error', 'Invalid reset token', [], 400);
    }

    if (strtotime($row['expires_at']) < time()) {
        json_response('error', 'Reset token expired', [], 400);
    }

    $email = $row['email'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ? LIMIT 1");
    $stmt->bind_param('ss', $hash, $email);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->close();

    json_response('success', 'Password has been reset successfully. Please login.');
}

// VERIFY EMAIL (optional endpoint)
if ($action === 'verify-email') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }

    $token = $_GET['token'] ?? '';
    if (trim($token) === '') {
        json_response('error', 'token is required', [], 422);
    }

    // This project doesn’t currently store email verification tokens.
    // So we provide a placeholder response.
    json_response('error', 'Email verification tokens not implemented in DB yet.', [], 501);
}

// REFRESH TOKEN (placeholder)
if ($action === 'refresh-token') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response('error', 'Method not allowed', [], 405);
    }

    require_field($input, 'remember_token', 'remember_token is required');
    json_response('error', 'refresh-token not implemented yet.', [], 501);
}

json_response('error', 'Unhandled action', [], 400);

