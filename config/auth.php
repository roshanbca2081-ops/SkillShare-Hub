<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/database.php';

function requireLogin() {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    if (!isLoggedIn()) {
        header('Location: ' . appUrl('login.php'));
        exit();
    }

    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT id, full_name, email, role, is_active FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([getUserId()]);
        $user = $stmt->fetch();
    } catch (Throwable $e) {
        error_log('Account access check failed: ' . $e->getMessage());
        $user = false;
    }

    if (!$user || (int)$user['is_active'] !== 1) {
        unset($_SESSION['user_id'], $_SESSION['full_name'], $_SESSION['role'], $_SESSION['email']);
        session_regenerate_id(true);
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => $user === false
                ? 'We could not verify your account. Please sign in again.'
                : 'This account is disabled. Contact support for help.',
        ];
        header('Location: ' . appUrl('login.php'));
        exit();
    }

    // Keep authorization tied to the current database record, not stale session data.
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
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