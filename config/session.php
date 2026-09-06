<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function appUrl($path = '') {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $segments = explode('/', trim($scriptName, '/'));
    $basePath = !empty($segments[0]) ? '/' . $segments[0] . '/' : '/';

    return $basePath . ltrim($path, '/');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserName() {
    return $_SESSION['full_name'] ?? 'User';
}

function isAdmin() {
    return getUserRole() === 'admin';
}

function isMentor() {
    return getUserRole() === 'mentor';
}

function isFresher() {
    return getUserRole() === 'fresher';
}