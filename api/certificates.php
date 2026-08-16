<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'authenticated' => false]);
    exit;
}

$pdo = getDB();
$userId = getUserId();
$role = getUserRole();
$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $sql = "SELECT c.id, c.certificate_number, c.issue_date, c.expiry_date, c.status, c.created_at, u.full_name as recipient_name, u.profile_picture, course.name as course_name FROM certificates c JOIN users u ON c.user_id = u.id LEFT JOIN courses course ON c.course_id = course.id";
    $params = [];

    if ($role === 'fresher') {
        $sql .= " WHERE c.user_id = ?";
        $params[] = $userId;
    } elseif ($role === 'mentor') {
        $sql .= " WHERE c.course_id IN (SELECT id FROM courses WHERE academic_field_id = (SELECT academic_field_id FROM users WHERE id = ?))";
        $params[] = $userId;
    }

    $sql .= " ORDER BY c.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT c.*, u.full_name as recipient_name, u.profile_picture, course.name as course_name, field.name as field_name FROM certificates c JOIN users u ON c.user_id = u.id LEFT JOIN courses course ON c.course_id = course.id LEFT JOIN academic_fields field ON course.academic_field_id = field.id WHERE c.id = ?");
    $stmt->execute([$id]);
    $cert = $stmt->fetch();

    if (!$cert) {
        echo json_encode(['success' => false, 'message' => 'Certificate not found']);
        exit;
    }

    $cert['qr_data'] = BASE_URL . 'certificate/view.php?cert=' . $cert['certificate_number'];
    echo json_encode(['success' => true, 'data' => $cert]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
