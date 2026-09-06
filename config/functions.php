<?php
/**
 * ============================================================
 * SkillShare Hub - Core Helper Functions
 * ============================================================
 * Project: /SkillShare-Hub/
 * Stack: PHP + MySQL + HTML + CSS + JavaScript
 * ============================================================
 */


/* ============================================================
   BASIC HELPERS
   ============================================================ */

/**
 * Redirect to another page.
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}


/**
 * Escape output safely for HTML.
 */
function sanitize($value)
{
    return htmlspecialchars(
        trim((string)$value),
        ENT_QUOTES,
        'UTF-8'
    );
}

function generateToken($length = 32)
{
    return bin2hex(random_bytes($length));
}


/**
 * Validate email.
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


/**
 * Validate phone number.
 */
function isValidPhone($phone)
{
    $phone = trim($phone);

    if ($phone === '') {
        return false;
    }

    return preg_match(
        '/^[0-9+\-\s().]{7,20}$/',
        $phone
    ) === 1;
}


/**
 * Validate URL.
 */
function isValidUrl($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}


/**
 * Generate CSRF token.
 */
function csrfToken()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}


/**
 * Verify CSRF token.
 */
function verifyCsrfToken($token)
{
    if (
        empty($_SESSION['csrf_token']) ||
        empty($token)
    ) {
        return false;
    }

    return hash_equals(
        $_SESSION['csrf_token'],
        $token
    );
}


/* ============================================================
   DATE / TIME
   ============================================================ */

/**
 * Get current date/time.
 */
function getCurrentDateTime()
{
    return date('Y-m-d H:i:s');
}


/**
 * Format date.
 */
function formatDate($date)
{
    if (empty($date)) {
        return 'N/A';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return 'N/A';
    }

    return date('M d, Y', $timestamp);
}


/**
 * Format date and time.
 */
function formatDateTime($date)
{
    if (empty($date)) {
        return 'N/A';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return 'N/A';
    }

    return date('M d, Y h:i A', $timestamp);
}


/**
 * Get relative time.
 */
function getTimeAgo($datetime)
{
    if (empty($datetime)) {
        return 'N/A';
    }

    $time = strtotime($datetime);

    if ($time === false) {
        return 'N/A';
    }

    $diff = time() - $time;

    if ($diff < 0) {
        return 'Just now';
    }

    if ($diff < 60) {
        return 'Just now';
    }

    if ($diff < 3600) {
        return floor($diff / 60) . ' minutes ago';
    }

    if ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    }

    if ($diff < 604800) {
        return floor($diff / 86400) . ' days ago';
    }

    if ($diff < 2592000) {
        return floor($diff / 604800) . ' weeks ago';
    }

    if ($diff < 31536000) {
        return floor($diff / 2592000) . ' months ago';
    }

    return date('M d, Y', $time);
}


/**
 * Get remaining time.
 */
function getDaysRemaining($date)
{
    if (empty($date)) {
        return 'N/A';
    }

    $future = strtotime($date);

    if ($future === false) {
        return 'N/A';
    }

    $diff = $future - time();

    if ($diff < 0) {
        return 'Overdue';
    }

    if ($diff < 3600) {
        return max(1, floor($diff / 60)) . ' minutes';
    }

    if ($diff < 86400) {
        return floor($diff / 3600) . ' hours';
    }

    return floor($diff / 86400) . ' days';
}


/* ============================================================
   TEXT HELPERS
   ============================================================ */

/**
 * Create URL-friendly slug.
 */
function createSlug($string)
{
    $string = strtolower(trim($string));

    $string = preg_replace(
        '/[^a-z0-9]+/',
        '-',
        $string
    );

    $string = trim($string, '-');

    return $string;
}


/**
 * Truncate text.
 */
function truncateText(
    $text,
    $length = 100,
    $ellipsis = '...'
) {
    $text = (string)$text;

    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length) . $ellipsis;
}


/**
 * Get user initials.
 */
function getInitials($name)
{
    if (empty($name)) {
        return 'U';
    }

    $words = preg_split(
        '/\s+/',
        trim($name)
    );

    $initials = '';

    foreach ($words as $word) {
        if ($word !== '') {
            $initials .= strtoupper(
                substr($word, 0, 1)
            );
        }
    }

    return substr($initials, 0, 2);
}


/* ============================================================
   STATUS / DISPLAY HELPERS
   ============================================================ */

/**
 * Get course level label.
 */
function getCourseLevel($level)
{
    $levels = [
        'beginner'     => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced'     => 'Advanced'
    ];

    return $levels[$level] ?? ucfirst((string)$level);
}


/**
 * Get Bootstrap-style badge class.
 */
function getStatusBadge($status)
{
    $badges = [
        'pending'   => 'warning',
        'active'    => 'success',
        'completed' => 'primary',
        'cancelled' => 'danger',
        'approved'  => 'success',
        'rejected'  => 'danger',
        'inactive'  => 'secondary',
        'ongoing'   => 'info',
        'scheduled' => 'primary',
        'draft'     => 'secondary',
        'archived'  => 'danger',
        'published' => 'success',
        'graded'    => 'success',
        'submitted' => 'info'
    ];

    return $badges[$status] ?? 'secondary';
}


/**
 * Format currency.
 */
function formatCurrency($amount, $currency = 'Rs. ')
{
    return $currency . number_format(
        (float)$amount,
        2
    );
}


/**
 * Get avatar.
 */
function getAvatar($user)
{
    $avatar = '';

    if (is_array($user)) {
        $avatar = $user['avatar'] ?? '';
    } elseif (is_object($user)) {
        $avatar = $user->avatar ?? '';
    }

    if (!empty($avatar)) {
        return $avatar;
    }

    return '/SkillShare-Hub/assets/images/default-avatar.png';
}


/**
 * Get random UI color.
 */
function getRandomColor()
{
    $colors = [
        '#6C63FF',
        '#FF6B6B',
        '#51CF66',
        '#FCC419',
        '#4DABF7',
        '#845EF7',
        '#FF922B',
        '#20C997',
        '#E599F7',
        '#FFD43B',
        '#339AF0',
        '#F06595'
    ];

    return $colors[array_rand($colors)];
}


/* ============================================================
   DATABASE HELPERS
   ============================================================ */

/**
 * Get user by ID.
 */
function getUserById($pdo, $userId)
{
    if (!$userId) {
        return null;
    }

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE id = ? LIMIT 1"
    );

    $stmt->execute([$userId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


/**
 * Get course by ID.
 */
function getCourseById($pdo, $courseId)
{
    if (!$courseId) {
        return null;
    }

    $stmt = $pdo->prepare(
        "SELECT * FROM courses WHERE id = ? LIMIT 1"
    );

    $stmt->execute([$courseId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


/**
 * Get session by ID.
 */
function getSessionById($pdo, $sessionId)
{
    if (!$sessionId) {
        return null;
    }

    $stmt = $pdo->prepare(
        "SELECT * FROM sessions WHERE id = ? LIMIT 1"
    );

    $stmt->execute([$sessionId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


/* ============================================================
   NOTIFICATIONS
   ============================================================ */

/**
 * Get unread notifications count.
 */
function getUnreadNotifications($pdo, $userId)
{
    if (!$userId) {
        return 0;
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM notifications
             WHERE user_id = ?
             AND is_read = 0"
        );

        $stmt->execute([$userId]);

        return (int)$stmt->fetchColumn();

    } catch (PDOException $e) {
        return 0;
    }
}


/**
 * Create notification.
 */
function createNotification(
    $pdo,
    $userId,
    $type,
    $title,
    $message,
    $link = null,
    $icon = null
) {
    try {

        $stmt = $pdo->prepare(
            "INSERT INTO notifications
            (
                user_id,
                type,
                title,
                message,
                link,
                icon
            )
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $userId,
            $type,
            $title,
            $message,
            $link,
            $icon
        ]);

        return $pdo->lastInsertId();

    } catch (PDOException $e) {
        return false;
    }
}


/**
 * Mark notification as read.
 */
function markNotificationsRead(
    $pdo,
    $userId,
    $notificationId = null
) {
    if (!$userId) {
        return false;
    }

    try {

        if ($notificationId) {

            $stmt = $pdo->prepare(
                "UPDATE notifications
                 SET is_read = 1,
                     read_at = NOW()
                 WHERE id = ?
                 AND user_id = ?"
            );

            $stmt->execute([
                $notificationId,
                $userId
            ]);

        } else {

            $stmt = $pdo->prepare(
                "UPDATE notifications
                 SET is_read = 1,
                     read_at = NOW()
                 WHERE user_id = ?"
            );

            $stmt->execute([$userId]);
        }

        return true;

    } catch (PDOException $e) {
        return false;
    }
}


/* ============================================================
   MESSAGES
   ============================================================ */

/**
 * Get unread message count.
 */
function getUnreadMessages($pdo, $userId)
{
    if (!$userId) {
        return 0;
    }

    try {

        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM messages
             WHERE receiver_id = ?
             AND is_read = 0"
        );

        $stmt->execute([$userId]);

        return (int)$stmt->fetchColumn();

    } catch (PDOException $e) {
        return 0;
    }
}


/* ============================================================
   SESSION HELPERS
   ============================================================ */

/**
 * Check whether a session is currently active.
 */
function isSessionActive($session)
{
    if (!$session) {
        return false;
    }

    if (($session['status'] ?? '') === 'ongoing') {
        return true;
    }

    if (
        empty($session['scheduled_at']) ||
        empty($session['duration'])
    ) {
        return false;
    }

    $start = strtotime(
        $session['scheduled_at']
    );

    if ($start === false) {
        return false;
    }

    $end = $start +
        ((int)$session['duration'] * 60);

    $now = time();

    return (
        ($session['status'] ?? '') === 'scheduled' &&
        $now >= $start &&
        $now <= $end
    );
}


/**
 * Check whether session has ended.
 */
function isSessionEnded($session)
{
    if (!$session) {
        return true;
    }

    if (($session['status'] ?? '') === 'completed') {
        return true;
    }

    if (
        empty($session['scheduled_at']) ||
        empty($session['duration'])
    ) {
        return false;
    }

    $start = strtotime(
        $session['scheduled_at']
    );

    if ($start === false) {
        return false;
    }

    $end = $start +
        ((int)$session['duration'] * 60);

    return time() > $end;
}


/**
 * Get number of bookings for session.
 */
function getSessionBookings(
    $pdo,
    $sessionId,
    $status = 'approved'
) {
    if (!$sessionId) {
        return 0;
    }

    try {

        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM bookings
             WHERE session_id = ?
             AND status = ?"
        );

        $stmt->execute([
            $sessionId,
            $status
        ]);

        return (int)$stmt->fetchColumn();

    } catch (PDOException $e) {
        return 0;
    }
}


/**
 * Get session participant count.
 */
function getSessionParticipants(
    $pdo,
    $sessionId
) {
    if (!$sessionId) {
        return 0;
    }

    try {

        $stmt = $pdo->prepare(
            "SELECT COUNT(DISTINCT fresher_id)
             FROM session_attendance
             WHERE session_id = ?"
        );

        $stmt->execute([$sessionId]);

        return (int)$stmt->fetchColumn();

    } catch (PDOException $e) {
        return 0;
    }
}


/* ============================================================
   ENROLLMENT / COURSE HELPERS
   ============================================================ */

/**
 * Check whether fresher is enrolled.
 */
function isEnrolled(
    $pdo,
    $studentId,
    $courseId
) {
    if (!$studentId || !$courseId) {
        return false;
    }

    try {

        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM enrollments
             WHERE fresher_id = ?
             AND course_id = ?
             AND status != 'dropped'"
        );

        $stmt->execute([
            $studentId,
            $courseId
        ]);

        return (int)$stmt->fetchColumn() > 0;

    } catch (PDOException $e) {
        return false;
    }
}


/**
 * Get student's course progress.
 */
function getStudentProgress(
    $pdo,
    $studentId,
    $courseId
) {
    if (!$studentId || !$courseId) {
        return 0;
    }

    try {

        $stmt = $pdo->prepare(
            "SELECT progress
             FROM enrollments
             WHERE fresher_id = ?
             AND course_id = ?
             LIMIT 1"
        );

        $stmt->execute([
            $studentId,
            $courseId
        ]);

        $result = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        return $result
            ? (float)$result['progress']
            : 0;

    } catch (PDOException $e) {
        return 0;
    }
}


/**
 * Get user's course count.
 */
function getUserCoursesCount(
    $pdo,
    $userId,
    $role = 'fresher'
) {
    if (!$userId) {
        return 0;
    }

    try {

        if ($role === 'mentor') {

            $stmt = $pdo->prepare(
                "SELECT COUNT(*)
                 FROM courses
                 WHERE mentor_id = ?
                 AND status = 'active'"
            );

        } else {

            $stmt = $pdo->prepare(
                "SELECT COUNT(*)
                 FROM enrollments
                 WHERE fresher_id = ?
                 AND status = 'active'"
            );
        }

        $stmt->execute([$userId]);

        return (int)$stmt->fetchColumn();

    } catch (PDOException $e) {
        return 0;
    }
}


/* ============================================================
   RATINGS
   ============================================================ */

/**
 * Get course rating.
 */
function getCourseRating(
    $pdo,
    $courseId
) {
    if (!$courseId) {
        return [
            'avg_rating' => 0,
            'total_ratings' => 0
        ];
    }

    try {

        $stmt = $pdo->prepare(
            "SELECT
                AVG(rating) AS avg_rating,
                COUNT(*) AS total_ratings
             FROM ratings
             WHERE course_id = ?"
        );

        $stmt->execute([$courseId]);

        $result = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        return $result ?: [
            'avg_rating' => 0,
            'total_ratings' => 0
        ];

    } catch (PDOException $e) {

        return [
            'avg_rating' => 0,
            'total_ratings' => 0
        ];
    }
}


/**
 * Get mentor rating.
 */
function getMentorRating(
    $pdo,
    $mentorId
) {
    if (!$mentorId) {
        return [
            'avg_rating' => 0,
            'total_ratings' => 0
        ];
    }

    try {

        $stmt = $pdo->prepare(
            "SELECT
                AVG(rating) AS avg_rating,
                COUNT(*) AS total_ratings
             FROM ratings
             WHERE mentor_id = ?"
        );

        $stmt->execute([$mentorId]);

        $result = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        return $result ?: [
            'avg_rating' => 0,
            'total_ratings' => 0
        ];

    } catch (PDOException $e) {

        return [
            'avg_rating' => 0,
            'total_ratings' => 0
        ];
    }
}


/* ============================================================
   FILE HELPERS
   ============================================================ */

/**
 * Get file size.
 */
function getFileSize($bytes)
{
    $bytes = (float)$bytes;

    if ($bytes >= 1073741824) {
        return number_format(
            $bytes / 1073741824,
            2
        ) . ' GB';
    }

    if ($bytes >= 1048576) {
        return number_format(
            $bytes / 1048576,
            2
        ) . ' MB';
    }

    if ($bytes >= 1024) {
        return number_format(
            $bytes / 1024,
            2
        ) . ' KB';
    }

    return $bytes . ' B';
}


/**
 * Get file extension.
 */
function getFileExtension($filename)
{
    return strtolower(
        pathinfo(
            $filename,
            PATHINFO_EXTENSION
        )
    );
}


/* ============================================================
   JSON HELPERS
   ============================================================ */

/**
 * Encode data as JSON.
 */
function escapeJson($data)
{
    return json_encode(
        $data,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );
}


/**
 * Decode JSON.
 */
function unescapeJson($data)
{
    if (empty($data)) {
        return [];
    }

    $result = json_decode(
        $data,
        true
    );

    return is_array($result)
        ? $result
        : [];
}


/**
 * Convert array to object.
 */
function toObject($array)
{
    return json_decode(
        json_encode($array)
    );
}


/**
 * Convert object to array.
 */
function toArray($object)
{
    return json_decode(
        json_encode($object),
        true
    );
}


/* ============================================================
   CERTIFICATE
   ============================================================ */

/**
 * Generate certificate code.
 */
function generateCertificateCode()
{
    return 'CERT-' .
        strtoupper(
            bin2hex(
                random_bytes(5)
            )
        );
}


/* ============================================================
   URL / REQUEST HELPERS
   ============================================================ */

/**
 * Get SkillShare Hub base URL.
 *
 * Project:
 * http://localhost/SkillShare-Hub/
 */
function getBaseUrl()
{
    return '/SkillShare-Hub/';
}


/**
 * Check AJAX request.
 */
function isAjax()
{
    return isset(
        $_SERVER['HTTP_X_REQUESTED_WITH']
    ) &&
    strtolower(
        $_SERVER['HTTP_X_REQUESTED_WITH']
    ) === 'xmlhttprequest';
}


/**
 * Return JSON response.
 */
function jsonResponse(
    $data,
    $status = 200
) {
    http_response_code($status);

    header(
        'Content-Type: application/json; charset=UTF-8'
    );

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;
}


/* ============================================================
   ACTIVITY LOG
   ============================================================ */

/**
 * Log user activity.
 *
 * This function safely does nothing if
 * system_logs table is not available.
 */
function logActivity(
    $pdo,
    $userId,
    $action,
    $description,
    $data = null
) {
    try {

        $stmt = $pdo->prepare(
            "INSERT INTO system_logs
            (
                user_id,
                action,
                description,
                ip_address,
                user_agent,
                data
            )
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $userId,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $data !== null
                ? json_encode($data)
                : null
        ]);

        return true;

    } catch (PDOException $e) {

        return false;
    }
}


/* ============================================================
   ACCESS CONTROL
   ============================================================ */

/**
 * Check whether user owns a resource.
 *
 * IMPORTANT:
 * $table should only be supplied
 * from trusted developer code.
 */
function hasAccess(
    $pdo,
    $userId,
    $resourceId,
    $table = 'resources'
) {
    if (!$userId || !$resourceId) {
        return false;
    }

    $allowedTables = [
        'resources',
        'courses',
        'sessions'
    ];

    if (!in_array(
        $table,
        $allowedTables,
        true
    )) {
        return false;
    }

    try {

        $sql = "
            SELECT COUNT(*)
            FROM {$table}
            WHERE id = ?
            AND (
                mentor_id = ?
                OR is_public = 1
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $resourceId,
            $userId
        ]);

        return (int)$stmt->fetchColumn() > 0;

    } catch (PDOException $e) {

        return false;
    }
}


/* ============================================================
   END OF FUNCTIONS
   ============================================================ */
?>