<?php
// Shared admin sidebar configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role'])) {
    header('Location: /SkillShare-Hub/login.php');
    exit;
}
$expectedRole = 'admin';
if ($_SESSION['user_role'] !== $expectedRole) {
    switch ($_SESSION['user_role']) {
        case 'mentor':
            header('Location: /SkillShare-Hub/dashboard/mentor/index.php');
            break;
        case 'fresher':
            header('Location: /SkillShare-Hub/dashboard/fresher/index.php');
            break;
        default:
            header('Location: /SkillShare-Hub/login.php');
            break;
    }
    exit;
}
$sidebar_role = 'admin';
$sidebar_items = [
    'Main' => [
        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'link' => 'index.php'],
        ['label' => 'Users', 'icon' => 'fa-users', 'link' => 'users.php'],
        ['label' => 'Mentors', 'icon' => 'fa-user-tie', 'link' => 'mentors.php'],
        ['label' => 'Freshers', 'icon' => 'fa-user-graduate', 'link' => 'freshers.php'],
    ],
    'Management' => [
        ['label' => 'Courses', 'icon' => 'fa-book-open', 'link' => 'courses.php'],
        ['label' => 'Academic Fields', 'icon' => 'fa-layer-group', 'link' => 'academic-fields.php'],
        ['label' => 'Bookings', 'icon' => 'fa-calendar-check', 'link' => 'bookings.php', 'badge' => '5'],
        ['label' => 'Sessions', 'icon' => 'fa-video', 'link' => 'sessions.php'],
        ['label' => 'Assignments', 'icon' => 'fa-file-pen', 'link' => 'assignments.php'],
        ['label' => 'Research', 'icon' => 'fa-flask', 'link' => 'research.php'],
        ['label' => 'Certificates', 'icon' => 'fa-award', 'link' => 'certificates.php'],
    ],
    'Finance' => [
        ['label' => 'Payments', 'icon' => 'fa-credit-card', 'link' => 'payments.php'],
        ['label' => 'Reports', 'icon' => 'fa-chart-pie', 'link' => 'reports.php'],
    ],
    'System' => [
        ['label' => 'Notifications', 'icon' => 'fa-bell', 'link' => 'notifications.php', 'badge' => '3'],
        ['label' => 'Profile', 'icon' => 'fa-user', 'link' => 'profile.php'],
        ['label' => 'Settings', 'icon' => 'fa-gear', 'link' => 'settings.php'],
    ],
];
