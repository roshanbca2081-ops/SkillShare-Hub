<?php
/**
 * Gmail SMTP configuration. Keep App Passwords outside tracked source files.
 * Environment variables override config/mail.local.php for deployment.
 */
$mailConfig = [
    'username' => '',
    'app_password' => '',
    'from_name' => 'SkillShare Hub',
    'base_url' => '',
];

$localFiles = [__DIR__ . '/mail.local.php'];
foreach ($localFiles as $localFile) {
    if (!is_file($localFile)) {
        continue;
    }
    $localConfig = require $localFile;
    if (is_array($localConfig)) {
        foreach (array_keys($mailConfig) as $key) {
            if (isset($localConfig[$key]) && is_string($localConfig[$key]) && trim($localConfig[$key]) !== '') {
                $mailConfig[$key] = trim($localConfig[$key]);
            }
        }
    }
}

$environmentNames = [
    'username' => 'SKILLSHARE_SMTP_USERNAME',
    'app_password' => 'SKILLSHARE_SMTP_APP_PASSWORD',
    'from_name' => 'SKILLSHARE_MAIL_FROM_NAME',
    'base_url' => 'SKILLSHARE_APP_URL',
];
foreach ($environmentNames as $key => $environmentName) {
    $value = getenv($environmentName);
    if ($value === false || trim((string)$value) === '') {
        $value = $_SERVER[$environmentName] ?? $_ENV[$environmentName] ?? '';
    }
    if (is_string($value) && trim($value) !== '') {
        $mailConfig[$key] = trim($value);
    }
}

$mailConfig['app_password'] = preg_replace('/\s+/', '', $mailConfig['app_password']);
$mailConfig['base_url'] = rtrim($mailConfig['base_url'], '/');

/** Send a password reset email using Gmail SMTP and PHPMailer. */
function sendPasswordResetEmail($recipientEmail, $recipientName, $resetUrl)
{
    global $mailConfig;

    $urlParts = parse_url($mailConfig['base_url']);
    $validBaseUrl = is_array($urlParts)
        && isset($urlParts['scheme'], $urlParts['host'])
        && in_array(strtolower($urlParts['scheme']), ['http', 'https'], true)
        && (strtolower($urlParts['scheme']) === 'https' || in_array(strtolower($urlParts['host']), ['localhost', '127.0.0.1'], true))
        && !isset($urlParts['user'])
        && !isset($urlParts['pass'])
        && !isset($urlParts['query'])
        && !isset($urlParts['fragment']);
    if (!$validBaseUrl
        || !filter_var($mailConfig['username'], FILTER_VALIDATE_EMAIL)
        || $mailConfig['app_password'] === ''
        || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)
        || !filter_var($resetUrl, FILTER_VALIDATE_URL)) {
        throw new RuntimeException('Password reset email is not configured correctly.');
    }

    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (!is_file($autoload)) {
        throw new RuntimeException('Composer dependencies are unavailable.');
    }
    require_once $autoload;

    $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host = 'smtp.gmail.com';
    $mailer->SMTPAuth = true;
    $mailer->Username = $mailConfig['username'];
    $mailer->Password = $mailConfig['app_password'];
    $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mailer->Port = 587;
    $mailer->Timeout = 15;
    $mailer->CharSet = 'UTF-8';
    $mailer->setFrom($mailConfig['username'], $mailConfig['from_name']);
    $mailer->addAddress($recipientEmail, $recipientName);
    $mailer->isHTML(true);
    $safeName = htmlspecialchars($recipientName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $mailer->Subject = 'Reset your SkillShare Hub password';
    $mailer->Body = '<!doctype html><html><body style="margin:0;padding:24px;background:#f3f5ff;font-family:Arial,sans-serif;color:#172033"><div style="max-width:560px;margin:auto;padding:32px;background:#fff;border-radius:16px"><h1 style="font-size:22px">Password reset</h1><p>Hello ' . $safeName . ',</p><p>We received a request to reset your SkillShare Hub password. This link expires in 15 minutes and can only be used once.</p><p style="margin:28px 0"><a href="' . $safeUrl . '" style="padding:13px 20px;color:#fff;background:#4f46e5;border-radius:9px;text-decoration:none;font-weight:bold">Choose a new password</a></p><p>If you did not request this, you can ignore this email.</p><p style="color:#667085;font-size:12px">If the button does not work, copy this link into your browser:<br><a href="' . $safeUrl . '">' . $safeUrl . '</a></p></div></body></html>';
    $mailer->AltBody = "Hello $recipientName,\n\nUse this link to reset your SkillShare Hub password. It expires in 15 minutes and can only be used once:\n$resetUrl\n\nIf you did not request this, ignore this email.";
    $mailer->send();
    return true;
}

return $mailConfig;