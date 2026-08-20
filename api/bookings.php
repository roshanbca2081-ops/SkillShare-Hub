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
    $sql = "SELECT b.id, b.booking_number, b.session_title, b.session_description, b.session_date, b.session_time, b.duration, b.hourly_rate, b.total_amount, b.meeting_link, b.status, b.payment_status, b.created_at, CONCAT(u.firstname, ' ', u.lastname) as mentor_name, u.profile_picture as mentor_avatar, m.specialization, s.name as skill_name FROM bookings b JOIN users u ON b.mentor_id = u.id LEFT JOIN mentors m ON u.id = m.user_id LEFT JOIN skills s ON b.skill_id = s.id";
    $params = [];

    if ($role === 'fresher') {
        $sql .= " WHERE b.fresher_id = ?";
        $params[] = $userId;
    } elseif ($role === 'mentor') {
        $sql .= " WHERE b.mentor_id = ?";
        $params[] = $userId;
    } else {
        $sql .= " WHERE 1=1";
    }

    if (isset($_GET['status']) && $_GET['status']) {
        $sql .= " AND b.status = ?";
        $params[] = $_GET['status'];
    }

    $sql .= " ORDER BY b.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $bookings]);
    exit;
}

if ($action === 'show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT b.*, CONCAT(u.firstname, ' ', u.lastname) as mentor_name, u.email as mentor_email, u.profile_picture as mentor_avatar, m.specialization, m.experience_years, m.rating, s.name as skill_name FROM bookings b JOIN users u ON b.mentor_id = u.id LEFT JOIN mentors m ON u.id = m.user_id LEFT JOIN skills s ON b.skill_id = s.id WHERE b.id = ?");
    $stmt->execute([$id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    if ($role === 'fresher' && $booking['fresher_id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }
    if ($role === 'mentor' && $booking['mentor_id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT s.id, s.session_title, s.session_date, s.session_time, s.duration, s.meeting_link, s.status, s.feedback_mentor, s.feedback_fresher FROM sessions s WHERE s.booking_id = ? ORDER BY s.created_at DESC");
    $stmt->execute([$id]);
    $booking['sessions'] = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $booking]);
    exit;
}

if ($action === 'create' && $_POST) {
    $mentorId = (int)($_POST['mentor_id'] ?? 0);
    $skillId = (int)($_POST['skill_id'] ?? 0);
    $courseId = (int)($_POST['course_id'] ?? 0);
    $sessionTitle = sanitize($_POST['session_title'] ?? 'Mentorship Session');
    $sessionDescription = sanitize($_POST['session_description'] ?? '');
    $sessionDate = sanitize($_POST['session_date'] ?? '');
    $sessionTime = sanitize($_POST['session_time'] ?? '');
    $duration = (int)($_POST['duration'] ?? 60);

    if (!$mentorId || !$sessionDate || !$sessionTime) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    if ($duration < 15 || $duration > 480) {
        echo json_encode(['success' => false, 'message' => 'Duration must be between 15 and 480 minutes']);
        exit;
    }

    $mentor = $pdo->prepare("SELECT hourly_rate, status FROM users WHERE id = ? AND role = 'mentor'");
    $mentor->execute([$mentorId]);
    $mentorData = $mentor->fetch();
    if (!$mentorData || $mentorData['status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Mentor not available']);
        exit;
    }

    $hourlyRate = (float)($mentorData['hourly_rate'] ?? 0);
    $totalAmount = round(($hourlyRate * $duration) / 60, 2);
    $bookingNumber = 'BK-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE mentor_id = ? AND session_date = ? AND status NOT IN ('cancelled','rejected') AND session_time < ADDTIME(?, SEC_TO_TIME(? * 60)) AND ADDTIME(session_time, SEC_TO_TIME(duration * 60)) > ?");
    $stmt->execute([$mentorId, $sessionDate, $sessionTime, $duration, $sessionTime]);
    $overlap = $stmt->fetchColumn();

    if ($overlap > 0) {
        echo json_encode(['success' => false, 'message' => 'This time slot is no longer available. Please choose another time.']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO bookings (booking_number, mentor_id, fresher_id, skill_id, session_title, session_description, session_date, session_time, duration, hourly_rate, total_amount, status, payment_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', NOW())");
        $stmt->execute([$bookingNumber, $mentorId, $userId, $skillId ?: null, $sessionTitle, $sessionDescription, $sessionDate, $sessionTime, $duration, $hourlyRate, $totalAmount]);

        $bookingId = (int)$pdo->lastInsertId();

        $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, link, is_read, created_at) VALUES (?, ?, ?, 'new_booking', ?, 0, NOW())")
            ->execute([$mentorId, 'New booking request', 'A fresher sent you a mentorship booking request.', 'dashboard/mentor/bookings.php']);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Booking created successfully', 'data' => ['booking_id' => $bookingId, 'booking_number' => $bookingNumber, 'amount' => $totalAmount, 'payment_status' => 'pending']]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to create booking: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'accept' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($role !== 'mentor') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, fresher_id, session_title, session_date, session_time, duration FROM bookings WHERE id = ? AND mentor_id = ? AND status = 'pending'");
    $stmt->execute([$id, $userId]);
    $booking = $stmt->fetch();

    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found or already processed']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        $pdo->prepare("UPDATE bookings SET status = 'confirmed', updated_at = NOW() WHERE id = ?")->execute([$id]);

        $stmt = $pdo->prepare("INSERT INTO sessions (booking_id, mentor_id, fresher_id, session_title, session_date, session_time, duration, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'scheduled', NOW())");
        $stmt->execute([$id, $userId, $booking['fresher_id'], $booking['session_title'], $booking['session_date'], $booking['session_time'], $booking['duration']]);

        $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, link, is_read, created_at) VALUES (?, ?, ?, 'booking_accepted', ?, 0, NOW())")
            ->execute([$booking['fresher_id'], 'Booking accepted', 'Your mentor accepted the booking request.', 'dashboard/fresher/bookings.php']);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Booking accepted']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to accept booking']);
    }
    exit;
}

if ($action === 'reject' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($role !== 'mentor') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    $reason = sanitize($_POST['reason'] ?? '');
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled', cancellation_reason = ?, cancelled_at = NOW(), cancelled_by = ? WHERE id = ? AND mentor_id = ? AND status = 'pending'");
    $stmt->execute([$reason, $userId, $id, $userId]);

    if ($stmt->rowCount() > 0) {
            $booking = $pdo->prepare("SELECT fresher_id FROM bookings WHERE id = ?");
        $booking->execute([$id]);
        $b = $booking->fetch();
        if ($b) {
            $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, link, is_read, created_at) VALUES (?, ?, ?, 'booking_rejected', ?, 0, NOW())")
                ->execute([$b['fresher_id'], 'Booking rejected', 'Your mentor rejected the booking request.', 'dashboard/fresher/bookings.php']);
        }
        echo json_encode(['success' => true, 'message' => 'Booking rejected']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found or already processed']);
    }
    exit;
}

if ($action === 'cancel' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $reason = sanitize($_POST['reason'] ?? '');
    $booking = $pdo->prepare("SELECT id, fresher_id, mentor_id, status FROM bookings WHERE id = ?");
    $booking->execute([$id]);
    $b = $booking->fetch();

    if (!$b) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    $allowed = false;
    if ($role === 'fresher' && $b['fresher_id'] == $userId && in_array($b['status'], ['pending','confirmed'])) {
        $allowed = true;
    } elseif ($role === 'mentor' && $b['mentor_id'] == $userId && in_array($b['status'], ['pending','confirmed'])) {
        $allowed = true;
    } elseif ($role === 'admin') {
        $allowed = true;
    }

    if (!$allowed) {
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        $pdo->prepare("UPDATE bookings SET status = 'cancelled', cancellation_reason = ?, cancelled_at = NOW(), cancelled_by = ? WHERE id = ?")->execute([$reason, $userId, $id]);
        $pdo->prepare("UPDATE sessions SET status = 'cancelled' WHERE booking_id = ? AND status IN ('scheduled','ongoing')")->execute([$id]);

        $notifyUserId = ($role === 'fresher') ? $b['mentor_id'] : $b['fresher_id'];
        $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, link, is_read, created_at) VALUES (?, ?, ?, 'booking_cancelled', ?, 0, NOW())")
            ->execute([$notifyUserId, 'Booking cancelled', 'A booking was cancelled.', 'dashboard/' . ($role === 'fresher' ? 'mentor' : 'fresher') . '/bookings.php']);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Booking cancelled']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
