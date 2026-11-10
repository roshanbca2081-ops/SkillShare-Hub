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

$course_id = (int)$progress['course_id'];
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_lessons,
                              COALESCE(SUM(CASE WHEN lp.completed = 1 THEN 1 ELSE 0 END), 0) AS completed_lessons
                       FROM course_modules cm
                       JOIN course_lessons cl ON cl.module_id = cm.id AND cl.is_published = 1
                       LEFT JOIN lesson_progress lp ON lp.lesson_id = cl.id AND lp.fresher_id = ?
                       WHERE cm.course_id = ? AND cm.is_published = 1");
$stmt->execute([$user_id, $course_id]);
$lessonCounts = $stmt->fetch();
$totalLessons = (int)$lessonCounts['total_lessons'];
$completedLessons = (int)$lessonCounts['completed_lessons'];
$progressPercent = $totalLessons > 0 ? (int)round(($completedLessons / $totalLessons) * 100) : 0;

$stmt = $pdo->prepare("UPDATE enrollments
                       SET progress = ?,
                           completed_at = CASE WHEN status = 'completed' THEN NULL ELSE completed_at END,
                           status = CASE WHEN status = 'completed' THEN 'active' ELSE status END,
                           last_accessed_at = NOW()
                       WHERE fresher_id = ? AND course_id = ?");
$stmt->execute([$progressPercent, $user_id, $course_id]);

$_SESSION['alert'] = [
    'type' => 'success',
    'icon' => 'check-circle',
    'message' => 'Lesson marked as incomplete.'
];

redirect('fresher/learning/course.php?id=' . $course_id);
