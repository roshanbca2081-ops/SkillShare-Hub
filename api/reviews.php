<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$pdo = getDB();
$userId = getUserId();
$role = getUserRole();
$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $mentorId = isset($_GET['mentor_id']) ? (int)$_GET['mentor_id'] : 0;
    if (!$mentorId) {
        echo json_encode(['success' => false, 'message' => 'Mentor ID required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT r.*, u.full_name as reviewer_name, u.profile_picture as reviewer_avatar FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewee_id = ? AND r.is_public = 1 ORDER BY r.created_at DESC LIMIT 20");
    $stmt->execute([$mentorId]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($action === 'create' && $_POST) {
    $mentorId = (int)($_POST['mentor_id'] ?? 0);
    $bookingId = (int)($_POST['booking_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = sanitize($_POST['comment'] ?? '');

    if (!$mentorId || !$bookingId || !$rating || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid review data']);
        exit;
    }

    $booking = $pdo->prepare("SELECT b.*, s.id as session_id FROM bookings b LEFT JOIN sessions s ON s.booking_id = b.id WHERE b.id = ? AND b.user_id = ? AND b.mentor_id = ? AND b.status = 'completed'");
    $booking->execute([$bookingId, $userId, $mentorId]);
    $b = $booking->fetch();

    if (!$b) {
        echo json_encode(['success' => false, 'message' => 'Booking not found or not completed']);
        exit;
    }

    $existing = $pdo->prepare("SELECT id FROM reviews WHERE reviewer_id = ? AND reviewee_id = ? AND booking_id = ?");
    $existing->execute([$userId, $mentorId, $bookingId]);
    if ($existing->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already reviewed this session']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO reviews (reviewer_id, reviewee_id, booking_id, rating, comment, is_public, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$userId, $mentorId, $bookingId, $rating, $comment]);

        $stmt = $pdo->prepare("SELECT COUNT(*) as count, AVG(rating) as avg_rating FROM reviews WHERE reviewee_id = ?");
        $stmt->execute([$mentorId]);
        $row = $stmt->fetch();
        $avgRating = $row['avg_rating'] !== null ? round((float)$row['avg_rating'], 2) : 0.00;
        $reviewCount = (int)$row['count'];

        $pdo->prepare("UPDATE mentors SET rating = ?, reviews_count = ? WHERE user_id = ?")->execute([$avgRating, $reviewCount, $mentorId]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
