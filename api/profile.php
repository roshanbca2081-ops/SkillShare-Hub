<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, PUT, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

$allowed = [
    'get-fresher-profile','update-fresher-profile',
    'get-graduate-profile','update-graduate-profile'
];

if (!in_array($action, $allowed, true)) {
    json_response('error', 'Invalid or missing action', [], 400);
}

if (!is_logged_in()) {
    json_response('error', 'Unauthorized', [], 401);
}

if ($action === 'get-fresher-profile' || $action === 'update-fresher-profile') {
    if (current_user_role() !== 'fresher') {
        json_response('error', 'Forbidden for current role', [], 403);
    }
}

if ($action === 'get-graduate-profile' || $action === 'update-graduate-profile') {
    if (current_user_role() !== 'graduate') {
        json_response('error', 'Forbidden for current role', [], 403);
    }
}

$uid = (int)($_SESSION['user_id'] ?? 0);

function require_body_fields($arr, $fields) {
    foreach ($fields as $f) {
        if (!isset($arr[$f]) || trim((string)$arr[$f]) === '') {
            json_response('error', "$f is required", [], 422);
        }
    }
}

if ($action === 'get-fresher-profile') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }

    global $conn;
    $stmt = $conn->prepare("SELECT p.*, f.academic_field, f.course, f.semester, f.resume_url, f.portfolio_url, f.interests
                            FROM user_profiles p
                            LEFT JOIN fresher_profiles f ON f.user_id = p.user_id
                            WHERE p.user_id = ? LIMIT 1");
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        // return defaults
        json_response('success', 'Profile not set yet', [
            'user_id' => $uid,
            'academic_field' => null,
            'course' => null,
            'semester' => null,
            'resume_url' => null,
            'portfolio_url' => null,
            'interests' => null
        ]);
    }

    json_response('success', 'Fetched fresher profile', $row);
}

if ($action === 'update-fresher-profile') {
    if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT','POST'], true)) {
        json_response('error', 'Method not allowed', [], 405);
    }

    $input = get_json_input();

    require_body_fields($input, ['academic_field','course','semester']);

    global $conn;

    // Upsert base profile
    $stmtBase = $conn->prepare("INSERT INTO user_profiles (user_id) VALUES (?) ON DUPLICATE KEY UPDATE user_id = user_id");
    $stmtBase->bind_param('i', $uid);
    $stmtBase->execute();
    $stmtBase->close();


    $academicField = (string)$input['academic_field'];
    $course = (string)$input['course'];
    $semester = (string)$input['semester'];
    $resumeUrl = isset($input['resume_url']) ? (string)$input['resume_url'] : null;
    $portfolioUrl = isset($input['portfolio_url']) ? (string)$input['portfolio_url'] : null;
    $interests = isset($input['interests']) ? (string)$input['interests'] : null;

    $stmt = $conn->prepare("INSERT INTO fresher_profiles (user_id, academic_field, course, semester, resume_url, portfolio_url, interests)
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE
                                academic_field = VALUES(academic_field),
                                course = VALUES(course),
                                semester = VALUES(semester),
                                resume_url = VALUES(resume_url),
                                portfolio_url = VALUES(portfolio_url),
                                interests = VALUES(interests)");

    $stmt->bind_param('issssss', $uid, $academicField, $course, $semester, $resumeUrl, $portfolioUrl, $interests);
    $stmt->execute();
    $stmt->close();

    json_response('success', 'Fresher profile updated');
}

if ($action === 'get-graduate-profile') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }

    global $conn;
    $stmt = $conn->prepare("SELECT p.*, g.experience, g.company, g.skills, g.biography, g.hourly_rate,
                                    g.available_time, g.rating, g.certificates
                            FROM user_profiles p
                            LEFT JOIN graduate_profiles g ON g.user_id = p.user_id
                            WHERE p.user_id = ? LIMIT 1");
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        json_response('success', 'Profile not set yet', [
            'user_id' => $uid,
            'experience' => null,
            'company' => null,
            'skills' => null,
            'biography' => null,
            'hourly_rate' => null,
            'available_time' => null,
            'rating' => null,
            'certificates' => null
        ]);
    }

    json_response('success', 'Fetched graduate profile', $row);
}

if ($action === 'update-graduate-profile') {
    if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT','POST'], true)) {
        json_response('error', 'Method not allowed', [], 405);
    }

    $input = get_json_input();

    require_body_fields($input, ['experience','company','hourly_rate']);

    global $conn;

    $stmtBase = $conn->prepare("INSERT INTO user_profiles (user_id) VALUES (?) ON DUPLICATE KEY UPDATE user_id = user_id");
    $stmtBase->bind_param('i', $uid);
    $stmtBase->execute();
    $stmtBase->close();


    $experience = (string)$input['experience'];
    $company = (string)$input['company'];
    $skills = isset($input['skills']) ? (string)$input['skills'] : null;
    $biography = isset($input['biography']) ? (string)$input['biography'] : null;
    $hourlyRate = (string)$input['hourly_rate'];
    $availableTime = isset($input['available_time']) ? (string)$input['available_time'] : null;
    $rating = isset($input['rating']) ? (string)$input['rating'] : null;
    $certificates = isset($input['certificates']) ? (string)$input['certificates'] : null;

    $stmt = $conn->prepare("INSERT INTO graduate_profiles (user_id, experience, company, skills, biography, hourly_rate, available_time, rating, certificates)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE
                                experience = VALUES(experience),
                                company = VALUES(company),
                                skills = VALUES(skills),
                                biography = VALUES(biography),
                                hourly_rate = VALUES(hourly_rate),
                                available_time = VALUES(available_time),
                                rating = VALUES(rating),
                                certificates = VALUES(certificates)");

    // All fields are treated as strings for safe binding; MySQL will coerce hourly_rate/decimals as needed.
    $stmt->bind_param('issssssss', $uid, $experience, $company, $skills, $biography, $hourlyRate, $availableTime, $rating, $certificates);
    $stmt->execute();
    $stmt->close();

    json_response('success', 'Graduate profile updated');
}

json_response('error', 'Unhandled action', [], 400);

