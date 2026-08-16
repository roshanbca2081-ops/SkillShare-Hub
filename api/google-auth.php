<?php
require_once __DIR__ . '/config.php';

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', '1084293847291-example.apps.googleusercontent.com');
define('GOOGLE_REDIRECT_URI', SITE_URL . '/api/google-callback.php');

$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'access_type' => 'online',
    'prompt' => 'select_account'
]);

if (isset($_GET['json'])) {
    jsonResponse(['success' => true, 'auth_url' => $authUrl]);
} else {
    header('Location: ' . $authUrl);
    exit();
}
