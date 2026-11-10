<?php
$page_title = 'Student Course Report';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$mentor_id = getUserId();
$enrollmentDateColumn = getEnrollmentDateColumn($pdo);
$enrollment_id = isset($_GET['enrollment']) ? (int)$_GET['enrollment'] : 0;

// Authorize the report through the mentor-owned course, not URL parameters alone.
$stmt = $pdo->prepare("SELECT e.*, e.$enrollmentDateColumn AS enrolled_at, u.id AS student_id, u.full_name, u.email, u.avatar,
                              c.id AS report_course_id, c.title AS course_title, c.mentor_id
                       FROM enrollments e
                       JOIN users u ON u.id = e.fresher_id
                       JOIN courses c ON c.id = e.course_id
                       WHERE e.id = ? AND c.mentor_id = ?
                       LIMIT 1");
$stmt->execute([$enrollment_id, $mentor_id]);
$report = $stmt->fetch();

if (!$report) {
    redirect('../students.php');
}

// Pull only lessons learners can access in the course player.
$stmt = $pdo->prepare("SELECT cm.title AS module_title, cm.order_number AS module_order,
                              cl.id AS lesson_id, cl.title AS lesson_title, cl.order_number AS lesson_order,
                                                            COALESCE(lp.completed, 0) AS completed, lp.completed_at,
                                                            cl.quiz_required,
                                                            (SELECT qa.score_percent FROM lesson_quiz_attempts qa
                                                             WHERE qa.fresher_id = ? AND qa.lesson_id = cl.id
                                                                 AND qa.quiz_version = cl.quiz_version
                                                             ORDER BY qa.id DESC LIMIT 1) AS quiz_score,
                                                            (SELECT qa.passed FROM lesson_quiz_attempts qa
                                                             WHERE qa.fresher_id = ? AND qa.lesson_id = cl.id
                                                                 AND qa.quiz_version = cl.quiz_version
                                                             ORDER BY qa.id DESC LIMIT 1) AS quiz_passed,
                                                            (SELECT qa.attempted_at FROM lesson_quiz_attempts qa
                                                             WHERE qa.fresher_id = ? AND qa.lesson_id = cl.id
                                                                 AND qa.quiz_version = cl.quiz_version
                                                             ORDER BY qa.id DESC LIMIT 1) AS quiz_attempted_at
                       FROM course_modules cm
                       JOIN course_lessons cl ON cl.module_id = cm.id AND cl.is_published = 1
                       LEFT JOIN lesson_progress lp ON lp.lesson_id = cl.id AND lp.fresher_id = ?
                       WHERE cm.course_id = ? AND cm.is_published = 1
                       ORDER BY cm.order_number, cl.order_number, cl.id");
$stmt->execute([$report['student_id'], $report['student_id'], $report['student_id'], $report['student_id'], $report['report_course_id']]);
$lessons = $stmt->fetchAll();
$total_lessons = count($lessons);
$completed_lessons = 0;
foreach ($lessons as $lesson) {
    if ((int)$lesson['completed'] === 1) {
        $completed_lessons++;
    }
}
$progress_percent = $total_lessons > 0
    ? (int)round(($completed_lessons / $total_lessons) * 100)
    : (int)($report['progress'] ?? 0);

// Include published assignments and this student's submission/grade, if any.
$stmt = $pdo->prepare("SELECT a.id, a.title, a.due_date, a.max_score,
                              s.status AS submission_status, s.submitted_at, s.score, s.feedback
                       FROM assignments a
                       LEFT JOIN assignment_submissions s
                              ON s.assignment_id = a.id AND s.fresher_id = ?
                       WHERE a.course_id = ? AND a.mentor_id = ? AND a.is_published = 1
                       ORDER BY a.due_date IS NULL, a.due_date, a.title");
$stmt->execute([$report['student_id'], $report['report_course_id'], $mentor_id]);
$assignments = $stmt->fetchAll();
$submitted_count = 0;
$graded_count = 0;
$earned_points = 0;
$available_points = 0;
foreach ($assignments as $assignment) {
    if (!empty($assignment['submission_status'])) {
        $submitted_count++;
    }
    if ($assignment['submission_status'] === 'graded' && $assignment['score'] !== null) {
        $graded_count++;
        $earned_points += (int)$assignment['score'];
        $available_points += (int)$assignment['max_score'];
    }
}
$grade_percent = $available_points > 0 ? round(($earned_points / $available_points) * 100) : null;
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<style>
    @media print {
        .sidebar, .navbar, .report-actions, footer { display: none !important; }
        .container-fluid, .col-md-9, .col-lg-10 { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; break-inside: avoid; }
        body { background: #fff !important; }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 pb-2 mb-4 border-bottom">
                <div>
                    <h1 class="h2 mb-1">Student Course Report</h1>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($report['course_title']); ?></p>
                </div>
                <div class="report-actions d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print report
                    </button>
                    <a href="view.php?id=<?php echo (int)$report['report_course_id']; ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to course
                    </a>
                </div>
            </div>

            <section class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <img src="<?php echo htmlspecialchars(getAvatar($report), ENT_QUOTES, 'UTF-8'); ?>"
                             alt="" class="rounded-circle me-3" style="width:64px;height:64px;object-fit:cover">
                        <div>
                            <h2 class="h4 mb-1"><?php echo htmlspecialchars($report['full_name']); ?></h2>
                            <div class="text-muted"><?php echo htmlspecialchars($report['email']); ?></div>
                        </div>
                        <span class="badge bg-<?php echo $report['status'] === 'completed' ? 'success' : ($report['status'] === 'active' ? 'primary' : 'secondary'); ?> ms-auto fs-6">
                            <?php echo htmlspecialchars(ucfirst($report['status'])); ?>
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-light rounded p-3 h-100">
                                <small class="text-muted d-block">Course progress</small>
                                <strong class="fs-4"><?php echo $progress_percent; ?>%</strong>
                                <div class="progress mt-2" style="height:7px">
                                    <div class="progress-bar" role="progressbar" style="width:<?php echo $progress_percent; ?>%" aria-valuenow="<?php echo $progress_percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-light rounded p-3 h-100">
                                <small class="text-muted d-block">Lessons completed</small>
                                <strong class="fs-4"><?php echo $completed_lessons; ?> / <?php echo $total_lessons; ?></strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-light rounded p-3 h-100">
                                <small class="text-muted d-block">Assignments submitted</small>
                                <strong class="fs-4"><?php echo $submitted_count; ?> / <?php echo count($assignments); ?></strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-light rounded p-3 h-100">
                                <small class="text-muted d-block">Graded assignment average</small>
                                <strong class="fs-4"><?php echo $grade_percent === null ? '—' : $grade_percent . '%'; ?></strong>
                                <?php if ($grade_percent !== null): ?>
                                    <small class="text-muted d-block"><?php echo $graded_count; ?> graded</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3 text-muted small">
                        <div class="col-md-4"><strong>Enrolled:</strong> <?php echo !empty($report['enrolled_at']) ? htmlspecialchars(formatDateTime($report['enrolled_at'])) : '—'; ?></div>
                        <div class="col-md-4"><strong>Last accessed:</strong> <?php echo !empty($report['last_accessed_at']) ? htmlspecialchars(formatDateTime($report['last_accessed_at'])) : 'Not accessed'; ?></div>
                        <div class="col-md-4"><strong>Completed:</strong> <?php echo !empty($report['completed_at']) ? htmlspecialchars(formatDateTime($report['completed_at'])) : 'Not completed'; ?></div>
                    </div>
                </div>
            </section>

            <section class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h3 class="h5 mb-0"><i class="fas fa-list-check text-primary me-2"></i>Lesson progress</h3>
                </div>
                <?php if ($lessons): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>Module</th><th>Lesson</th><th>Status</th><th>Quiz result</th><th>Completed at</th></tr></thead>
                            <tbody>
                                <?php foreach ($lessons as $lesson): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($lesson['module_title']); ?></td>
                                        <td><?php echo htmlspecialchars($lesson['lesson_title']); ?></td>
                                        <td>
                                            <?php if ((int)$lesson['completed'] === 1): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border">Not completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (empty($lesson['quiz_required'])): ?>
                                                <span class="text-muted">Not required</span>
                                            <?php elseif ($lesson['quiz_score'] === null): ?>
                                                <span class="text-muted">No attempt</span>
                                            <?php else: ?>
                                                <span class="badge bg-<?php echo !empty($lesson['quiz_passed']) ? 'success' : 'warning text-dark'; ?>">
                                                    <?php echo (int)$lesson['quiz_score']; ?>% — <?php echo !empty($lesson['quiz_passed']) ? 'Passed' : 'Not passed'; ?>
                                                </span>
                                                <small class="d-block text-muted"><?php echo htmlspecialchars(formatDateTime($lesson['quiz_attempted_at'])); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo !empty($lesson['completed_at']) ? htmlspecialchars(formatDateTime($lesson['completed_at'])) : '—'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="card-body text-muted">There are no published lessons in this course yet.</div>
                <?php endif; ?>
            </section>

            <section class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h3 class="h5 mb-0"><i class="fas fa-clipboard-check text-primary me-2"></i>Assignment report</h3>
                </div>
                <?php if ($assignments): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>Assignment</th><th>Due</th><th>Submission</th><th>Score</th><th>Feedback</th></tr></thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                        <td><?php echo !empty($assignment['due_date']) ? htmlspecialchars(formatDateTime($assignment['due_date'])) : '—'; ?></td>
                                        <td>
                                            <?php if (!empty($assignment['submission_status'])): ?>
                                                <span class="badge bg-<?php echo $assignment['submission_status'] === 'graded' ? 'success' : ($assignment['submission_status'] === 'returned' ? 'warning' : 'info'); ?>">
                                                    <?php echo htmlspecialchars(ucfirst($assignment['submission_status'])); ?>
                                                </span>
                                                <small class="d-block text-muted"><?php echo htmlspecialchars(formatDateTime($assignment['submitted_at'])); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Not submitted</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $assignment['score'] !== null ? (int)$assignment['score'] . ' / ' . (int)$assignment['max_score'] : '—'; ?></td>
                                        <td><?php echo !empty($assignment['feedback']) ? nl2br(htmlspecialchars($assignment['feedback'])) : '—'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="card-body text-muted">There are no published assignments for this course.</div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
