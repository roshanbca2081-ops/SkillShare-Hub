<?php
// Shared fresher sidebar configuration
$sidebar_role = 'fresher';
$sidebar_items = [
    'Main' => [
        ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'link' => 'index.php'],
        ['label' => 'Courses', 'icon' => 'fa-book-open', 'link' => 'courses.php'],
        ['label' => 'Mentors', 'icon' => 'fa-user-tie', 'link' => 'mentors.php'],
        ['label' => 'Academic Fields', 'icon' => 'fa-layer-group', 'link' => 'academic-fields.php'],
        ['label' => 'Research', 'icon' => 'fa-flask', 'link' => 'research.php'],
    ],
    'Learning' => [
        ['label' => 'Bookings', 'icon' => 'fa-calendar-check', 'link' => 'bookings.php', 'badge' => '2'],
        ['label' => 'Sessions', 'icon' => 'fa-video', 'link' => 'sessions.php'],
        ['label' => 'Assignments', 'icon' => 'fa-file-pen', 'link' => 'assignments.php'],
        ['label' => 'Interview', 'icon' => 'fa-people-group', 'link' => 'interview.php'],
        ['label' => 'Certificates', 'icon' => 'fa-award', 'link' => 'certificates.php'],
    ],
    'Account' => [
        ['label' => 'Messages', 'icon' => 'fa-envelope', 'link' => 'messages.php'],
        ['label' => 'Notifications', 'icon' => 'fa-bell', 'link' => 'notifications.php', 'badge' => '4'],
        ['label' => 'Payments', 'icon' => 'fa-credit-card', 'link' => 'payments.php'],
        ['label' => 'Profile', 'icon' => 'fa-user', 'link' => 'profile.php'],
        ['label' => 'Settings', 'icon' => 'fa-gear', 'link' => 'settings.php'],
    ],
];
