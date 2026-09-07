<?php
$page_title = 'Mark Lesson Incomplete';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();
$lesson_id = isset($_GET['lesson']) ? (int)$_GET['lesson'] : 0;

if (!$lesson_id) {
    redirect('fresher/learning/my-course.php');
}

$stmt = $pdo->prepare("SELECT lp.*, cl.title as lesson_title, cm.course_id 
                       FROM lesson_progress lp 
                       JOIN course_lessons cl ON lp.lesson_id = cl.id 
                       JOIN course_modules cm ON cl.module_id = cm.id 
                       WHERE lp.fresher_id = ? AND lp.lesson_id = ? AND lp.completed = 1");
$stmt->execute([$user_id, $lesson_id]);
$progress = $stmt->fetch();

if (!$progress) {
    redirect('fresher/learning/my-course.php');
}

$stmt = $pdo->prepare("UPDATE lesson_progress SET completed = 0, completed_at = NULL, updated_at = NOW() 
                       WHERE fresher_id = ? AND lesson_id = ?");
$stmt->execute([$user_id, $lesson_id]);

$_SESSION['alert'] = [
    'type' => 'success',
    'icon' => 'check-circle',
    'message' => 'Lesson marked as incomplete.'
];

redirect('fresher/learning/my-course.php');
