<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'authenticated' => false]);
    exit;
}

$pdo = getDB();
$userId = getUserId();
$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $sql = "SELECT r.id, r.title, r.description, r.type, r.category, r.file_path, r.file_name, r.external_url, r.doi, r.publication_date, r.views, r.downloads, r.likes, r.status, r.created_at, u.full_name as author_name, field.name as field_name FROM research r JOIN users u ON r.user_id = u.id LEFT JOIN academic_fields field ON u.academic_field_id = field.id";
    $params = [];

    if (isset($_GET['category']) && $_GET['category']) {
        $sql .= " WHERE r.category = ?";
        $params[] = $_GET['category'];
    } elseif (isset($_GET['search']) && $_GET['search']) {
        $sql .= " WHERE r.title LIKE ? OR r.description LIKE ?";
        $params[] = '%' . $_GET['search'] . '%';
        $params[] = '%' . $_GET['search'] . '%';
    }

    $sql .= " ORDER BY r.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT r.*, u.full_name as author_name, u.profile_picture, u.bio as author_bio, field.name as field_name FROM research r JOIN users u ON r.user_id = u.id LEFT JOIN academic_fields field ON u.academic_field_id = field.id WHERE r.id = ? AND r.status = 'approved'");
    $stmt->execute([$id]);
    $research = $stmt->fetch();

    if (!$research) {
        echo json_encode(['success' => false, 'message' => 'Research not found']);
        exit;
    }

    $pdo->prepare("UPDATE research SET views = views + 1 WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true, 'data' => $research]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
