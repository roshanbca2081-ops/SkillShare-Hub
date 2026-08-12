<?php

/**
 * Core Helper Functions
 */

if (!function_exists('sanitize')) {
    function sanitize($data)
    {
        if (is_array($data)) {
            return array_map('sanitize', $data);
        }
        return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('slugify')) {
    function slugify($text)
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data);
        exit();
    }
}

if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('dd')) {
    function dd($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit;
    }
}

if (!function_exists('redirectBack')) {
    function redirectBack() {
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL;
        redirect($referer);
    }
}

if (!function_exists('successResponse')) {
    function successResponse($data = [], $message = 'Success') {
        return jsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse($message = 'Error', $code = 400, $errors = []) {
        return jsonResponse([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}

if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('csrfField')) {
    function csrfField() {
        return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
    }
}

if (!function_exists('checkCSRF')) {
    function checkCSRF() {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!validateCSRFToken($token)) {
            errorResponse('Invalid CSRF token', 403);
            exit;
        }
        return true;
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('getUserId')) {
    function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('getUserRole')) {
    function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return getUserRole() === 'admin';
    }
}

if (!function_exists('isMentor')) {
    function isMentor() {
        return getUserRole() === 'mentor';
    }
}

if (!function_exists('isFresher')) {
    function isFresher() {
        return getUserRole() === 'fresher';
    }
}

if (!function_exists('hasRole')) {
    function hasRole($role) {
        return getUserRole() === $role;
    }
}

if (!function_exists('setFlash')) {
    function setFlash($key, $value) {
        $_SESSION['flash'][$key] = $value;
    }
}

if (!function_exists('getFlash')) {
    function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $value;
        }
        return null;
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $format = DATE_DISPLAY) {
        if (empty($date)) return 'N/A';
        return date($format, strtotime($date));
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        if ($diff < 2592000) return floor($diff / 604800) . 'w ago';
        if ($diff < 31536000) return floor($diff / 2592000) . 'mo ago';
        return floor($diff / 31536000) . 'y ago';
    }
}

if (!function_exists('truncate')) {
    function truncate($text, $length = 100, $suffix = '...') {
        if (strlen($text) <= $length) return $text;
        return substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('getFileExtension')) {
    function getFileExtension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}

if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes) {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $k = 1024;
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $units[$i];
    }
}

if (!function_exists('generateUniqueId')) {
    function generateUniqueId() {
        return uniqid() . '_' . bin2hex(random_bytes(8));
    }
}

if (!function_exists('generateBookingNumber')) {
    function generateBookingNumber() {
        return 'BK-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }
}

if (!function_exists('generateCertificateNumber')) {
    function generateCertificateNumber() {
        return 'SH-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(6)));
    }
}

if (!function_exists('getGravatar')) {
    function getGravatar($email, $size = 80) {
        $hash = md5(strtolower(trim($email)));
        return 'https://www.gravatar.com/avatar/' . $hash . '?s=' . $size . '&d=mm';
    }
}

if (!function_exists('validateEmail')) {
    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('validatePhone')) {
    function validatePhone($phone) {
        return preg_match('/^[0-9+\-\s()]{7,20}$/', $phone);
    }
}

if (!function_exists('validatePassword')) {
    function validatePassword($password) {
        return strlen($password) >= 6;
    }
}

if (!function_exists('validateUrl')) {
    function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
