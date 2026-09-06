<?php
// This file is similar to course.php but focused on a single lesson
// It can be used for direct lesson access

$page_title = 'Lesson';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$lesson_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get lesson details
$stmt = $pdo->prepare("SELECT cl.*, cm.course_id, c.title as course_title 
                       FROM course_lessons cl 
                       JOIN course_modules cm ON cl.module_id = cm.id 
                       JOIN courses c ON cm.course_id = c.id 
                       WHERE cl.id = ? AND cl.is_published = 1");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch();

if (!$lesson) {
    redirect('../dashboard.php');
}

// Check enrollment
$user_id = getUserId();
$stmt = $pdo->prepare("SELECT * FROM enrollments WHERE fresher_id = ? AND course_id = ? AND status != 'dropped'");
$stmt->execute([$user_id, $lesson['course_id']]);
$enrollment = $stmt->fetch();

if (!$enrollment) {
    redirect('my-courses.php');
}

// Redirect to course page with lesson parameter
redirect('course.php?id=' . $lesson['course_id'] . '&lesson=' . $lesson_id);
?>