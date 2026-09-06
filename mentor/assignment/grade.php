<?php
$page_title = 'Grade Assignments';

require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$assignment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// =====================================================
// GET ASSIGNMENT DETAILS
// =====================================================

$stmt = $pdo->prepare("
    SELECT 
        a.*, 
        c.title AS course_title
    FROM assignments a
    JOIN courses c ON a.course_id = c.id
    WHERE a.id = ? 
      AND a.mentor_id = ?
");

$stmt->execute([$assignment_id, $user_id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    redirect('index.php');
}

// =====================================================
// GET SUBMISSIONS
// =====================================================

$stmt = $pdo->prepare("
    SELECT 
        s.*, 
        u.full_name, 
        u.email, 
        u.avatar
    FROM assignment_submissions s
    JOIN users u ON s.fresher_id = u.id
    WHERE s.assignment_id = ?
    ORDER BY s.submitted_at DESC
");

$stmt->execute([$assignment_id]);
$submissions = $stmt->fetchAll();

// =====================================================
// GRADE SUBMISSION
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade_submission'])) {

    $submission_id = isset($_POST['submission_id'])
        ? (int)$_POST['submission_id']
        : 0;

    $score = isset($_POST['score'])
        ? (float)$_POST['score']
        : 0;

    $feedback = isset($_POST['feedback'])
        ? sanitize($_POST['feedback'])
        : '';

    // -------------------------------------------------
    // Validate score
    // -------------------------------------------------

    if ($score < 0 || $score > (float)$assignment['max_score']) {

        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Invalid score. Please enter a valid score.'
        ];

        redirect('grade.php?id=' . $assignment_id);
    }

    // -------------------------------------------------
    // Make sure submission belongs to this assignment
    // -------------------------------------------------

    $stmt = $pdo->prepare("
        SELECT id, fresher_id
        FROM assignment_submissions
        WHERE id = ?
          AND assignment_id = ?
    ");

    $stmt->execute([
        $submission_id,
        $assignment_id
    ]);

    $student = $stmt->fetch();

    if (!$student) {

        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Invalid submission.'
        ];

        redirect('grade.php?id=' . $assignment_id);
    }

    // -------------------------------------------------
    // Update submission
    // -------------------------------------------------

    $stmt = $pdo->prepare("
        UPDATE assignment_submissions
        SET 
            score = ?,
            feedback = ?,
            status = 'graded',
            graded_by = ?,
            graded_at = NOW()
        WHERE id = ?
          AND assignment_id = ?
    ");

    $stmt->execute([
        $score,
        $feedback,
        $user_id,
        $submission_id,
        $assignment_id
    ]);

    // =================================================
    // SEND NOTIFICATION TO FRESHER
    // =================================================

    $stmt = $pdo->prepare("
        INSERT INTO notifications
            (user_id, type, title, message, link)
        VALUES
            (
                ?,
                'assignment_graded',
                'Assignment Graded',
                CONCAT(
                    'Your assignment \"',
                    ?,
                    '\" has been graded.'
                ),
                CONCAT(
                    '/SkillShare-Hub/fresher/assignments/details.php?id=',
                    ?
                )
            )
    ");

    $stmt->execute([
        $student['fresher_id'],
        $assignment['title'],
        $assignment_id
    ]);

    // =================================================
    // SUCCESS MESSAGE
    // =================================================

    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Submission graded successfully!'
    ];

    redirect('grade.php?id=' . $assignment_id);
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">

    <div class="row">

        <!-- Mentor Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/mentor-sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">

            <!-- Page Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                <h1 class="h2">
                    Grade:
                    <?php echo htmlspecialchars($assignment['title']); ?>
                </h1>

                <div class="btn-toolbar mb-2 mb-md-0">

                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>

                </div>

            </div>

            <!-- Submissions -->
            <div class="row">

                <div class="col-lg-12">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body p-0">

                            <?php if (!empty($submissions)): ?>

                                <div class="table-responsive">

                                    <table class="table table-hover mb-0">

                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Submitted</th>
                                                <th>Status</th>
                                                <th>Score</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            <?php foreach ($submissions as $submission): ?>

                                                <tr>

                                                    <!-- Student -->
                                                    <td>

                                                        <div class="d-flex align-items-center">

                                                            <img
                                                                src="<?php echo getAvatar($submission); ?>"
                                                                class="rounded-circle me-2"
                                                                style="width:32px;height:32px;"
                                                                alt="Student"
                                                            >

                                                            <?php echo htmlspecialchars($submission['full_name']); ?>

                                                        </div>

                                                    </td>

                                                    <!-- Submitted -->
                                                    <td>
                                                        <?php
                                                        echo formatDateTime(
                                                            $submission['submitted_at']
                                                        );
                                                        ?>
                                                    </td>

                                                    <!-- Status -->
                                                    <td>

                                                        <span class="badge bg-<?php
                                                            echo $submission['status'] === 'graded'
                                                                ? 'success'
                                                                : 'warning';
                                                        ?>">

                                                            <?php
                                                            echo ucfirst(
                                                                htmlspecialchars(
                                                                    $submission['status']
                                                                )
                                                            );
                                                            ?>

                                                        </span>

                                                    </td>

                                                    <!-- Score -->
                                                    <td>

                                                        <?php if ($submission['score'] !== null): ?>

                                                            <?php echo htmlspecialchars($submission['score']); ?>
                                                            /
                                                            <?php echo htmlspecialchars($assignment['max_score']); ?>

                                                        <?php else: ?>

                                                            -

                                                        <?php endif; ?>

                                                    </td>

                                                    <!-- Actions -->
                                                    <td>

                                                        <button
                                                            class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#gradeModal<?php echo $submission['id']; ?>"
                                                        >

                                                            <i class="fas fa-graduation-cap"></i>
                                                            Grade

                                                        </button>

                                                        <a
                                                            href="view-submission.php?id=<?php echo $submission['id']; ?>"
                                                            class="btn btn-sm btn-outline-secondary"
                                                        >

                                                            <i class="fas fa-eye"></i>

                                                        </a>

                                                    </td>

                                                </tr>

                                                <!-- Grade Modal -->
                                                <div
                                                    class="modal fade"
                                                    id="gradeModal<?php echo $submission['id']; ?>"
                                                    tabindex="-1"
                                                    aria-hidden="true"
                                                >

                                                    <div class="modal-dialog">

                                                        <div class="modal-content">

                                                            <div class="modal-header">

                                                                <h5 class="modal-title">
                                                                    Grade Submission
                                                                </h5>

                                                                <button
                                                                    type="button"
                                                                    class="btn-close"
                                                                    data-bs-dismiss="modal"
                                                                ></button>

                                                            </div>

                                                            <form method="POST">

                                                                <div class="modal-body">

                                                                    <input
                                                                        type="hidden"
                                                                        name="submission_id"
                                                                        value="<?php echo $submission['id']; ?>"
                                                                    >

                                                                    <p>
                                                                        <strong>Student:</strong>
                                                                        <?php
                                                                        echo htmlspecialchars(
                                                                            $submission['full_name']
                                                                        );
                                                                        ?>
                                                                    </p>

                                                                    <div class="mb-3">

                                                                        <label class="form-label">
                                                                            Score
                                                                            (max
                                                                            <?php
                                                                            echo htmlspecialchars(
                                                                                $assignment['max_score']
                                                                            );
                                                                            ?>
                                                                            )
                                                                        </label>

                                                                        <input
                                                                            type="number"
                                                                            name="score"
                                                                            class="form-control"
                                                                            value="<?php
                                                                                echo $submission['score'] ?? '';
                                                                            ?>"
                                                                            step="0.5"
                                                                            min="0"
                                                                            max="<?php
                                                                                echo htmlspecialchars(
                                                                                    $assignment['max_score']
                                                                                );
                                                                            ?>"
                                                                            required
                                                                        >

                                                                    </div>

                                                                    <div class="mb-3">

                                                                        <label class="form-label">
                                                                            Feedback
                                                                        </label>

                                                                        <textarea
                                                                            name="feedback"
                                                                            class="form-control"
                                                                            rows="3"
                                                                        ><?php
                                                                            echo htmlspecialchars(
                                                                                $submission['feedback'] ?? ''
                                                                            );
                                                                        ?></textarea>

                                                                    </div>

                                                                </div>

                                                                <div class="modal-footer">

                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-secondary"
                                                                        data-bs-dismiss="modal"
                                                                    >
                                                                        Cancel
                                                                    </button>

                                                                    <button
                                                                        type="submit"
                                                                        name="grade_submission"
                                                                        class="btn btn-primary"
                                                                    >

                                                                        <i class="fas fa-save"></i>
                                                                        Save Grade

                                                                    </button>

                                                                </div>

                                                            </form>

                                                        </div>

                                                    </div>

                                                </div>

                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php else: ?>

                                <div class="text-center py-5">

                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>

                                    <h5>
                                        No submissions yet
                                    </h5>

                                    <p class="text-muted">
                                        Students haven't submitted this assignment yet.
                                    </p>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include '../../includes/footer.php'; ?>