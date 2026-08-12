<?php

/**
 * Fresher Middleware
 */

require_once __DIR__ . '/AuthMiddleware.php';

class FresherMiddleware
{
    public static function check()
    {
        AuthMiddleware::handle();
        $user = AuthMiddleware::user();
        if (!$user || ($user->role ?? '') !== 'fresher') {
            header('Location: /404.php');
            exit();
        }
        return true;
    }

    public static function handle()
    {
        AuthMiddleware::handle();
        if (!isFresher()) {
            setFlash('error', 'Access denied. Fresher only.');
            redirect(APP_URL . 'dashboard');
            exit;
        }
        return true;
    }
}
