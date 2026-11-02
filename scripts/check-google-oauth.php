<?php
// Safe CLI-only diagnostic. It never outputs credential values and makes no database changes.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

try {
    $settings = require dirname(__DIR__) . '/config/social-auth.php';
} catch (Throwable $e) {
    fwrite(STDERR, 'Config load failed: ' . get_class($e) . PHP_EOL);
    exit(1);
}

$google = $settings['google'] ?? [];
$localFiles = [
    dirname(__DIR__) . '/config/social-auth.local.php',
    dirname(__DIR__) . '/config/social-auth.local.php.php',
];
$localValues = [];
foreach ($localFiles as $path) {
    if (!is_file($path)) {
        continue;
    }
    try {
        $config = require $path;
        if (is_array($config) && isset($config['google']) && is_array($config['google'])) {
            $localValues[basename($path)] = $config['google'];
        }
    } catch (Throwable $e) {
        fwrite(STDOUT, basename($path) . ': present but failed to load' . PHP_EOL);
    }
}

$allConfigured = true;
foreach ([
    'client_id' => 'SKILLSHARE_GOOGLE_CLIENT_ID',
    'client_secret' => 'SKILLSHARE_GOOGLE_CLIENT_SECRET',
] as $key => $environmentKey) {
    $value = trim((string)($google[$key] ?? ''));
    $configured = $value !== '' && !preg_match('/^(YOUR_|CHANGE_ME|dummy-|replace[_ -])/i', $value);
    $allConfigured = $allConfigured && $configured;

    $source = 'none';
    $envValue = getenv($environmentKey);
    if (($envValue !== false && trim((string)$envValue) !== '')
        || !empty($_SERVER[$environmentKey]) || !empty($_ENV[$environmentKey])) {
        $source = 'environment';
    } else {
        foreach ($localValues as $fileName => $localGoogle) {
            $localValue = trim((string)($localGoogle[$key] ?? ''));
            if ($localValue !== '' && !preg_match('/^(YOUR_|CHANGE_ME|dummy-|replace[_ -])/i', $localValue)) {
                $source = $fileName;
            }
        }
    }
    fwrite(STDOUT, $key . ': ' . ($configured ? 'configured' : 'MISSING') . ' (source: ' . $source . ')' . PHP_EOL);
}

$projectFolder = basename(dirname(__DIR__));
fwrite(STDOUT, 'Expected localhost callback: http://localhost/' . $projectFolder . '/social-auth.php' . PHP_EOL);
fwrite(STDOUT, 'Credentials: ' . ($allConfigured ? 'loaded (values hidden)' : 'not configured') . PHP_EOL);
exit($allConfigured ? 0 : 2);
