<?php
if (session_status() === PHP_SESSION_NONE) {
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