<?php
/**
 * Loads OAuth credentials only on the server. Keep local credentials out of Git.
 * Windows may append .php when file extensions are hidden, so support both local names.
 */
$google = [
    'client_id' => '',
    'client_secret' => '',
];

$localFiles = [
    __DIR__ . '/social-auth.local.php.php',
    __DIR__ . '/social-auth.local.php',
];

foreach ($localFiles as $localFile) {
    if (!is_file($localFile)) {
        continue;
    }

    $localConfig = require $localFile;
    $localGoogle = is_array($localConfig) ? ($localConfig['google'] ?? []) : [];
    if (!is_array($localGoogle)) {
        continue;
    }

    foreach (['client_id', 'client_secret'] as $key) {
        $value = trim((string)($localGoogle[$key] ?? ''));
        if ($value !== '') {
            $google[$key] = $value;
        }
    }
}

// Environment settings override local file values (e.g. Apache/XAMPP configuration).
foreach (['client_id' => 'SKILLSHARE_GOOGLE_CLIENT_ID', 'client_secret' => 'SKILLSHARE_GOOGLE_CLIENT_SECRET'] as $key => $environmentName) {
    $value = getenv($environmentName);
    if ($value === false || trim((string)$value) === '') {
        $value = $_SERVER[$environmentName] ?? $_ENV[$environmentName] ?? '';
    }
    if (is_string($value) && trim($value) !== '') {
        $google[$key] = trim($value);
    }
}

return ['google' => $google];
