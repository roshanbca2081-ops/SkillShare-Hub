
<?php
require_once __DIR__ . '/config.php';

define('FACEBOOK_APP_ID', '123456789012345');
define('FACEBOOK_REDIRECT_URI', SITE_URL . '/api/facebook-callback.php');

$authUrl = 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query([
    'client_id' => FACEBOOK_APP_ID,
    'redirect_uri' => FACEBOOK_REDIRECT_URI,
    'scope' => 'email,public_profile',
    'response_type' => 'code'
]);

if (isset($_GET['json'])) {
    jsonResponse(['success' => true, 'auth_url' => $authUrl]);
} else {
    header('Location: ' . $authUrl);
    exit();
}
