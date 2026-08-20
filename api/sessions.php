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
    $sql = "SELECT s.id, s.booking_id, s.mentor_id, s.fresher_id, s.session_title, s.session_date, s.session_time, s.duration, s.meeting_link, s.status, s.feedback_mentor, s.feedback_fresher, s.rating_mentor, s.rating_fresher, CONCAT(u.firstname, ' ', u.lastname) as mentor_name, u.profile_picture as mentor_avatar, CONCAT(f.firstname, ' ', f.lastname) as fresher_name FROM sessions s JOIN users u ON s.mentor_id = u.id JOIN users f ON s.fresher_id = f.id";
    $params = [];

    if ($role === 'fresher') {
        $sql .= " WHERE s.fresher_id = ?";
        $params[] = $userId;
    } elseif ($role === 'mentor') {
        $sql .= " WHERE s.mentor_id = ?";
        $params[] = $userId;
    }

    $sql .= " ORDER BY s.session_date DESC, s.session_time DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT s.*, b.session_title as booking_title, b.session_description as booking_description, b.meeting_link as booking_meeting_link, b.duration as booking_duration, CONCAT(u.firstname, ' ', u.lastname) as mentor_name, u.profile_picture as mentor_avatar, u.bio as mentor_bio, m.specialization, u.hourly_rate, CONCAT(f.firstname, ' ', f.lastname) as fresher_name, c.name as course_name FROM sessions s JOIN users u ON s.mentor_id = u.id JOIN users f ON s.fresher_id = f.id LEFT JOIN mentors m ON u.id = m.user_id LEFT JOIN bookings b ON s.booking_id = b.id LEFT JOIN courses c ON b.course_id = c.id WHERE s.id = ?");
    $stmt->execute([$id]);
    $session = $stmt->fetch();

    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session not found']);
        exit;
    }
    echo json_encode(['success' => true, 'data' => $session]);
    exit;
}

if ($action === 'update_notes' && isset($_GET['id']) && $_POST) {
    $id = (int)$_GET['id'];
    $notes = sanitize($_POST['notes'] ?? '');
    $pdo->prepare("UPDATE sessions SET notes = ? WHERE id = ?")->execute([$notes, $id]);
    echo json_encode(['success' => true, 'message' => 'Notes updated']);
    exit;
}

if ($action === 'add_feedback' && isset($_GET['id']) && $_POST) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT mentor_id, fresher_id, status FROM sessions WHERE id = ?");
    $stmt->execute([$id]);
    $session = $stmt->fetch();

    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'Session not found']);
        exit;
    }

    if ($session['mentor_id'] == $userId) {
        $pdo->prepare("UPDATE sessions SET feedback_mentor = ?, rating_mentor = ? WHERE id = ?")->execute([
            sanitize($_POST['feedback'] ?? ''),
            (int)($_POST['rating'] ?? 0),
            $id
        ]);
    } elseif ($session['fresher_id'] == $userId) {
        $pdo->prepare("UPDATE sessions SET feedback_fresher = ?, rating_fresher = ? WHERE id = ?")->execute([
            sanitize($_POST['feedback'] ?? ''),
            (int)($_POST['rating'] ?? 0),
            $id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }
    echo json_encode(['success' => true, 'message' => 'Feedback added']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
