<?php

/**
 * Admin Middleware
 */

require_once __DIR__ . '/AuthMiddleware.php';

class AdminMiddleware
{
    public static function check()
    {
        AuthMiddleware::handle();
        $user = AuthMiddleware::user();
        if (!$user || ($user->role ?? '') !== 'admin') {
            header('Location: /404.php');
            exit();
        }
        return true;
    }

    public static function handle()
    {
        AuthMiddleware::handle();
        if (!isAdmin()) {
            setFlash('error', 'Access denied. Admin only.');
            redirect(APP_URL . 'dashboard');
            exit;
        }
        return true;
    }
}
