<?php
// Settings alias - redirects to the correct setting.php file
// This file exists to satisfy the sidebar link to account/settings.php
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

// Forward all POST data and GET params to setting.php
// Since we can't do a transparent forward, we just include the actual file
$_SERVER['PHP_SELF'] = str_replace('settings.php', 'setting.php', $_SERVER['PHP_SELF']);
require __DIR__ . '/setting.php';
