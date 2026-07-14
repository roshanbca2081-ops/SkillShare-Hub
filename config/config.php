<?php
if (session_status() === PHP_SESSION_NONE) {
    $sessionPath = __DIR__ . '/../storage/sessions';
    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0775, true);
    }
    session_save_path($sessionPath);
    session_start();
}

define('SITE_NAME', 'ShareSkill Hub');
define('SITE_URL', 'http://localhost/SkillShare%20Hub');
define('SITE_EMAIL', 'hello@shareskillhub.dev');
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('SESSION_TIMEOUT', 1800);
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'shareskillhub');
?>
