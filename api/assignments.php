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
    if ($role === 'fresher') {
        $sql = "SELECT a.id, a.title, a.description, a.deadline, a.attachment, a.max_score, a.status, c.name as course_name, m.full_name as mentor_name, s.id as submission_id, s.submission_text, s.file_path as submission_file, s.score, s.feedback, s.status as submission_status, s.submitted_at, s.reviewed_at, s.is_late FROM assignments a JOIN users m ON a.mentor_id = m.id LEFT JOIN courses c ON a.course_id = c.id LEFT JOIN submissions s ON a.id = s.assignment_id AND s.fresher_id = ? WHERE a.status IN ('published','closed') ORDER BY a.deadline ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
    } elseif ($role === 'mentor') {
        $sql = "SELECT a.id, a.title, a.deadline, a.max_score, a.status, a.total_submissions, c.name as course_name, (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id AND status = 'submitted') as pending_submissions FROM assignments a LEFT JOIN courses c ON a.course_id = c.id WHERE a.mentor_id = ? ORDER BY a.deadline ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
    } else {
        $sql = "SELECT a.id, a.title, a.deadline, a.status, c.name as course_name, m.full_name as mentor_name FROM assignments a JOIN users m ON a.mentor_id = m.id LEFT JOIN courses c ON a.course_id = c.id ORDER BY a.created_at DESC";
        $stmt = $pdo->query($sql);
    }
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT a.*, c.name as course_name, u.full_name as mentor_name FROM assignments a JOIN users u ON a.mentor_id = u.id LEFT JOIN courses c ON a.course_id = c.id WHERE a.id = ?");
    $stmt->execute([$id]);
    $assignment = $stmt->fetch();

    if (!$assignment) {
        echo json_encode(['success' => false, 'message' => 'Assignment not found']);
        exit;
    }

    if ($role === 'fresher') {
        $stmt = $pdo->prepare("SELECT * FROM submissions WHERE assignment_id = ? AND fresher_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$id, $userId]);
        $assignment['submission'] = $stmt->fetch();
    } elseif ($role === 'mentor') {
        $stmt = $pdo->prepare("SELECT s.*, u.full_name as fresher_name, u.profile_picture FROM submissions s JOIN users u ON s.fresher_id = u.id WHERE s.assignment_id = ? ORDER BY s.created_at DESC");
        $stmt->execute([$id]);
        $assignment['submissions'] = $stmt->fetchAll();
    }

    echo json_encode(['success' => true, 'data' => $assignment]);
    exit;
}

if ($action === 'submit' && $role === 'fresher' && isset($_GET['id']) && ($_POST || $_FILES)) {
    $id = (int)$_GET['id'];
    $submissionText = sanitize($_POST['submission_text'] ?? '');
    $filePath = null;
    $fileName = null;
    $fileSize = 0;

    if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf','doc','docx','txt','zip','jpg','png','jpeg'];
        $ext = strtolower(pathinfo($_FILES['submission_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $fileName = 'submission_' . $userId . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/submissions/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $uploadDir . $fileName)) {
                $filePath = $fileName;
                $fileSize = $_FILES['submission_file']['size'];
            }
        }
    }

    $existing = $pdo->prepare("SELECT id FROM submissions WHERE assignment_id = ? AND fresher_id = ?");
    $existing->execute([$id, $userId]);
    if ($existing->fetch()) {
        $stmt = $pdo->prepare("UPDATE submissions SET submission_text = ?, file_path = ?, file_name = ?, file_size = ?, submitted_at = NOW(), status = 'submitted' WHERE assignment_id = ? AND fresher_id = ?");
        $stmt->execute([$submissionText, $filePath, $fileName, $fileSize, $id, $userId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO submissions (assignment_id, fresher_id, submission_text, file_path, file_name, file_size, status, submitted_at, created_at) VALUES (?, ?, ?, ?, ?, ?, 'submitted', NOW(), NOW())");
        $stmt->execute([$id, $userId, $submissionText, $filePath, $fileName, $fileSize]);
    }

    $mentorId = $pdo->query("SELECT mentor_id FROM assignments WHERE id = " . (int)$id)->fetchColumn();
    if ($mentorId) {
        $payload = json_encode(['assignment_id' => (int)$id, 'fresher_id' => $userId]);
        $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, payload, is_read, created_at) VALUES (?, 'assignment_submitted', 'New Assignment Submission', 'A fresher has submitted an assignment.', ?, 0, NOW())")->execute([$mentorId, $payload]);
    }

    echo json_encode(['success' => true, 'message' => 'Assignment submitted successfully']);
    exit;
}

if ($action === 'create' && $role === 'mentor' && $_POST) {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $courseId = (int)($_POST['course_id'] ?? 0);
    $deadline = sanitize($_POST['deadline'] ?? '');
    $instructions = sanitize($_POST['instructions'] ?? '');
    $attachment = null;

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf','doc','docx','txt','zip','jpg','png','jpeg'];
        $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $attachment = 'assignment_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/assignments/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $attachment);
        }
    }

    $stmt = $pdo->prepare("INSERT INTO assignments (mentor_id, course_id, title, description, instructions, file_path, file_name, deadline, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'published', NOW())");
    $stmt->execute([$userId, $courseId ?: null, $title, $description, $instructions, $attachment, $attachment, $deadline ?: date('Y-m-d H:i:s', strtotime('+7 days'))]);

    echo json_encode(['success' => true, 'message' => 'Assignment created successfully', 'data' => ['id' => $pdo->lastInsertId()]]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
