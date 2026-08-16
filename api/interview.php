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
    $sql = "SELECT q.id, q.question, q.category, q.sub_category, q.difficulty, q.company, q.answer, q.tips, q.likes, q.views, q.created_at FROM interview_questions q WHERE q.status = 'approved'";
    $params = [];

    if (isset($_GET['category']) && $_GET['category']) {
        $sql .= " AND q.category = ?";
        $params[] = $_GET['category'];
    }
    if (isset($_GET['difficulty']) && $_GET['difficulty']) {
        $sql .= " AND q.difficulty = ?";
        $params[] = $_GET['difficulty'];
    }
    if (isset($_GET['search']) && $_GET['search']) {
        $sql .= " AND (q.question LIKE ? OR q.answer LIKE ?)";
        $params[] = '%' . $_GET['search'] . '%';
        $params[] = '%' . $_GET['search'] . '%';
    }

    $sql .= " ORDER BY q.created_at DESC LIMIT 100";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $questions = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $questions]);
    exit;
}

if ($action === 'categories') {
    $stmt = $pdo->query("SELECT DISTINCT category FROM interview_questions WHERE status = 'approved' ORDER BY category");
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'practice' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM interview_questions WHERE id = ? AND status = 'approved'");
    $stmt->execute([$id]);
    $q = $stmt->fetch();
    if (!$q) { echo json_encode(['success' => false, 'message' => 'Question not found']); exit; }
    echo json_encode(['success' => true, 'data' => $q]);
    exit;
}

if ($action === 'create' && $role === 'mentor' && $_POST) {
    $question = sanitize($_POST['question'] ?? '');
    $category = sanitize($_POST['category'] ?? '');
    $subCategory = sanitize($_POST['sub_category'] ?? '');
    $difficulty = sanitize($_POST['difficulty'] ?? 'medium');
    $answer = sanitize($_POST['answer'] ?? '');
    $tips = sanitize($_POST['tips'] ?? '');

    if (!$question || !$category) { echo json_encode(['success' => false, 'message' => 'Question and category required']); exit; }

    $stmt = $pdo->prepare("INSERT INTO interview_questions (user_id, question, category, sub_category, difficulty, company, answer, tips, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$userId, $question, $category, $subCategory, $difficulty, sanitize($_POST['company'] ?? ''), $answer, $tips]);
    echo json_encode(['success' => true, 'message' => 'Question created', 'data' => ['id' => $pdo->lastInsertId()]]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
