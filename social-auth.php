<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$oauthConfig = require __DIR__ . '/config/social-auth.php';
$google = $oauthConfig['google'] ?? [];

function googleAuthFail($message)
{
    unset($_SESSION['google_oauth']);
    $_SESSION['alert'] = [
        'type' => 'danger',
        'icon' => 'exclamation-circle',
        'message' => $message,
    ];
    redirect('login.php');
}

function googleAuthCallbackUri()
{
    $configured = getenv('SKILLSHARE_OAUTH_REDIRECT_URI');
    if ($configured === false || trim((string)$configured) === '') {
        $configured = $_SERVER['SKILLSHARE_OAUTH_REDIRECT_URI'] ?? $_ENV['SKILLSHARE_OAUTH_REDIRECT_URI'] ?? '';
    }

    if (is_string($configured) && trim($configured) !== '') {
        $uri = trim($configured);
    } else {
        $host = (string)($_SERVER['HTTP_HOST'] ?? '');
        if (!preg_match('/^(localhost|127\.0\.0\.1)(?::[0-9]{1,5})?$/i', $host)) {
            $https = !empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off';
            if (!$https || !preg_match('/^[a-z0-9.-]+(?::[0-9]{1,5})?$/i', $host)) {
                throw new RuntimeException('OAuth callback must use HTTPS outside localhost.');
            }
        }
        $scheme = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') ? 'https' : 'http';
        $path = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/social-auth.php'));
        $uri = $scheme . '://' . $host . $path;
    }

    $parts = parse_url($uri);
    if (!$parts || empty($parts['scheme']) || empty($parts['host']) || !empty($parts['query']) || !empty($parts['fragment'])) {
        throw new RuntimeException('OAuth callback URI must be an absolute URL without query or fragment.');
    }
    if (strtolower($parts['scheme']) !== 'https'
        && !in_array(strtolower($parts['host']), ['localhost', '127.0.0.1'], true)) {
        throw new RuntimeException('OAuth callback must use HTTPS outside localhost.');
    }

    return $uri;
}

function googleAuthHttpJson($url, $method = 'GET', array $data = [], array $headers = [])
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('PHP cURL is not enabled. Enable it in XAMPP php.ini and restart Apache.');
    }

    $curl = curl_init($url);
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => $headers,
    ];
    if ($method === 'POST') {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
    }
    curl_setopt_array($curl, $options);
    $body = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);

    if ($body === false || $status < 200 || $status >= 300) {
        error_log('Google OAuth HTTP request failed; status=' . $status . '; curl=' . $curlError);
        throw new RuntimeException('Google could not complete sign-in. Please try again.');
    }
    $decoded = json_decode($body, true);
    if (!is_array($decoded) || !empty($decoded['error'])) {
        throw new RuntimeException('Google returned an invalid sign-in response.');
    }
    return $decoded;
}

function googleAuthFinish(PDO $pdo, array $profile)
{
    $subject = trim((string)($profile['sub'] ?? ''));
    $email = strtolower(trim((string)($profile['email'] ?? '')));
    $emailVerified = ($profile['email_verified'] ?? false) === true || ($profile['email_verified'] ?? '') === 'true';
    $name = trim((string)($profile['name'] ?? ''));

    if ($subject === '' || !$emailVerified || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Google did not return a verified account email.');
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.role, u.is_active
                               FROM oauth_accounts oa JOIN users u ON u.id = oa.user_id
                               WHERE oa.provider = 'google' AND oa.provider_user_id = ? LIMIT 1");
        $stmt->execute([$subject]);
        $user = $stmt->fetch();

        if (!$user) {
            $stmt = $pdo->prepare('SELECT id, full_name, email, role, is_active FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && (int)$user['is_active'] !== 1) {
                throw new RuntimeException('This SkillShare Hub account is disabled. Contact support.');
            }

            if (!$user) {
                $fullName = $name !== '' ? $name : strstr($email, '@', true);
                $randomPassword = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, is_active) VALUES (?, ?, ?, 'fresher', 1)");
                $stmt->execute([$fullName, $email, $randomPassword]);
                $user = [
                    'id' => $pdo->lastInsertId(),
                    'full_name' => $fullName,
                    'email' => $email,
                    'role' => 'fresher',
                    'is_active' => 1,
                ];
            }

            // Google verified the email. Unique provider-subject constraints prevent duplicate links.
            $stmt = $pdo->prepare("INSERT INTO oauth_accounts (user_id, provider, provider_user_id, email) VALUES (?, 'google', ?, ?)");
            $stmt->execute([$user['id'], $subject, $email]);
        }

        if (!$user || (int)$user['is_active'] !== 1) {
            throw new RuntimeException('This SkillShare Hub account is disabled or unavailable.');
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Welcome back, ' . $user['full_name'] . '!'];

    switch ($user['role']) {
        case 'admin': redirect('admin/dashboard.php');
        case 'mentor': redirect('mentor/dashboard.php');
        default: redirect('fresher/dashboard.php');
    }
}

if (empty($google['client_id']) || empty($google['client_secret'])) {
    error_log('Google OAuth is not configured; client_id=' . (empty($google['client_id']) ? 'missing' : 'present') . ', client_secret=' . (empty($google['client_secret']) ? 'missing' : 'present'));
    googleAuthFail('Google sign-in is not configured. Check the private server configuration.');
}

$isCallback = isset($_GET['code']) || isset($_GET['error']) || isset($_GET['state']);
if (!$isCallback) {
    if (($_GET['provider'] ?? '') !== 'google') {
        googleAuthFail('Unsupported sign-in provider.');
    }
    try {
        $callbackUri = googleAuthCallbackUri();
    } catch (Throwable $e) {
        error_log('Google OAuth callback configuration error: ' . $e->getMessage());
        googleAuthFail('Google sign-in callback is not configured correctly.');
    }

    $state = bin2hex(random_bytes(32));
    $_SESSION['google_oauth'] = ['state' => $state, 'created_at' => time()];
    $authorizationUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
        'client_id' => $google['client_id'],
        'redirect_uri' => $callbackUri,
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'state' => $state,
        'prompt' => 'select_account',
    ], '', '&', PHP_QUERY_RFC3986);
    header('Location: ' . $authorizationUrl, true, 302);
    exit;
}

$savedOAuth = $_SESSION['google_oauth'] ?? null;
unset($_SESSION['google_oauth']);
if (!is_array($savedOAuth)
    || empty($_GET['state'])
    || !hash_equals((string)($savedOAuth['state'] ?? ''), (string)$_GET['state'])
    || time() - (int)($savedOAuth['created_at'] ?? 0) > 600) {
    googleAuthFail('The Google sign-in request expired or could not be verified. Please try again.');
}
if (isset($_GET['error'])) {
    googleAuthFail('Google sign-in was cancelled or declined.');
}
if (empty($_GET['code'])) {
    googleAuthFail('Google did not return an authorization code. Please try again.');
}

try {
    $callbackUri = googleAuthCallbackUri();
    $token = googleAuthHttpJson('https://oauth2.googleapis.com/token', 'POST', [
        'code' => (string)$_GET['code'],
        'client_id' => $google['client_id'],
        'client_secret' => $google['client_secret'],
        'redirect_uri' => $callbackUri,
        'grant_type' => 'authorization_code',
    ], ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded']);
    if (empty($token['access_token'])) {
        throw new RuntimeException('Google did not return an access token.');
    }
    $profile = googleAuthHttpJson('https://openidconnect.googleapis.com/v1/userinfo', 'GET', [], [
        'Authorization: Bearer ' . $token['access_token'],
        'Accept: application/json',
    ]);
    googleAuthFinish($pdo, $profile);
} catch (Throwable $e) {
    error_log('Google sign-in failed: ' . $e->getMessage());
    $safeMessage = $e instanceof RuntimeException ? $e->getMessage() : 'Google sign-in could not be completed. Please try again.';
    googleAuthFail($safeMessage);
}
