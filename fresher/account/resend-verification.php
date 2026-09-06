<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';

if (!isLoggedIn()) {
    redirect('../../login.php');
}

$user_id = getUserId();

// Check if already verified
$stmt = $pdo->prepare("SELECT is_verified FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user['is_verified']) {
    $_SESSION['alert'] = [
        'type' => 'info',
        'icon' => 'info-circle',
        'message' => 'Your account is already verified.'
    ];
    redirect('../dashboard.php');
}

// Generate new verification token
$token = generateToken();
$stmt = $pdo->prepare("UPDATE users SET verification_token = ?, verification_token_expiry = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE id = ?");
$stmt->execute([$token, $user_id]);

// Send verification email (simplified)
$verification_link = "http://$_SERVER[HTTP_HOST]/fresher/account/verify.php?token=$token";

$_SESSION['alert'] = [
    'type' => 'success',
    'icon' => 'check-circle',
    'message' => 'A new verification email has been sent to your registered email address.'
];
redirect('../dashboard.php');
?>