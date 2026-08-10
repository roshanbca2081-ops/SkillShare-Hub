<?php

/**
 * Auth Middleware
 */

class AuthMiddleware
{
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) || isset($_SESSION['user_data']);
    }

    public static function user()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_data'])) {
            return (object)$_SESSION['user_data'];
        }
        return null;
    }

    public static function handle()
    {
        if (!self::check()) {
            header('Location: /login');
            exit();
        }
    }
}
