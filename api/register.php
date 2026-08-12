<?php
// SkillShare Hub - Register API Endpoint
require_once __DIR__ . '/config.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request method.', null, 405);
}

$data = getRequestData();

$firstname = trim($data['firstname'] ?? '');
$lastname  = trim($data['lastname'] ?? '');
$fullName  = trim($data['full_name'] ?? trim($firstname . ' ' . $lastname));
$email     = trim($data['email'] ?? '');
$role      = trim($data['role'] ?? 'fresher');
$password  = $data['password'] ?? '';
$confirm   = $data['confirm_password'] ?? '';
$phone     = trim($data['phone'] ?? '');
$address   = trim($data['address'] ?? '');

// Validate required fields
if ($fullName === '' || $email === '' || $password === '') {
    respond(false, 'Full name, email and password are required.', null, 422);
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Please enter a valid email address.', null, 422);
}

// Validate password length
if (strlen($password) < 6) {
    respond(false, 'Password must be at least 6 characters.', null, 422);
}

// Validate password confirmation
if ($password !== $confirm) {
    respond(false, 'Passwords do not match.', null, 422);
}

// Validate role
$validRoles = ['fresher', 'mentor', 'admin'];
if (!in_array($role, $validRoles)) {
    respond(false, 'Invalid role selected.', null, 422);
}

try {
    $pdo = getDB();

    // Check if email already exists
    $check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        respond(false, 'An account with this email already exists.', null, 409);
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $sql = 'INSERT INTO users (full_name, email, password_hash, role, status, phone, address, created_at)
            VALUES (:full_name, :email, :password, :role, :status, :phone, :address, NOW())';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':full_name' => $fullName,
        ':email'     => $email,
        ':password'  => $hashedPassword,
        ':role'      => $role,
        ':status'    => 'active',
        ':phone'     => $phone,
        ':address'   => $address,
    ]);

    $userId = (int) $pdo->lastInsertId();

    // Auto-login the new user
    $_SESSION['user_id']    = $userId;
    $_SESSION['user_name']  = trim($firstname . ' ' . $lastname);
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role']  = $role;

    // Redirect based on role
    $dashboard = '';
    switch ($role) {
        case 'mentor':
            $dashboard = 'dashboard/mentor/index.php';
            break;
        case 'admin':
            $dashboard = 'dashboard/admin/index.php';
            break;
        default:
            $dashboard = 'dashboard/fresher/index.php';
            break;
    }

    respond(true, 'Account created successfully! Welcome aboard.', [
        'user_id'  => $userId,
        'name'     => trim($firstname . ' ' . $lastname),
        'email'    => $email,
        'role'     => $role,
        'redirect' => $dashboard,
    ], 201);
} catch (Exception $e) {
    // Check if it's a duplicate entry error
    if ($e instanceof PDOException && $e->getCode() == 23000) {
        respond(false, 'An account with this email already exists.', null, 409);
    }
    respond(false, 'An unexpected error occurred. Please try again.', null, 500);
}
