<?php
require_once __DIR__ . '/session.php';

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . appUrl('login.php'));
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if (getUserRole() !== $role) {
        header('Location: ' . appUrl('404.php'));
        exit();
    }
}

function requireAdmin() {
    requireRole('admin');
}

function requireMentor() {
    requireRole('mentor');
}

function requireFresher() {
    requireRole('fresher');
}