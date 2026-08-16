<?php
/**
 * Admin Helper Functions
 */

function admin_sidebar_items() {
    return [
        'Main' => [
            ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'link' => ADMIN_URL . 'index.php'],
            ['label' => 'Users', 'icon' => 'fa-users', 'link' => ADMIN_URL . 'users.php'],
            ['label' => 'Mentors', 'icon' => 'fa-user-tie', 'link' => ADMIN_URL . 'mentors.php'],
            ['label' => 'Freshers', 'icon' => 'fa-user-graduate', 'link' => ADMIN_URL . 'freshers.php'],
        ],
        'Management' => [
            ['label' => 'Academic Fields', 'icon' => 'fa-layer-group', 'link' => ADMIN_URL . 'academic-fields.php'],
            ['label' => 'Courses', 'icon' => 'fa-book-open', 'link' => ADMIN_URL . 'courses.php'],
            ['label' => 'Bookings', 'icon' => 'fa-calendar-check', 'link' => ADMIN_URL . 'bookings.php'],
            ['label' => 'Sessions', 'icon' => 'fa-video', 'link' => ADMIN_URL . 'sessions.php'],
            ['label' => 'Assignments', 'icon' => 'fa-file-pen', 'link' => ADMIN_URL . 'assignments.php'],
            ['label' => 'Research', 'icon' => 'fa-flask', 'link' => ADMIN_URL . 'research.php'],
            ['label' => 'Certificates', 'icon' => 'fa-award', 'link' => ADMIN_URL . 'certificates.php'],
        ],
        'Finance' => [
            ['label' => 'Payments', 'icon' => 'fa-credit-card', 'link' => ADMIN_URL . 'payments.php'],
            ['label' => 'Reports', 'icon' => 'fa-chart-pie', 'link' => ADMIN_URL . 'reports.php'],
        ],
        'System' => [
            ['label' => 'Notifications', 'icon' => 'fa-bell', 'link' => ADMIN_URL . 'notifications.php'],
            ['label' => 'Messages', 'icon' => 'fa-envelope', 'link' => ADMIN_URL . 'messages.php'],
            ['label' => 'Interview Prep', 'icon' => 'fa-comments', 'link' => ADMIN_URL . 'interview.php'],
            ['label' => 'Feedback', 'icon' => 'fa-star', 'link' => ADMIN_URL . 'feedback.php'],
        ],
        'Account' => [
            ['label' => 'Profile', 'icon' => 'fa-id-card', 'link' => ADMIN_URL . 'profile.php'],
            ['label' => 'Settings', 'icon' => 'fa-gear', 'link' => ADMIN_URL . 'settings.php'],
            ['label' => 'Logout', 'icon' => 'fa-sign-out-alt', 'link' => ADMIN_URL . 'logout.php'],
        ],
    ];
}

function admin_status_badge($status) {
    $map = [
        'active' => 'approved',
        'approved' => 'approved',
        'completed' => 'approved',
        'issued' => 'approved',
        'verified' => 'approved',
        'paid' => 'approved',
        'inactive' => 'rejected',
        'rejected' => 'rejected',
        'revoked' => 'rejected',
        'failed' => 'rejected',
        'refunded' => 'rejected',
        'cancelled' => 'rejected',
        'pending' => 'pending',
        'suspended' => 'pending',
        'scheduled' => 'pending',
        'ongoing' => 'pending',
        'published' => 'approved',
    ];
    return $map[strtolower($status)] ?? 'pending';
}

function admin_role_badge($role) {
    $map = [
        'admin' => 'danger',
        'mentor' => 'secondary',
        'fresher' => 'primary',
    ];
    return $map[strtolower($role)] ?? 'primary';
}

function admin_format_date($date) {
    if (empty($date)) return 'N/A';
    return date('M j, Y', strtotime($date));
}

function admin_format_datetime($datetime) {
    if (empty($datetime)) return 'N/A';
    return date('M j, Y g:i A', strtotime($datetime));
}

function admin_time_ago($datetime) {
    if (empty($datetime)) return 'N/A';
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

function admin_confirm_delete($message = 'Are you sure you want to delete this item?') {
    return "return confirm('$message')";
}

function admin_redirect_back() {
    $referer = $_SERVER['HTTP_REFERER'] ?? ADMIN_URL . 'index.php';
    header('Location: ' . $referer);
    exit();
}
