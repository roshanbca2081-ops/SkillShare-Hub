<?php
$page_title = 'Verify Account';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';

if (isLoggedIn()) {
    redirect('../dashboard.php');
}

$token = isset($_GET['token']) ? sanitize($_GET['token']) : '';

if (empty($token)) {
    redirect('../../login.php');
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token = ? AND verification_token_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if ($user) {
    $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL, verification_token_expiry = NULL WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Your email has been verified! You can now login.'
    ];
    redirect('../../login.php');
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'icon' => 'exclamation-circle',
        'message' => 'Invalid or expired verification token.'
    ];
    redirect('../../login.php');
}
?>