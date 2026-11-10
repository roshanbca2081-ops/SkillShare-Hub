<?php
$page_title = 'Course Learning';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check enrollment
$user_id = getUserId();
$stmt = $pdo->prepare("SELECT * FROM enrollments WHERE fresher_id = ? AND course_id = ? AND status != 'dropped'");
$stmt->execute([$user_id, $course_id]);
$enrollment = $stmt->fetch();

if (!$enrollment) {
    redirect('my-course.php');
}

// Get course details
$stmt = $pdo->prepare("SELECT c.*, u.full_name as mentor_name, u.avatar 
                       FROM courses c 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE c.id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    redirect('my-course.php');
}

// Published lesson sequence is enforced on the server, including direct lesson URLs.
$stmt = $pdo->prepare("SELECT cm.id AS module_id, cm.title AS module_title,
                              l.*, COALESCE(lp.completed, 0) AS saved_completed,
                              COALESCE((SELECT COUNT(*) FROM lesson_quiz_questions q WHERE q.lesson_id = l.id), 0) AS quiz_question_count,
                              EXISTS(SELECT 1 FROM lesson_quiz_attempts qa
                                     WHERE qa.fresher_id = ? AND qa.lesson_id = l.id
                                       AND qa.quiz_version = l.quiz_version AND qa.passed = 1) AS quiz_passed
                       FROM course_modules cm
                       JOIN course_lessons l ON l.module_id = cm.id AND l.is_published = 1
                       LEFT JOIN lesson_progress lp ON lp.lesson_id = l.id AND lp.fresher_id = ?
                       WHERE cm.course_id = ? AND cm.is_published = 1
                       ORDER BY cm.order_number, cm.id, l.order_number, l.id");
$stmt->execute([$user_id, $user_id, $course_id]);
$all_lessons = $stmt->fetchAll();
$modules = [];
$lessons = [];
$lessonIndexes = [];
$first_lesson = null;
foreach ($all_lessons as $index => &$lesson) {
    $lesson['completed'] = (int)$lesson['saved_completed'];
    $lesson['quiz_required'] = (int)($lesson['quiz_required'] ?? 0);
    $lesson['quiz_pass_percent'] = (int)($lesson['quiz_pass_percent'] ?? 70);
    $lesson['quiz_version'] = (int)($lesson['quiz_version'] ?? 1);
    $lesson['quiz_passed'] = (int)$lesson['quiz_passed'];
    if ($lesson['quiz_required'] && ($lesson['quiz_question_count'] < 1 || !$lesson['quiz_passed'])) {
        $lesson['completed'] = 0;
    }
    if (!isset($modules[$lesson['module_id']])) {
        $modules[$lesson['module_id']] = ['id' => $lesson['module_id'], 'title' => $lesson['module_title']];
    }
    $lessons[$lesson['module_id']][] = $lesson;
    $lessonIndexes[(int)$lesson['id']] = $index;
    if ($first_lesson === null) {
        $first_lesson = $lesson;
    }
}
unset($lesson);
$modules = array_values($modules);

// Keep learners on the first unfinished lesson across their enrolled course sequence.
$pendingLessonQuery = $pdo->prepare("SELECT c.id AS course_id, l.id AS lesson_id
                                   FROM enrollments e
                                   JOIN courses c ON c.id = e.course_id
                                   JOIN course_modules cm ON cm.course_id = c.id AND cm.is_published = 1
                                                                     JOIN course_lessons l ON l.module_id = cm.id AND l.is_published = 1
                                                                     LEFT JOIN lesson_progress lp ON lp.fresher_id = e.fresher_id AND lp.lesson_id = l.id
                                                                     WHERE e.fresher_id = ? AND e.status IN ('active', 'completed')
                                                                         AND (COALESCE(lp.completed, 0) = 0 OR (l.quiz_required = 1 AND NOT EXISTS (
                                                                                 SELECT 1 FROM lesson_quiz_attempts qa
                                                                                 WHERE qa.fresher_id = e.fresher_id AND qa.lesson_id = l.id
                                                                                     AND qa.quiz_version = l.quiz_version AND qa.passed = 1
                                                                         )))
                                   ORDER BY e.id, cm.order_number, cm.id, l.order_number, l.id
                                   LIMIT 1");
$pendingLessonQuery->execute([$user_id]);
$pendingLesson = $pendingLessonQuery->fetch();
if ($pendingLesson && (int)$pendingLesson['course_id'] !== $course_id) {
        redirect('course.php?id=' . (int)$pendingLesson['course_id'] . '&lesson=' . (int)$pendingLesson['lesson_id']);
}

// Requested lessons are only available once all earlier lessons are complete.
$requested_lesson_id = isset($_GET['lesson']) ? (int)$_GET['lesson'] : ($first_lesson ? (int)$first_lesson['id'] : 0);
$requested_index = $requested_lesson_id && isset($lessonIndexes[$requested_lesson_id]) ? $lessonIndexes[$requested_lesson_id] : 0;
$first_unfinished_index = null;
foreach ($all_lessons as $index => $lesson) {
    if (empty($lesson['completed'])) {
        $first_unfinished_index = $index;
        break;
    }
}
if ($requested_lesson_id && isset($lessonIndexes[$requested_lesson_id]) && $first_unfinished_index !== null && $requested_index > $first_unfinished_index) {
    $lockedLesson = $all_lessons[$first_unfinished_index];
    $_SESSION['alert'] = ['type' => 'warning', 'icon' => 'lock', 'message' => 'Complete this lesson and pass its required quiz before moving on.'];
    redirect('course.php?id=' . $course_id . '&lesson=' . (int)$lockedLesson['id']);
}

$lesson_id = $requested_lesson_id;
$current_lesson = $lesson_id && isset($lessonIndexes[$lesson_id]) ? $all_lessons[$lessonIndexes[$lesson_id]] : null;
$quiz_questions = [];
$latestAttempt = null;
if ($current_lesson && $current_lesson['quiz_required']) {
    $stmt = $pdo->prepare('SELECT id, question_text, option_a, option_b, option_c, option_d FROM lesson_quiz_questions WHERE lesson_id = ? ORDER BY order_number, id');
    $stmt->execute([$lesson_id]);
    $quiz_questions = $stmt->fetchAll();
    $stmt = $pdo->prepare('SELECT score_percent, passed, attempted_at FROM lesson_quiz_attempts WHERE fresher_id = ? AND lesson_id = ? AND quiz_version = ? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$user_id, $lesson_id, $current_lesson['quiz_version']]);
    $latestAttempt = $stmt->fetch();
}

$recalculateEnrollment = function () use ($pdo, $user_id, $course_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_lessons,
                                  COALESCE(SUM(CASE WHEN lp.completed = 1 THEN 1 ELSE 0 END), 0) AS completed_lessons
                           FROM course_modules cm
                           JOIN course_lessons cl ON cl.module_id = cm.id AND cl.is_published = 1
                           LEFT JOIN lesson_progress lp ON lp.lesson_id = cl.id AND lp.fresher_id = ?
                           WHERE cm.course_id = ? AND cm.is_published = 1");
    $stmt->execute([$user_id, $course_id]);
    $counts = $stmt->fetch();
    $total = (int)$counts['total_lessons'];
    $completed = (int)$counts['completed_lessons'];
    $percent = $total > 0 ? (int)round(($completed / $total) * 100) : 0;
    $isComplete = $total > 0 && $completed >= $total;
    $stmt = $pdo->prepare("UPDATE enrollments
                           SET progress = ?,
                               status = CASE WHEN ? = 1 THEN 'completed' WHEN status = 'completed' THEN 'active' ELSE status END,
                               completed_at = CASE WHEN ? = 1 THEN COALESCE(completed_at, NOW()) WHEN ? = 0 THEN NULL ELSE completed_at END,
                               last_accessed_at = NOW()
                           WHERE fresher_id = ? AND course_id = ?");
    $stmt->execute([$percent, $isComplete ? 1 : 0, $isComplete ? 1 : 0, $isComplete ? 1 : 0, $user_id, $course_id]);
    if ($isComplete) {
        try {
            ensureCourseCertificate($pdo, $user_id, $course_id);
        } catch (Throwable $e) {
            error_log('Course certificate issuance failed: ' . $e->getMessage());
        }
    }
    return ['progress' => $percent, 'complete' => $isComplete];
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['submit_quiz']) || isset($_POST['complete_lesson']))) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'Your session token expired. Please reload and try again.'];
        redirect('course.php?id=' . $course_id . '&lesson=' . $lesson_id);
    }

    $postedLessonId = (int)($_POST['lesson_id'] ?? 0);
    if (!$current_lesson || $postedLessonId !== (int)$current_lesson['id']) {
        redirect('course.php?id=' . $course_id);
    }

    if (isset($_POST['submit_quiz'])) {
        $questionStmt = $pdo->prepare('SELECT id, correct_option FROM lesson_quiz_questions WHERE lesson_id = ? ORDER BY order_number, id');
        $questionStmt->execute([$lesson_id]);
        $answerKey = $questionStmt->fetchAll();
        $submittedAnswers = $_POST['answers'] ?? [];
        $isValidAttempt = $answerKey && is_array($submittedAnswers) && count($answerKey) === count($submittedAnswers);
        $correctCount = 0;

        foreach ($answerKey as $question) {
            $questionKey = (string)$question['id'];
            $answer = isset($submittedAnswers[$questionKey]) ? strtoupper((string)$submittedAnswers[$questionKey]) : '';
            if (!in_array($answer, ['A', 'B', 'C', 'D'], true)) {
                $isValidAttempt = false;
            } elseif ($answer === $question['correct_option']) {
                $correctCount++;
            }
        }

        if (!$isValidAttempt) {
            $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'Answer every quiz question before submitting.'];
        } else {
            $score = (int)round(($correctCount / count($answerKey)) * 100);
            $passed = $score >= $current_lesson['quiz_pass_percent'];
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare('INSERT INTO lesson_quiz_attempts (fresher_id, lesson_id, quiz_version, score_percent, passed) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$user_id, $lesson_id, $current_lesson['quiz_version'], $score, $passed ? 1 : 0]);
                if ($passed) {
                    $stmt = $pdo->prepare('INSERT INTO lesson_progress (fresher_id, lesson_id, completed, completed_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()');
                    $stmt->execute([$user_id, $lesson_id]);
                }
                $pdo->commit();
            } catch (Throwable $e) {
                $pdo->rollBack();
                error_log('Lesson quiz submission failed: ' . $e->getMessage());
                $_SESSION['alert'] = ['type' => 'danger', 'icon' => 'exclamation-circle', 'message' => 'We could not save your quiz attempt. Please try again.'];
                redirect('course.php?id=' . $course_id . '&lesson=' . $lesson_id);
            }

            $courseProgress = $recalculateEnrollment();
            $_SESSION['alert'] = $passed
                ? ['type' => 'success', 'icon' => 'check-circle', 'message' => 'You passed with ' . $score . '%. Lesson completed!']
                : ['type' => 'warning', 'icon' => 'redo', 'message' => 'You scored ' . $score . '%. You need ' . $current_lesson['quiz_pass_percent'] . '% to pass. Review the lesson and try again.'];
        }
        redirect('course.php?id=' . $course_id . '&lesson=' . $lesson_id);
    }

    if ($current_lesson['quiz_required'] && !$current_lesson['quiz_passed']) {
        $_SESSION['alert'] = ['type' => 'warning', 'icon' => 'lock', 'message' => 'Pass the lesson quiz before marking this lesson complete.'];
        redirect('course.php?id=' . $course_id . '&lesson=' . $lesson_id);
    }

    $stmt = $pdo->prepare('INSERT INTO lesson_progress (fresher_id, lesson_id, completed, completed_at) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()');
    $stmt->execute([$user_id, $lesson_id]);
    $courseProgress = $recalculateEnrollment();
    $_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Lesson marked as complete!'];
    redirect('course.php?id=' . $course_id . '&lesson=' . $lesson_id);
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?php echo htmlspecialchars($course['title']); ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="my-course.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Courses
                    </a>
                </div>
            </div>
            
            <div class="row">
                <!-- Sidebar - Course Content -->
                <div class="col-lg-3 order-lg-1 order-2">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                        <div class="card-body p-3">
                            <h6 class="mb-3">Course Content</h6>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar" style="width: <?php echo $enrollment['progress'] ?? 0; ?>%"></div>
                            </div>
                            <small class="text-muted d-block mb-3"><?php echo $enrollment['progress'] ?? 0; ?>% complete</small>
                            
                            <?php foreach ($modules as $module): ?>
                            <div class="module-group mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="small"><?php echo htmlspecialchars($module['title']); ?></strong>
                                    <span class="badge bg-secondary"><?php echo count($lessons[$module['id']] ?? []); ?></span>
                                </div>
                                <?php if (!empty($lessons[$module['id']])): ?>
                                    <?php foreach ($lessons[$module['id']] as $lesson): ?>
                                    <a href="course.php?id=<?php echo $course_id; ?>&lesson=<?php echo $lesson['id']; ?>" 
                                       class="d-flex align-items-center text-decoration-none p-1 rounded <?php echo ($current_lesson && $current_lesson['id'] == $lesson['id']) ? 'bg-primary bg-opacity-10' : ''; ?>">
                                        <span class="me-2">
                                            <?php if ($lesson['completed']): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php else: ?>
                                                <i class="fas fa-circle text-muted" style="font-size: 8px;"></i>
                                            <?php endif; ?>
                                        </span>
                                        <span class="small text-truncate <?php echo ($current_lesson && $current_lesson['id'] == $lesson['id']) ? 'text-primary' : 'text-muted'; ?>">
                                            <?php echo htmlspecialchars($lesson['title']); ?>
                                        </span>
                                    </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="col-lg-9 order-lg-2 order-1">
                    <?php if ($current_lesson): ?>
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <h3><?php echo htmlspecialchars($current_lesson['title']); ?></h3>
                                
                                <?php if ($current_lesson['video_url']): ?>
                                <div class="ratio ratio-16x9 mb-4">
                                    <video controls>
                                        <source src="<?php echo $current_lesson['video_url']; ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <?php endif; ?>
                                
                                <div class="lesson-content">
                                    <?php echo nl2br(htmlspecialchars($current_lesson['content'] ?? 'No content available.')); ?>
                                </div>
                                
                                <?php if ($current_lesson['resource_url']): ?>
                                <div class="mt-3">
                                    <a href="<?php echo $current_lesson['resource_url']; ?>" class="btn btn-outline-primary" download>
                                        <i class="fas fa-download"></i> Download Resource
                                    </a>
                                </div>
                                <?php endif; ?>

                                <?php if ($current_lesson['quiz_required']): ?>
                                    <div class="card border-primary border-opacity-25 mt-4">
                                        <div class="card-header bg-primary bg-opacity-10">
                                            <h4 class="h6 mb-0"><i class="fas fa-circle-question me-2"></i>Lesson knowledge check</h4>
                                            <small class="text-muted">Pass mark: <?php echo (int)$current_lesson['quiz_pass_percent']; ?>%</small>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($latestAttempt): ?>
                                                <div class="alert alert-<?php echo !empty($latestAttempt['passed']) ? 'success' : 'warning'; ?> py-2">
                                                    Your latest score: <strong><?php echo (int)$latestAttempt['score_percent']; ?>%</strong>
                                                    <?php echo !empty($latestAttempt['passed']) ? '— quiz passed.' : '— review the lesson and try again.'; ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($quiz_questions): ?>
                                                <form method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input type="hidden" name="lesson_id" value="<?php echo (int)$current_lesson['id']; ?>">
                                                    <?php foreach ($quiz_questions as $questionIndex => $question): ?>
                                                        <fieldset class="mb-4" aria-labelledby="quizQuestion<?php echo (int)$question['id']; ?>">
                                                            <legend class="fs-6 fw-semibold" id="quizQuestion<?php echo (int)$question['id']; ?>">
                                                                <?php echo ($questionIndex + 1) . '. ' . htmlspecialchars($question['question_text']); ?>
                                                            </legend>
                                                            <?php foreach (['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $optionKey => $optionColumn): ?>
                                                                <label class="d-flex align-items-start gap-2 p-2 border rounded mb-2">
                                                                    <input class="form-check-input mt-1" type="radio" name="answers[<?php echo (int)$question['id']; ?>]" value="<?php echo $optionKey; ?>" required>
                                                                    <span><strong><?php echo $optionKey; ?>.</strong> <?php echo htmlspecialchars($question[$optionColumn]); ?></span>
                                                                </label>
                                                            <?php endforeach; ?>
                                                        </fieldset>
                                                    <?php endforeach; ?>
                                                    <button type="submit" name="submit_quiz" value="1" class="btn btn-primary">
                                                        <i class="fas fa-paper-plane me-1"></i>Submit quiz
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <div class="alert alert-warning mb-0">This required quiz is not ready yet. Please contact your mentor.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <div>
                                        <?php if ($current_lesson['completed']): ?>
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Completed</span>
                                        <?php elseif ($current_lesson['quiz_required'] && !$current_lesson['quiz_passed']): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-lock"></i> Pass quiz to complete</span>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="lesson_id" value="<?php echo (int)$current_lesson['id']; ?>">
                                                <button type="submit" name="complete_lesson" value="1" class="btn btn-success">
                                                    <i class="fas fa-check"></i> Mark as Complete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <?php if ($current_lesson['completed']): ?>
                                            <button class="btn btn-outline-secondary" onclick="markIncomplete(<?php echo $current_lesson['id']; ?>)">
                                                <i class="fas fa-undo"></i> Mark Incomplete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation between lessons -->
                        <div class="d-flex justify-content-between">
                            <?php
                            // Find previous and next lessons
                            $all_lessons = [];
                            foreach ($modules as $module) {
                                $all_lessons = array_merge($all_lessons, $lessons[$module['id']] ?? []);
                            }
                            $current_index = array_search($current_lesson['id'], array_column($all_lessons, 'id'));
                            $prev_lesson = $current_index > 0 ? $all_lessons[$current_index - 1] : null;
                            $next_lesson = $current_index < count($all_lessons) - 1 ? $all_lessons[$current_index + 1] : null;
                            ?>
                            <?php if ($prev_lesson): ?>
                                <a href="course.php?id=<?php echo $course_id; ?>&lesson=<?php echo $prev_lesson['id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left"></i> Previous: <?php echo htmlspecialchars($prev_lesson['title']); ?>
                                </a>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>
                            
                            <?php if ($next_lesson): ?>
                                <?php if ($current_lesson['completed']): ?>
                                    <a href="course.php?id=<?php echo $course_id; ?>&lesson=<?php echo $next_lesson['id']; ?>" class="btn btn-primary">
                                        Next: <?php echo htmlspecialchars($next_lesson['title']); ?> <i class="fas fa-arrow-right"></i>
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary" disabled title="Complete this lesson<?php echo $current_lesson['quiz_required'] ? ' and pass its quiz' : ''; ?> first">
                                        <i class="fas fa-lock me-1"></i>Complete lesson to continue
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($current_lesson['completed']): ?>
                                    <a href="my-course.php" class="btn btn-success"><i class="fas fa-check"></i> Course Complete!</a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-success" disabled><i class="fas fa-lock me-1"></i>Complete lesson to finish</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                                <h4>No lessons available</h4>
                                <p class="text-muted">This course doesn't have any lessons yet.</p>
                                <a href="my-course.php" class="btn btn-primary">Back to Courses</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markIncomplete(lessonId) {
    if (confirm('Mark this lesson as incomplete?')) {
        window.location.href = 'mark-incomplete.php?lesson=' + encodeURIComponent(lessonId);
    }
}
</script>

<?php include '../../includes/footer.php'; ?>