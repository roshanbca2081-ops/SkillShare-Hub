<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn() || getUserRole() !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'authenticated' => false]);
    exit;
}

$pdo = getDB();
$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $role = $_GET['role'] ?? '';
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    $sql = "SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) AS full_name, u.email, u.phone, u.profile_picture, u.role, u.status, u.email_verified AS is_verified, u.hourly_rate, u.created_at, u.last_login, af.name as field_name, c.name as course_name, m.specialization, m.rating FROM users u LEFT JOIN academic_fields af ON u.academic_field_id = af.id LEFT JOIN courses c ON u.course_id = c.id LEFT JOIN mentors m ON u.id = m.user_id WHERE 1=1";
    $params = [];

    if ($role) { $sql .= " AND u.role = ?"; $params[] = $role; }
    if ($status) { $sql .= " AND u.status = ?"; $params[] = $status; }
    if ($search) { $sql .= " AND (CONCAT(u.firstname, ' ', u.lastname) LIKE ? OR u.email LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

    $sql .= " ORDER BY u.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT u.*, af.name as field_name, c.name as course_name, m.* FROM users u LEFT JOIN academic_fields af ON u.academic_field_id = af.id LEFT JOIN courses c ON u.course_id = c.id LEFT JOIN mentors m ON u.id = m.user_id WHERE u.id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) { echo json_encode(['success' => false, 'message' => 'User not found']); exit; }
    echo json_encode(['success' => true, 'data' => $user]);
    exit;
}

if ($action === 'update_status' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = sanitize($_POST['status'] ?? 'active');
    $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$status, $id]);
    echo json_encode(['success' => true, 'message' => 'User status updated']);
    exit;
}

if ($action === 'update_role' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $role = sanitize($_POST['role'] ?? 'fresher');
    $pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, $id]);
    echo json_encode(['success' => true, 'message' => 'User role updated']);
    exit;
}

if ($action === 'stats') {
    $stats = [
        'users' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn(),
        'freshers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'fresher' AND status = 'active'")->fetchColumn(),
        'mentors' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mentor' AND status = 'active'")->fetchColumn(),
        'admins' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND status = 'active'")->fetchColumn(),
        'fields' => (int)$pdo->query("SELECT COUNT(*) FROM academic_fields WHERE status = 'active'")->fetchColumn(),
        'courses' => (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn(),
        'bookings' => (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
        'sessions' => (int)$pdo->query("SELECT COUNT(*) FROM sessions")->fetchColumn(),
        'assignments' => (int)$pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn(),
        'research' => (int)$pdo->query("SELECT COUNT(*) FROM research")->fetchColumn(),
        'payments' => (int)$pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn(),
        'certificates' => (int)$pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn(),
        'revenue' => (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'")->fetchColumn(),
        'pending_bookings' => (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn(),
    ];

    $recentUsers = $pdo->query("SELECT id, CONCAT(firstname, ' ', lastname) AS full_name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT 10")->fetchAll();
    $recentBookings = $pdo->query("SELECT b.id, b.booking_number, b.session_title, b.session_date, b.status, CONCAT(u.firstname, ' ', u.lastname) as mentor_name, CONCAT(f.firstname, ' ', f.lastname) as fresher_name FROM bookings b JOIN users u ON b.mentor_id = u.id JOIN users f ON b.fresher_id = f.id ORDER BY b.created_at DESC LIMIT 10")->fetchAll();

    echo json_encode(['success' => true, 'data' => ['stats' => $stats, 'recent_users' => $recentUsers, 'recent_bookings' => $recentBookings]]);
    exit;
}
