<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request method.', null, 405);
}

$data = getRequestData();

$provider  = trim($data['provider'] ?? '');
$email     = trim($data['email'] ?? '');
$name      = trim($data['name'] ?? '');
$avatar    = trim($data['avatar'] ?? '');
$socialId  = trim($data['social_id'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Valid email required for social login.', null, 422);
}

if (empty($name)) {
    $name = strstr($email, '@', true) ?: 'User';
}

try {
    $pdo = getDB();

    // Check if user already exists
    $stmt = $pdo->prepare('SELECT id, full_name, firstname, lastname, email, role, status FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['status'] !== 'active') {
            respond(false, 'Your account is inactive. Please contact support.', null, 403);
        }
        $userId = $user['id'];
        $role   = $user['role'];
        $userName = !empty($user['full_name']) ? $user['full_name'] : ($user['firstname'] . ' ' . $user['lastname']);
    } else {
        // Create new user automatically via social login
        $parts = explode(' ', $name, 2);
        $firstname = $parts[0] ?? $name;
        $lastname  = $parts[1] ?? '';
        $role = 'fresher';

        $insertSql = 'INSERT INTO users (full_name, firstname, lastname, email, role, status, is_verified, created_at)
                      VALUES (:full_name, :firstname, :lastname, :email, :role, :status, 1, NOW())';
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            ':full_name' => $name,
            ':firstname' => $firstname,
            ':lastname'  => $lastname,
            ':email'     => $email,
            ':role'      => $role,
            ':status'    => 'active'
        ]);

        $userId   = (int) $pdo->lastInsertId();
        $userName = $name;
    }

    // Set session
    session_regenerate_id(true);
    $_SESSION['user_id']    = $userId;
    $_SESSION['user_name']  = $userName;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role']  = $role;

    // Determine dashboard redirect
    $dashboard = 'dashboard/fresher/index.php';
    if ($role === 'mentor') {
        $dashboard = 'dashboard/mentor/index.php';
    } elseif ($role === 'admin') {
        $dashboard = 'dashboard/admin/index.php';
    }

    respond(true, ucfirst($provider) . ' login successful! Redirecting...', [
        'user_id'  => $userId,
        'name'     => $userName,
        'email'    => $email,
        'role'     => $role,
        'redirect' => $dashboard
    ]);
} catch (Exception $e) {
    respond(false, 'Social authentication failed: ' . $e->getMessage(), null, 500);
}
