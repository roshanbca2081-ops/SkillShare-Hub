<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

$pdo = getDB();
$role = getUserRole();
$userId = getUserId();

header('Content-Type: application/json; charset=UTF-8');

function countRows($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function getSum($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (float)$stmt->fetchColumn();
}

if ($role === 'admin') {
    $stats = [
        'users' => countRows($pdo, "SELECT COUNT(*) FROM users WHERE status = 'active'"),
        'freshers' => countRows($pdo, "SELECT COUNT(*) FROM users WHERE role = 'fresher' AND status = 'active'"),
        'mentors' => countRows($pdo, "SELECT COUNT(*) FROM users WHERE role = 'mentor' AND status = 'active'"),
        'fields' => countRows($pdo, "SELECT COUNT(*) FROM academic_fields WHERE status = 'active'"),
        'courses' => countRows($pdo, "SELECT COUNT(*) FROM courses WHERE status = 'active'"),
        'bookings' => countRows($pdo, "SELECT COUNT(*) FROM bookings"),
        'sessions' => countRows($pdo, "SELECT COUNT(*) FROM sessions"),
        'assignments' => countRows($pdo, "SELECT COUNT(*) FROM assignments"),
        'research' => countRows($pdo, "SELECT COUNT(*) FROM research"),
        'payments' => countRows($pdo, "SELECT COUNT(*) FROM payments"),
        'certificates' => countRows($pdo, "SELECT COUNT(*) FROM certificates"),
        'revenue' => getSum($pdo, "SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'"),
        'pending_bookings' => countRows($pdo, "SELECT COUNT(*) FROM bookings WHERE status = 'pending'"),
    ];
    echo json_encode(['success' => true, 'data' => $stats]);

} elseif ($role === 'mentor') {
    $stats = [
        'courses' => countRows($pdo, "SELECT COUNT(*) FROM courses WHERE status = 'active'"),
        'students' => countRows($pdo, "SELECT COUNT(DISTINCT fresher_id) FROM bookings WHERE mentor_id = ? AND status IN ('confirmed','completed')", [$userId]),
        'earnings' => getSum($pdo, "SELECT COALESCE(SUM(amount), 0) FROM payments WHERE user_id = ? AND status = 'completed'", [$userId]),
        'pending' => countRows($pdo, "SELECT COUNT(*) FROM bookings WHERE mentor_id = ? AND status = 'pending'", [$userId]),
        'sessions' => countRows($pdo, "SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND status = 'completed'", [$userId]),
        'rating' => getSum($pdo, "SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE reviewee_id = ?", [$userId]),
        'reviews' => countRows($pdo, "SELECT COUNT(*) FROM reviews WHERE reviewee_id = ?", [$userId]),
        'unread_messages' => countRows($pdo, "SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0", [$userId]),
        'unread_notifications' => countRows($pdo, "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0", [$userId]),
        'total_bookings' => countRows($pdo, "SELECT COUNT(*) FROM bookings WHERE mentor_id = ?", [$userId]),
        'upcoming_sessions' => countRows($pdo, "SELECT COUNT(*) FROM sessions WHERE mentor_id = ? AND status = 'scheduled' AND session_date >= CURDATE()", [$userId]),
    ];
    echo json_encode(['success' => true, 'data' => $stats]);

} elseif ($role === 'fresher') {
    $stats = [
        'enrolled_courses' => countRows($pdo, "SELECT COUNT(*) FROM course_enrollments WHERE user_id = ? AND status = 'active'", [$userId]),
        'completed_sessions' => countRows($pdo, "SELECT COUNT(*) FROM sessions WHERE fresher_id = ? AND status = 'completed'", [$userId]),
        'certificates' => countRows($pdo, "SELECT COUNT(*) FROM certificates WHERE user_id = ? AND status = 'issued'", [$userId]),
        'unread_messages' => countRows($pdo, "SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0", [$userId]),
        'unread_notifications' => countRows($pdo, "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0", [$userId]),
        'pending_assignments' => countRows($pdo, "SELECT COUNT(DISTINCT a.id) FROM assignments a JOIN submissions s ON a.id = s.assignment_id WHERE s.fresher_id = ? AND s.status = 'submitted'", [$userId]),
        'upcoming_bookings' => countRows($pdo, "SELECT COUNT(*) FROM bookings WHERE fresher_id = ? AND status IN ('confirmed','pending') AND session_date >= CURDATE()", [$userId]),
        'total_bookings' => countRows($pdo, "SELECT COUNT(*) FROM bookings WHERE fresher_id = ?", [$userId]),
        'total_sessions' => countRows($pdo, "SELECT COUNT(*) FROM sessions WHERE fresher_id = ?", [$userId]),
        'completed_courses' => countRows($pdo, "SELECT COUNT(*) FROM course_enrollments WHERE user_id = ? AND status = 'completed'", [$userId]),
    ];
    echo json_encode(['success' => true, 'data' => $stats]);
} else {
    echo json_encode(['success' => false, 'message' => 'Unknown role']);
}
