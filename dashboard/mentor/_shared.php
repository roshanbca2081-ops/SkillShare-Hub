<?php
// Shared mentor sidebar configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role'])) {
    header('Location: /SkillShare-Hub/login.php');
    exit;
}
$expectedRole = 'mentor';
if ($_SESSION['user_role'] !== $expectedRole) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            header('Location: /SkillShare-Hub/dashboard/admin/index.php');
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
$sidebar_role = 'mentor';
$sidebar_items = [
    'Main' => [
        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'link' => 'index.php'],
        ['label' => 'My Courses', 'icon' => 'fa-book-open', 'link' => 'my-courses.php'],
        ['label' => 'Students', 'icon' => 'fa-user-graduate', 'link' => 'students.php'],
    ],
    'Engagement' => [
        ['label' => 'Bookings', 'icon' => 'fa-calendar-check', 'link' => 'bookings.php', 'badge' => '4'],
        ['label' => 'Sessions', 'icon' => 'fa-video', 'link' => 'sessions.php'],
        ['label' => 'Assignments', 'icon' => 'fa-file-pen', 'link' => 'assignments.php'],
        ['label' => 'Interview', 'icon' => 'fa-people-group', 'link' => 'interview.php'],
        ['label' => 'Research', 'icon' => 'fa-flask', 'link' => 'research.php'],
    ],
    'Account' => [
        ['label' => 'Messages', 'icon' => 'fa-envelope', 'link' => 'messages.php', 'badge' => '6'],
        ['label' => 'Notifications', 'icon' => 'fa-bell', 'link' => 'notifications.php'],
        ['label' => 'Certificates', 'icon' => 'fa-award', 'link' => 'certificates.php'],
        ['label' => 'Profile', 'icon' => 'fa-user', 'link' => 'profile.php'],
        ['label' => 'Settings', 'icon' => 'fa-gear', 'link' => 'settings.php'],
    ],
];
