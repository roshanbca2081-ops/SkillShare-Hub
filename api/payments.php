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
    $sql = "SELECT p.id, p.booking_id, p.invoice_number, p.amount, p.tax_amount, p.total_amount, p.payment_method, p.payment_type, p.transaction_id, p.payment_gateway, p.status, p.payment_date, p.created_at, b.session_title, b.booking_number FROM payments p LEFT JOIN bookings b ON p.booking_id = b.id";
    $params = [];

    if ($role === 'fresher') {
        $sql .= " WHERE p.user_id = ?";
        $params[] = $userId;
    } elseif ($role === 'mentor') {
        $sql .= " WHERE p.user_id = ? OR (p.booking_id IN (SELECT id FROM bookings WHERE mentor_id = ?))";
        $params[] = $userId;
        $params[] = $userId;
    }

    $sql .= " ORDER BY p.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT p.*, b.session_title, b.booking_number, b.status as booking_status FROM payments p LEFT JOIN bookings b ON p.booking_id = b.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $payment = $stmt->fetch();

    if (!$payment) {
        echo json_encode(['success' => false, 'message' => 'Payment not found']);
        exit;
    }

    if ($role === 'fresher' && $payment['user_id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    echo json_encode(['success' => true, 'data' => $payment]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
