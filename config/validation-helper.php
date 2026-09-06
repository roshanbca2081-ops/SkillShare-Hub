<?php
// ============================================
// Validation Helper Functions// ============================================

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    return preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $phone);
}

function validatePassword($password) {
    return strlen($password) >= 8 &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/[0-9]/', $password) &&
           preg_match('/[^A-Za-z0-9]/', $password);
}

function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function validateFutureDate($date) {
    return strtotime($date) > time();
}

function validatePastDate($date) {
    return strtotime($date) < time();
}

function validateRange($value, $min, $max) {
    return $value >= $min && $value <= $max;
}

function validateInArray($value, $allowed) {
    return in_array($value, $allowed);
}

function validateUnique($pdo, $field, $table, $column, $value, $excludeId = null) {
    $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
    $params = [$value];
    
    if ($excludeId) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn() == 0;
}

function validateExists($pdo, $field, $table, $column, $value) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
    $stmt->execute([$value]);
    return $stmt->fetchColumn() > 0;
}

// CSRF Protection
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Rate Limiting
function checkRateLimit($key, $limit = 5, $time = 300) {
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = [
            'count' => 1,
            'first_attempt' => time()
        ];
        return true;
    }
    
    $data = $_SESSION['rate_limit'][$key];
    
    if (time() - $data['first_attempt'] > $time) {
        $_SESSION['rate_limit'][$key] = [
            'count' => 1,
            'first_attempt' => time()
        ];
        return true;
    }
    
    if ($data['count'] >= $limit) {
        return false;
    }
    
    $_SESSION['rate_limit'][$key]['count']++;
    return true;
}