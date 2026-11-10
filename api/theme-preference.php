<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/functions.php';
require_once '../config/auth.php';
require_once '../config/user-preferences.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Require authentication
if (!isLoggedIn()) {
    http_response_code(401);
    exit('Unauthorized');
}

$user_id = getUserId();
$theme = (string)($_POST['theme_preference'] ?? 'system');

// Validate theme value
if (!in_array($theme, ['light', 'dark', 'system'])) {
    $theme = 'system';
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'Invalid request token']);
    exit;
}

try {
    saveUserThemePreference($pdo, getUserId(), $theme);
    
    echo json_encode(['success' => true, 'theme' => $theme]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save theme preference']);
}