<?php
/**
 * SkillShare Hub - Root Configuration
 * Central config for all frontend pages
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('UTC');

// Application constants
define('SITE_URL', 'http://localhost/SkillShare-Hub');
define('BASE_URL', SITE_URL . '/');
define('BASE_PATH', dirname(__DIR__));
define('DB_HOST', 'localhost');
define('DB_NAME', 'skillshare_hub');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection (PDO)
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

// Auth helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, full_name, email, profile_picture, role, bio, academic_field_id, course_id, hourly_rate, is_verified, status, created_at, last_login FROM users WHERE id = ?");
    $stmt->execute([getUserId()]);
    return $stmt->fetch();
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function sanitize($data) {
    if (is_array($data)) return array_map('sanitize', $data);
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit();
}

function getSidebarItems($role) {
    $baseUrl = BASE_URL . 'dashboard/' . $role . '/';
    $items = [];

    if ($role === 'fresher') {
        $items = [
            'Main' => [
                ['label' => 'Dashboard', 'icon' => 'tachometer-alt', 'link' => $baseUrl . 'index.php'],
                ['label' => 'Academic Fields', 'icon' => 'book', 'link' => $baseUrl . 'academic-fields.php'],
                ['label' => 'Courses', 'icon' => 'graduation-cap', 'link' => $baseUrl . 'courses.php'],
                ['label' => 'Mentors', 'icon' => 'chalkboard-user', 'link' => $baseUrl . 'mentors.php'],
            ],
            'Sessions' => [
                ['label' => 'Bookings', 'icon' => 'calendar-check', 'link' => $baseUrl . 'bookings.php'],
                ['label' => 'My Sessions', 'icon' => 'clock', 'link' => $baseUrl . 'sessions.php'],
                ['label' => 'Assignments', 'icon' => 'file-pen', 'link' => $baseUrl . 'assignments.php'],
            ],
            'Learning' => [
                ['label' => 'Resources', 'icon' => 'book-open', 'link' => $baseUrl . 'resources.php'],
                ['label' => 'Research', 'icon' => 'microscope', 'link' => $baseUrl . 'research.php'],
                ['label' => 'Interview', 'icon' => 'comments', 'link' => $baseUrl . 'interview.php'],
            ],
            'Account' => [
                ['label' => 'Certificates', 'icon' => 'certificate', 'link' => $baseUrl . 'certificates.php'],
                ['label' => 'Payments', 'icon' => 'credit-card', 'link' => $baseUrl . 'payments.php'],
                ['label' => 'Messages', 'icon' => 'message', 'link' => $baseUrl . 'messages.php'],
                ['label' => 'Notifications', 'icon' => 'bell', 'link' => $baseUrl . 'notifications.php'],
                ['label' => 'Profile', 'icon' => 'id-card', 'link' => $baseUrl . 'profile.php'],
                ['label' => 'Settings', 'icon' => 'gear', 'link' => $baseUrl . 'settings.php'],
            ],
        ];
    } elseif ($role === 'mentor') {
        $items = [
            'Main' => [
                ['label' => 'Dashboard', 'icon' => 'tachometer-alt', 'link' => $baseUrl . 'index.php'],
                ['label' => 'My Courses', 'icon' => 'graduation-cap', 'link' => $baseUrl . 'my-courses.php'],
                ['label' => 'Students', 'icon' => 'users', 'link' => $baseUrl . 'students.php'],
            ],
            'Sessions' => [
                ['label' => 'Bookings', 'icon' => 'calendar-check', 'link' => $baseUrl . 'bookings.php'],
                ['label' => 'My Sessions', 'icon' => 'clock', 'link' => $baseUrl . 'sessions.php'],
                ['label' => 'Assignments', 'icon' => 'file-pen', 'link' => $baseUrl . 'assignments.php'],
            ],
            'Learning' => [
                ['label' => 'Resources', 'icon' => 'book-open', 'link' => $baseUrl . 'resources.php'],
                ['label' => 'Research', 'icon' => 'microscope', 'link' => $baseUrl . 'research.php'],
                ['label' => 'Interview', 'icon' => 'comments', 'link' => $baseUrl . 'interview.php'],
            ],
            'Account' => [
                ['label' => 'Certificates', 'icon' => 'certificate', 'link' => $baseUrl . 'certificates.php'],
                ['label' => 'Messages', 'icon' => 'message', 'link' => $baseUrl . 'messages.php'],
                ['label' => 'Notifications', 'icon' => 'bell', 'link' => $baseUrl . 'notifications.php'],
                ['label' => 'Profile', 'icon' => 'id-card', 'link' => $baseUrl . 'profile.php'],
                ['label' => 'Settings', 'icon' => 'gear', 'link' => $baseUrl . 'settings.php'],
            ],
        ];
    } elseif ($role === 'admin') {
        $items = [
            'Management' => [
                ['label' => 'Users', 'icon' => 'users', 'link' => $baseUrl . 'users.php'],
                ['label' => 'Freshers', 'icon' => 'user-graduate', 'link' => $baseUrl . 'freshers.php'],
                ['label' => 'Mentors', 'icon' => 'chalkboard-user', 'link' => $baseUrl . 'mentors.php'],
                ['label' => 'Academic Fields', 'icon' => 'book', 'link' => $baseUrl . 'academic-fields.php'],
                ['label' => 'Courses', 'icon' => 'graduation-cap', 'link' => $baseUrl . 'courses.php'],
            ],
            'Operations' => [
                ['label' => 'Bookings', 'icon' => 'calendar-check', 'link' => $baseUrl . 'bookings.php'],
                ['label' => 'Sessions', 'icon' => 'clock', 'link' => $baseUrl . 'sessions.php'],
                ['label' => 'Assignments', 'icon' => 'file-pen', 'link' => $baseUrl . 'assignments.php'],
            ],
            'Learning' => [
                ['label' => 'Resources', 'icon' => 'book-open', 'link' => $baseUrl . 'resources.php'],
                ['label' => 'Research', 'icon' => 'microscope', 'link' => $baseUrl . 'research.php'],
                ['label' => 'Interview', 'icon' => 'comments', 'link' => $baseUrl . 'interview.php'],
            ],
            'Finance' => [
                ['label' => 'Payments', 'icon' => 'credit-card', 'link' => $baseUrl . 'payments.php'],
                ['label' => 'Certificates', 'icon' => 'certificate', 'link' => $baseUrl . 'certificates.php'],
            ],
            'Communication' => [
                ['label' => 'Messages', 'icon' => 'message', 'link' => $baseUrl . 'messages.php'],
                ['label' => 'Notifications', 'icon' => 'bell', 'link' => $baseUrl . 'notifications.php'],
            ],
            'Reports' => [
                ['label' => 'Reports', 'icon' => 'chart-line', 'link' => $baseUrl . 'reports.php'],
                ['label' => 'Activity Logs', 'icon' => 'history', 'link' => $baseUrl . 'activity-logs.php'],
            ],
            'Account' => [
                ['label' => 'Profile', 'icon' => 'id-card', 'link' => $baseUrl . 'profile.php'],
                ['label' => 'Settings', 'icon' => 'gear', 'link' => $baseUrl . 'settings.php'],
            ],
        ];
    }
    return $items;
}

if (file_exists(__DIR__ . '/dashboard/_layout.php')) {
    require_once __DIR__ . '/dashboard/_layout.php';
}
