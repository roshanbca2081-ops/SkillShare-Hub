<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$code = $_GET['code'] ?? '';
$error = $_GET['error'] ?? '';

if ($error || empty($code)) {
    header('Location: ' . BASE_URL . 'login.php?error=' . urlencode('Google authentication was cancelled or failed.'));
    exit();
}

// In production, exchange $code with Google token API and get user profile.
// Here we handle the callback gracefully or redirect to login with user token response.
header('Location: ' . BASE_URL . 'login.php?google_auth=success');
exit();
