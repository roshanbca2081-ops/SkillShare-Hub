<?php

/**
 * Guest Middleware
 */

require_once __DIR__ . '/AuthMiddleware.php';

class GuestMiddleware
{
    public static function check()
    {
        if (AuthMiddleware::check()) {
            header('Location: /dashboard.php');
            exit();
        }
        return true;
    }

    public static function handle()
    {
        if (isLoggedIn()) {
            redirect(APP_URL . 'dashboard');
            exit;
        }
        return true;
    }
}
