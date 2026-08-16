<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

$pdo = getDB();

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $sql = "SELECT c.id, c.name, c.slug, c.description, c.duration, c.level, c.rating, c.total_mentors, c.total_skills, c.status, f.name as field_name, f.slug as field_slug, f.icon as field_icon, f.color as field_color FROM courses c JOIN academic_fields f ON c.academic_field_id = f.id WHERE c.status = 'active'";
    $params = [];

    if (isset($_GET['field_id']) && $_GET['field_id']) {
        $sql .= " AND c.academic_field_id = ?";
        $params[] = (int)$_GET['field_id'];
    }
    if (isset($_GET['search']) && $_GET['search']) {
        $sql .= " AND (c.name LIKE ? OR c.description LIKE ?)";
        $params[] = '%' . $_GET['search'] . '%';
        $params[] = '%' . $_GET['search'] . '%';
    }

    $sql .= " ORDER BY c.name LIMIT 50";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $courses]);
    exit;
}

if ($action === 'show' && isset($_GET['slug'])) {
    $stmt = $pdo->prepare("SELECT c.*, f.name as field_name, f.slug as field_slug, f.icon as field_icon, f.color as field_color FROM courses c JOIN academic_fields f ON c.academic_field_id = f.id WHERE c.slug = ? AND c.status = 'active'");
    $stmt->execute([$_GET['slug']]);
    $course = $stmt->fetch();

    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Course not found']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, name, slug, description, icon, category FROM skills WHERE course_id = ? AND status = 'active' ORDER BY name");
    $stmt->execute([$course['id']]);
    $course['skills'] = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT u.id, u.full_name, u.profile_picture, u.bio, u.hourly_rate, m.rating, m.reviews_count, m.total_sessions, m.total_students, m.experience_years, m.specialization FROM users u JOIN mentors m ON u.id = m.user_id WHERE u.role = 'mentor' AND u.status = 'active' AND u.course_id = ? ORDER BY m.rating DESC LIMIT 10");
    $stmt->execute([$course['id']]);
    $course['mentors'] = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $course]);
    exit;
}

if ($action === 'enroll' && isLoggedIn()) {
    $courseId = (int)($_POST['course_id'] ?? 0);
    $userId = getUserId();

    if (!$courseId) {
        echo json_encode(['success' => false, 'message' => 'Invalid course']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM course_enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$userId, $courseId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Already enrolled']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO course_enrollments (user_id, course_id, enrollment_date, status) VALUES (?, ?, CURDATE(), 'active')");
    $stmt->execute([$userId, $courseId]);

    echo json_encode(['success' => true, 'message' => 'Enrolled successfully', 'data' => ['enrollment_id' => $pdo->lastInsertId()]]);
    exit;
}
