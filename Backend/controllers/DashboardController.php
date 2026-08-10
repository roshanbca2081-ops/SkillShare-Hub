<?php

/**
 * Dashboard Controller
 */

require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class DashboardController
{
    public function index()
    {
        if (!AuthMiddleware::check()) {
            sendError('Unauthorized', 401);
        }

        $user = AuthMiddleware::user();
        sendSuccess([
            'user' => $user,
            'message' => "Welcome to your {$user->role} dashboard"
        ]);
    }
}
