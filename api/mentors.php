<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=UTF-8');

$pdo = getDB();

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $sql = "SELECT u.id, u.full_name, u.email, u.profile_picture, u.bio, u.hourly_rate, u.academic_field_id, m.specialization, m.experience_years, m.current_company, m.current_position, m.qualification, m.is_verified, m.rating, m.reviews_count, m.total_sessions, m.total_students, m.total_hours, f.name as field_name, f.icon as field_icon, f.color as field_color FROM users u JOIN mentors m ON u.id = m.user_id LEFT JOIN academic_fields f ON u.academic_field_id = f.id WHERE u.role = 'mentor' AND u.status = 'active' AND m.is_verified = 1";
    $params = [];

    if (isset($_GET['field_id']) && $_GET['field_id']) {
        $sql .= " AND u.academic_field_id = ?";
        $params[] = (int)$_GET['field_id'];
    }
    if (isset($_GET['search']) && $_GET['search']) {
        $sql .= " AND (u.full_name LIKE ? OR u.bio LIKE ? OR m.specialization LIKE ?)";
        $params[] = '%' . $_GET['search'] . '%';
        $params[] = '%' . $_GET['search'] . '%';
        $params[] = '%' . $_GET['search'] . '%';
    }

    $sql .= " ORDER BY m.rating DESC, m.reviews_count DESC LIMIT 50";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $mentors = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT id, name, slug, icon, color FROM academic_fields WHERE status = 'active' ORDER BY sort_order");
    $fields = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $mentors, 'fields' => $fields]);
    exit;
}

if ($action === 'show' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.profile_picture, u.bio, u.hourly_rate, u.address, u.city, u.state, u.country, u.date_of_birth, u.gender, u.created_at as member_since, m.specialization, m.experience_years, m.current_company, m.current_position, m.qualification, m.certifications, m.languages, m.is_verified, m.verified_at, m.rating, m.reviews_count, m.total_sessions, m.total_students, m.total_hours, m.portfolio_url, m.linkedin_url, m.github_url, m.website_url, m.youtube_url, f.name as field_name, f.icon as field_icon, f.color as field_color FROM users u JOIN mentors m ON u.id = m.user_id LEFT JOIN academic_fields f ON u.academic_field_id = f.id WHERE u.id = ? AND u.role = 'mentor' AND u.status = 'active'");
    $stmt->execute([$id]);
    $mentor = $stmt->fetch();

    if (!$mentor) {
        echo json_encode(['success' => false, 'message' => 'Mentor not found']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT r.rating, r.comment, r.created_at, u.full_name, u.profile_picture FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewee_id = ? AND r.is_public = 1 ORDER BY r.created_at DESC LIMIT 10");
    $stmt->execute([$id]);
    $mentor['reviews'] = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM availability WHERE user_id = ? AND is_available = 1 ORDER BY FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')");
    $stmt->execute([$id]);
    $mentor['availability'] = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT c.id, c.name, c.slug, c.description, c.duration, c.level, c.rating FROM courses c WHERE c.academic_field_id = ? AND c.status = 'active' ORDER BY c.name LIMIT 10");
    $stmt->execute([$mentor['academic_field_id']]);
    $mentor['courses'] = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM skills s JOIN skill_learners sl ON s.id = sl.skill_id WHERE sl.user_id = ? AND sl.status = 'completed' ORDER BY s.name");
    $stmt->execute([$id]);
    $mentor['skills'] = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $mentor]);
    exit;
}

if ($action === 'search') {
    $search = $_GET['q'] ?? '';
    $stmt = $pdo->prepare("SELECT u.id, u.full_name, u.profile_picture, u.hourly_rate, m.rating, m.reviews_count, m.specialization FROM users u JOIN mentors m ON u.id = m.user_id WHERE u.role = 'mentor' AND u.status = 'active' AND m.is_verified = 1 AND (u.full_name LIKE ? OR m.specialization LIKE ?) ORDER BY m.rating DESC LIMIT 20");
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    $mentors = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $mentors]);
    exit;
}
