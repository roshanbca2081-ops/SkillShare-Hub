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
}
