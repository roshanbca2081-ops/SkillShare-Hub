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
$email     = trim($data['email'] ?? '');
$role      = trim($data['role'] ?? 'fresher');
$password  = $data['password'] ?? '';
$confirm   = $data['confirm_password'] ?? '';
$academicFieldId = isset($data['academic_field_id']) ? (int)$data['academic_field_id'] : 0;
$courseId    = isset($data['course_id']) ? (int)$data['course_id'] : 0;
$skillIds    = isset($data['skill_ids']) && is_array($data['skill_ids']) ? array_map('intval', $data['skill_ids']) : [];

// Validate required fields
if ($firstname === '' || $lastname === '' || $email === '' || $password === '') {
    respond(false, 'First name, last name, email and password are required.', null, 422);
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

// Validate academic field and course relationship
if ($academicFieldId && $courseId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE id = ? AND academic_field_id = ? AND status = 'active'");
    $stmt->execute([$courseId, $academicFieldId]);
    if (!$stmt->fetchColumn()) {
        respond(false, 'Selected course does not belong to the chosen academic field.', null, 422);
    }
}

// Validate skills belong to the selected course
if (!empty($skillIds) && $courseId) {
    $placeholders = implode(',', array_fill(0, count($skillIds), '?'));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM skills WHERE id IN ($placeholders) AND course_id = ? AND status = 'active'");
    $stmt->execute(array_merge($skillIds, [$courseId]));
    if ($stmt->fetchColumn() !== count($skillIds)) {
        respond(false, 'One or more selected skills do not belong to the chosen course.', null, 422);
    }
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
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $fullName  = trim($firstname . ' ' . $lastname);

    // Insert new user
    $sql = 'INSERT INTO users (full_name, firstname, lastname, email, password, password_hash, role, status, academic_field_id, course_id, created_at)
            VALUES (:full_name, :firstname, :lastname, :email, :password, :password_hash, :role, :status, :academic_field_id, :course_id, NOW())';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':full_name' => $fullName,
        ':firstname' => $firstname,
        ':lastname'  => $lastname,
        ':email'     => $email,
        ':password'  => $hashedPassword,
        ':password_hash' => $hashedPassword,
        ':role'      => $role,
        ':status'    => 'active',
        ':academic_field_id' => $academicFieldId ?: null,
        ':course_id' => $courseId ?: null,
    ]);

    $userId = (int) $pdo->lastInsertId();

    // Insert user skills
    if (!empty($skillIds)) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_skills (user_id, skill_id, created_at) VALUES (?, ?, NOW())");
        foreach ($skillIds as $skillId) {
            $stmt->execute([$userId, $skillId]);
        }
    }

    // Auto-login the new user
    session_regenerate_id(true);
    $_SESSION['user_id']    = $userId;
    $_SESSION['user_name']  = $firstname . ' ' . $lastname;
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
        'name'     => $firstname . ' ' . $lastname,
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
