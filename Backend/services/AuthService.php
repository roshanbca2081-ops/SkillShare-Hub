<?php

/**
 * Auth Service
 */

require_once __DIR__ . '/../models/User.php';

class AuthService
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login($email, $password, $remember = false)
    {
        $user = $this->userModel->findByEmail($email);
        if ($user && password_verify($password, $user->password)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_data'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ];
            return $user;
        }
        return false;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user_id']);
        unset($_SESSION['user_data']);
        session_destroy();
        return true;
    }

    public function validateVerificationToken($token)
    {
        return false;
    }

    public function invalidateVerificationToken($token)
    {
        return true;
    }
}
