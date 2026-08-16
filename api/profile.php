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

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $user = getCurrentUser();
    $field = null;
    if ($user['academic_field_id']) {
        $stmt = $pdo->prepare("SELECT name FROM academic_fields WHERE id = ?");
        $stmt->execute([$user['academic_field_id']]);
        $field = $stmt->fetchColumn();
    }
    $course = null;
    if ($user['course_id']) {
        $stmt = $pdo->prepare("SELECT name FROM courses WHERE id = ?");
        $stmt->execute([$user['course_id']]);
        $course = $stmt->fetchColumn();
    }

    $extra = [];
    if ($role === 'mentor') {
        $stmt = $pdo->prepare("SELECT * FROM mentors WHERE user_id = ?");
        $stmt->execute([$userId]);
        $m = $stmt->fetch();
        $extra = $m ?: [];
        $stmt = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM availability WHERE user_id = ? AND is_available = 1 ORDER BY FIELD(day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday','sunday')");
        $stmt->execute([$userId]);
        $extra['availability'] = $stmt->fetchAll();
    } elseif ($role === 'fresher') {
        $stmt = $pdo->prepare("SELECT * FROM freshers WHERE user_id = ?");
        $stmt->execute([$userId]);
        $f = $stmt->fetch();
        $extra = $f ?: [];
    }

    echo json_encode(['success' => true, 'data' => array_merge((array)$user, $extra, ['field_name' => $field, 'course_name' => $course])]);
    exit;
}

if ($method === 'POST') {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $bio = sanitize($_POST['bio'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $country = sanitize($_POST['country'] ?? '');
    $postalCode = sanitize($_POST['postal_code'] ?? '');
    $academicFieldId = (int)($_POST['academic_field_id'] ?? 0);
    $courseId = (int)($_POST['course_id'] ?? 0);

    $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, bio = ?, address = ?, city = ?, state = ?, country = ?, postal_code = ?, academic_field_id = ?, course_id = ?, updated_at = NOW() WHERE id = ?")->execute([
        $fullName, $phone, $bio, $address, $city, $state, $country, $postalCode, $academicFieldId ?: null, $courseId ?: null, $userId
    ]);

    if ($role === 'mentor') {
        $data = [
            'specialization' => sanitize($_POST['specialization'] ?? ''),
            'experience_years' => (int)($_POST['experience_years'] ?? 0),
            'current_company' => sanitize($_POST['current_company'] ?? ''),
            'current_position' => sanitize($_POST['current_position'] ?? ''),
            'qualification' => sanitize($_POST['qualification'] ?? ''),
            'certifications' => sanitize($_POST['certifications'] ?? ''),
            'languages' => sanitize($_POST['languages'] ?? ''),
            'expertise_areas' => sanitize($_POST['expertise_areas'] ?? ''),
            'portfolio_url' => sanitize($_POST['portfolio_url'] ?? ''),
            'linkedin_url' => sanitize($_POST['linkedin_url'] ?? ''),
            'github_url' => sanitize($_POST['github_url'] ?? ''),
            'website_url' => sanitize($_POST['website_url'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $setParts = [];
        $params = [];
        foreach ($data as $k => $v) { $setParts[] = "$k = ?"; $params[] = $v; }
        $params[] = $userId;
        $sql = "UPDATE mentors SET " . implode(', ', $setParts) . " WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if (isset($_POST['availability']) && is_array($_POST['availability'])) {
            $pdo->exec("DELETE FROM availability WHERE user_id = $userId");
            foreach ($_POST['availability'] as $day => $avail) {
                $start = $avail['start'] ?? '';
                $end = $avail['end'] ?? '';
                $isAvail = $avail['available'] ? 1 : 0;
                if ($start && $end) {
                    $pdo->prepare("INSERT INTO availability (user_id, day_of_week, start_time, end_time, is_available, created_at) VALUES (?, ?, ?, ?, ?, NOW())")->execute([$userId, $day, $start, $end, $isAvail]);
                }
            }
        }
    } elseif ($role === 'fresher') {
        $data = [
            'education_level' => sanitize($_POST['education_level'] ?? ''),
            'institution' => sanitize($_POST['institution'] ?? ''),
            'graduation_year' => (int)($_POST['graduation_year'] ?? 0),
            'current_semester' => (int)($_POST['current_semester'] ?? 0),
            'interests' => sanitize($_POST['interests'] ?? ''),
            'goals' => sanitize($_POST['goals'] ?? ''),
            'skills_learned' => sanitize($_POST['skills_learned'] ?? ''),
            'preferred_learning_style' => sanitize($_POST['preferred_learning_style'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $setParts = [];
        $params = [];
        foreach ($data as $k => $v) { $setParts[] = "$k = ?"; $params[] = $v; }
        $params[] = $userId;
        $sql = "INSERT INTO freshers (" . implode(', ', array_keys($data)) . ", user_id) VALUES (" . str_repeat('?, ', count($data)) . "?) ON DUPLICATE KEY UPDATE " . implode(', ', array_map(fn($k) => "$k = VALUES($k)", array_keys($data)));
        $stmt = $pdo->prepare($sql);
        $allParams = array_values($data);
        $allParams[] = $userId;
        $stmt->execute($allParams);
    }

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['profile_picture']['tmp_name']);
        finfo_close($finfo);
        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/profiles/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadDir . $filename)) {
                $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?")->execute([$filename, $userId]);
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid method']);
