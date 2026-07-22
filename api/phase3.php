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
    'list-fields',
    'list-courses',
    'list-courses-by-field',
    'list-assignments',
    'list-practical-skills',
    'list-assignments-by-course',
    'list-assignments-by-field',
    'list-mentors-by-practical-skill'
];

if (!in_array($action, $allowed, true)) {
    json_response('error', 'Invalid or missing action', [], 400);
}

if (!is_logged_in()) {
    json_response('error', 'Unauthorized', [], 401);
}

$uid = (int)($_SESSION['user_id'] ?? 0);

// Phase 3 endpoints are role-agnostic for now (can be restricted later).
if (in_array($action, ['list-fields'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }
    $data = get_fields();
    json_response('success', 'Fields fetched', ['fields' => $data]);
}

if (in_array($action, ['list-courses'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }
    $data = get_courses();
    json_response('success', 'Courses fetched', ['courses' => $data]);
}

if (in_array($action, ['list-courses-by-field'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }
    $fieldId = (int)($_GET['field_id'] ?? 0);
    $data = get_courses_by_field($fieldId);
    json_response('success', 'Courses fetched by field', ['courses' => $data]);
}

if (in_array($action, ['list-assignments'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }
    $data = get_assignments();
    json_response('success', 'Assignments fetched', ['assignments' => $data]);
}

if (in_array($action, ['list-assignments-by-course'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }
    $courseId = (int)($_GET['course_id'] ?? 0);
    $data = get_assignments_by_course($courseId);
    json_response('success', 'Assignments fetched by course', ['assignments' => $data]);
}

if (in_array($action, ['list-assignments-by-field'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }
    $fieldId = (int)($_GET['field_id'] ?? 0);
    $data = get_assignments_by_field($fieldId);
    json_response('success', 'Assignments fetched by field', ['assignments' => $data]);
}

if (in_array($action, ['list-practical-skills'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }
    $data = get_practical_skills();
    json_response('success', 'Practical skills fetched', ['practical_skills' => $data]);
}

if (in_array($action, ['list-mentors-by-practical-skill'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_response('error', 'Method not allowed', [], 405);
    }
    $skillId = (int)($_GET['skill_id'] ?? 0);
    $data = get_mentors_by_practical_skill($skillId);
    json_response('success', 'Mentors fetched by practical skill', ['mentors' => $data]);
}


json_response('error', 'Unhandled action', [], 400);

