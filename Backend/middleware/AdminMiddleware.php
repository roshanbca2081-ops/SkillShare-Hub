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
}
