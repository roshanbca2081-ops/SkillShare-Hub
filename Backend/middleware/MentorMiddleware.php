<?php

/**
 * Mentor Middleware
 */

require_once __DIR__ . '/AuthMiddleware.php';

class MentorMiddleware
{
    public static function check()
    {
        AuthMiddleware::handle();
        $user = AuthMiddleware::user();
        if (!$user || ($user->role ?? '') !== 'mentor') {
            header('Location: /404.php');
            exit();
        }
        return true;
    }

    public static function handle()
    {
        AuthMiddleware::handle();
        if (!isMentor()) {
            setFlash('error', 'Access denied. Mentor only.');
            redirect(APP_URL . 'dashboard');
            exit;
        }
        return true;
    }
}
