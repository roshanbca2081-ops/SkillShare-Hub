<?php
$page_title = 'Review Applications';

require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireMentor();

$user_id = getUserId();
$project_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// =====================================================
// GET RESEARCH PROJECT
// =====================================================

$stmt = $pdo->prepare("
    SELECT *
    FROM research_projects
    WHERE id = ?
      AND mentor_id = ?
");

$stmt->execute([
    $project_id,
    $user_id
]);

$project = $stmt->fetch();

if (!$project) {
    redirect('index.php');
}

// =====================================================
// UPDATE APPLICATION STATUS
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {

    $application_id = isset($_POST['application_id'])
        ? (int) $_POST['application_id']
        : 0;

    $status = isset($_POST['status'])
        ? trim($_POST['status'])
        : '';

    $feedback = isset($_POST['feedback'])
        ? sanitize($_POST['feedback'])
        : '';

    // -------------------------------------------------
    // Validate status
    // -------------------------------------------------

    $allowed_statuses = [
        'pending',
        'approved',
        'rejected'
    ];

    if (!in_array($status, $allowed_statuses, true)) {

        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Invalid application status.'
        ];

        redirect('review.php?id=' . $project_id);
    }

    // -------------------------------------------------
    // Verify application belongs to this project
    // -------------------------------------------------

    $stmt = $pdo->prepare("
        SELECT id, fresher_id
        FROM research_applications
        WHERE id = ?
          AND project_id = ?
    ");

    $stmt->execute([
        $application_id,
        $project_id
    ]);

    $application = $stmt->fetch();

    if (!$application) {

        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => 'Application not found.'
        ];

        redirect('review.php?id=' . $project_id);
    }

    // -------------------------------------------------
    // Update application
    // -------------------------------------------------

    $stmt = $pdo->prepare("
        UPDATE research_applications
        SET
            status = ?,
            feedback = ?,
            reviewed_at = NOW()
        WHERE id = ?
          AND project_id = ?
    ");

    $stmt->execute([
        $status,
        $feedback,
        $application_id,
        $project_id
    ]);

    // =================================================
    // SEND NOTIFICATION TO FRESHER
    // =================================================

    $stmt = $pdo->prepare("
        INSERT INTO notifications
        (
            user_id,
            type,
            title,
            message,
            link
        )
        VALUES
        (
            ?,
            'research',
            'Application Update',
            CONCAT(
                'Your application for \"',
                ?,
                '\" has been ',
                ?,
                '.'
            ),
            CONCAT(
                '/SkillShare-Hub/fresher/research/details.php?id=',
                ?
            )
        )
    ");

    $stmt->execute([
        $application['fresher_id'],
        $project['title'],
        $status,
        $project_id
    ]);

    // =================================================
    // SUCCESS MESSAGE
    // =================================================

    $_SESSION['alert'] = [
        'type' => 'success',
        'icon' => 'check-circle',
        'message' => 'Application status updated successfully!'
    ];

    redirect('review.php?id=' . $project_id);
}

?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>
<?php include '../../includes/alerts.php'; ?>

<div class="container-fluid">

    <div class="row">

        <!-- =================================================
             MENTOR SIDEBAR
        ================================================== -->

        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">

            <?php include '../../includes/mentor-sidebar.php'; ?>

        </div>

        <!-- =================================================
             MAIN CONTENT
        ================================================== -->

        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">

            <!-- Page Header -->

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                <h1 class="h2">
                    Review Applications
                </h1>

                <div class="btn-toolbar mb-2 mb-md-0">

                    <a
                        href="index.php"
                        class="btn btn-outline-secondary"
                    >
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>

                </div>

            </div>

            <!-- =================================================
                 APPLICATIONS TABLE
            ================================================== -->

            <div class="row">

                <div class="col-lg-12">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body p-0">

                            <?php if (!empty($applications)): ?>

                                <div class="table-responsive">

                                    <table class="table table-hover mb-0">

                                        <thead>

                                            <tr>
                                                <th>Student</th>
                                                <th>Applied</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php foreach ($applications as $application): ?>

                                                <tr>

                                                    <!-- Student -->

                                                    <td>

                                                        <div class="d-flex align-items-center">

                                                            <img
                                                                src="<?php echo getAvatar($application); ?>"
                                                                class="rounded-circle me-2"
                                                                style="width:32px;height:32px;"
                                                                alt="Student"
                                                            >

                                                            <div>

                                                                <div>
                                                                    <?php
                                                                    echo htmlspecialchars(
                                                                        $application['full_name']
                                                                    );
                                                                    ?>
                                                                </div>

                                                                <small class="text-muted">
                                                                    <?php
                                                                    echo htmlspecialchars(
                                                                        $application['email']
                                                                    );
                                                                    ?>
                                                                </small>

                                                            </div>

                                                        </div>

                                                    </td>

                                                    <!-- Applied Date -->

                                                    <td>

                                                        <?php
                                                        echo formatDateTime(
                                                            $application['applied_at']
                                                        );
                                                        ?>

                                                    </td>

                                                    <!-- Status -->

                                                    <td>

                                                        <?php

                                                        if ($application['status'] === 'approved') {
                                                            $badge = 'success';
                                                        } elseif ($application['status'] === 'rejected') {
                                                            $badge = 'danger';
                                                        } else {
                                                            $badge = 'warning';
                                                        }

                                                        ?>

                                                        <span class="badge bg-<?php echo $badge; ?>">

                                                            <?php
                                                            echo ucfirst(
                                                                htmlspecialchars(
                                                                    $application['status']
                                                                )
                                                            );
                                                            ?>

                                                        </span>

                                                    </td>

                                                    <!-- Actions -->

                                                    <td>

                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#reviewModal<?php echo $application['id']; ?>"
                                                        >

                                                            <i class="fas fa-edit"></i>
                                                            Review

                                                        </button>

                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#detailsModal<?php echo $application['id']; ?>"
                                                        >

                                                            <i class="fas fa-eye"></i>

                                                        </button>

                                                    </td>

                                                </tr>

                                                <!-- =================================================
                                                     REVIEW MODAL
                                                ================================================== -->

                                                <div
                                                    class="modal fade"
                                                    id="reviewModal<?php echo $application['id']; ?>"
                                                    tabindex="-1"
                                                    aria-hidden="true"
                                                >

                                                    <div class="modal-dialog">

                                                        <div class="modal-content">

                                                            <div class="modal-header">

                                                                <h5 class="modal-title">
                                                                    Review Application
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
                                                                        name="application_id"
                                                                        value="<?php echo $application['id']; ?>"
                                                                    >

                                                                    <p>
                                                                        <strong>Student:</strong>
                                                                        <?php
                                                                        echo htmlspecialchars(
                                                                            $application['full_name']
                                                                        );
                                                                        ?>
                                                                    </p>

                                                                    <div class="mb-3">

                                                                        <label class="form-label">
                                                                            Status
                                                                        </label>

                                                                        <select
                                                                            name="status"
                                                                            class="form-select"
                                                                            required
                                                                        >

                                                                            <option
                                                                                value="pending"
                                                                                <?php
                                                                                echo $application['status'] === 'pending'
                                                                                    ? 'selected'
                                                                                    : '';
                                                                                ?>
                                                                            >
                                                                                Pending
                                                                            </option>

                                                                            <option
                                                                                value="approved"
                                                                                <?php
                                                                                echo $application['status'] === 'approved'
                                                                                    ? 'selected'
                                                                                    : '';
                                                                                ?>
                                                                            >
                                                                                Approve
                                                                            </option>

                                                                            <option
                                                                                value="rejected"
                                                                                <?php
                                                                                echo $application['status'] === 'rejected'
                                                                                    ? 'selected'
                                                                                    : '';
                                                                                ?>
                                                                            >
                                                                                Reject
                                                                            </option>

                                                                        </select>

                                                                    </div>

                                                                    <div class="mb-3">

                                                                        <label class="form-label">
                                                                            Feedback
                                                                        </label>

                                                                        <textarea
                                                                            name="feedback"
                                                                            class="form-control"
                                                                            rows="4"
                                                                            placeholder="Enter feedback for the student..."
                                                                        ><?php
                                                                        echo htmlspecialchars(
                                                                            $application['feedback'] ?? ''
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
                                                                        name="update_status"
                                                                        class="btn btn-primary"
                                                                    >

                                                                        <i class="fas fa-save"></i>
                                                                        Update

                                                                    </button>

                                                                </div>

                                                            </form>

                                                        </div>

                                                    </div>

                                                </div>

                                                <!-- =================================================
                                                     DETAILS MODAL
                                                ================================================== -->

                                                <div
                                                    class="modal fade"
                                                    id="detailsModal<?php echo $application['id']; ?>"
                                                    tabindex="-1"
                                                    aria-hidden="true"
                                                >

                                                    <div class="modal-dialog">

                                                        <div class="modal-content">

                                                            <div class="modal-header">

                                                                <h5 class="modal-title">
                                                                    Application Details
                                                                </h5>

                                                                <button
                                                                    type="button"
                                                                    class="btn-close"
                                                                    data-bs-dismiss="modal"
                                                                ></button>

                                                            </div>

                                                            <div class="modal-body">

                                                                <p>
                                                                    <strong>Student:</strong>
                                                                    <?php
                                                                    echo htmlspecialchars(
                                                                        $application['full_name']
                                                                    );
                                                                    ?>
                                                                </p>

                                                                <p>
                                                                    <strong>Email:</strong>
                                                                    <?php
                                                                    echo htmlspecialchars(
                                                                        $application['email']
                                                                    );
                                                                    ?>
                                                                </p>

                                                                <p>
                                                                    <strong>Applied:</strong>
                                                                    <?php
                                                                    echo formatDateTime(
                                                                        $application['applied_at']
                                                                    );
                                                                    ?>
                                                                </p>

                                                                <hr>

                                                                <p>
                                                                    <strong>Cover Letter:</strong>
                                                                </p>

                                                                <p>
                                                                    <?php
                                                                    echo nl2br(
                                                                        htmlspecialchars(
                                                                            $application['cover_letter']
                                                                            ?? 'No cover letter provided.'
                                                                        )
                                                                    );
                                                                    ?>
                                                                </p>

                                                                <?php if (!empty($application['skills'])): ?>

                                                                    <p>
                                                                        <strong>Skills:</strong>
                                                                        <?php
                                                                        echo htmlspecialchars(
                                                                            $application['skills']
                                                                        );
                                                                        ?>
                                                                    </p>

                                                                <?php endif; ?>

                                                                <?php if (!empty($application['experience'])): ?>

                                                                    <p>
                                                                        <strong>Experience:</strong>
                                                                        <?php
                                                                        echo nl2br(
                                                                            htmlspecialchars(
                                                                                $application['experience']
                                                                            )
                                                                        );
                                                                        ?>
                                                                    </p>

                                                                <?php endif; ?>

                                                                <?php if (!empty($application['feedback'])): ?>

                                                                    <hr>

                                                                    <p>
                                                                        <strong>Feedback:</strong>
                                                                    </p>

                                                                    <p>
                                                                        <?php
                                                                        echo nl2br(
                                                                            htmlspecialchars(
                                                                                $application['feedback']
                                                                            )
                                                                        );
                                                                        ?>
                                                                    </p>

                                                                <?php endif; ?>

                                                            </div>

                                                            <div class="modal-footer">

                                                                <button
                                                                    type="button"
                                                                    class="btn btn-secondary"
                                                                    data-bs-dismiss="modal"
                                                                >
                                                                    Close
                                                                </button>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php else: ?>

                                <div class="text-center py-5">

                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>

                                    <h5>
                                        No applications yet
                                    </h5>

                                    <p class="text-muted">
                                        Students haven't applied to this project yet.
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