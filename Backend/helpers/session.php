<?php

/**
 * Session Helper
 */

if (!function_exists('setFlashMessage')) {
    function setFlashMessage($key, $message)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$key] = $message;
    }
}

if (!function_exists('getFlashMessage')) {
    function getFlashMessage($key)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }
}

if (!function_exists('hasFlashMessage')) {
    function hasFlashMessage($key)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['flash'][$key]);
    }
}
