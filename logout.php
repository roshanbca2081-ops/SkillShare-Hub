<?php
require_once __DIR__ . '/config.php';

$_SESSION = [];
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}
session_destroy();

header('Location: ' . BASE_URL . 'index.php');
exit();
