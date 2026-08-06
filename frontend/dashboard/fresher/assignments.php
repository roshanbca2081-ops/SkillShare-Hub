<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Assignments';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments | Fresher - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/assignment.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include '../../components/loader.php'; ?>
    <?php include '../../components/sidebar.php'; ?>

    <div class="dashboard-main">
        <?php include __DIR__ . '/_topbar.php'; ?>

        <div class="panel reveal">
            <div class="panel-header"><h5><i class="fa-solid fa-file-pen" style="color:var(--primary);"></i> My Assignments</h5><button class="btn btn-primary btn-sm"><i class="fa-solid fa-arrow-up-from-bracket"></i> Submit Assignment</button></div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Assignment</th><th>Course</th><th>Due Date</th><th>Marks</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $fresher_assignments = [
                            ['Data Preprocessing', 'Data Science', 'Jan 15', '85/100', 'Graded'],
                            ['Python Loops Project', 'Python', 'Jan 18', '—', 'Pending'],
                            ['Web Page Design', 'Web Dev', 'Jan 20', '—', 'Not Submitted'],
                            ['Neural Network Lab', 'Machine Learning', 'Jan 12', '92/100', 'Graded'],
                            ['Statistics Report', 'Statistics', 'Jan 22', '—', 'Pending'],
                        ];
                        foreach ($fresher_assignments as $a): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><strong><?php echo $a[0]; ?></strong></td>
                                <td><?php echo $a[1]; ?></td>
                                <td><?php echo $a[2]; ?></td>
                                <td><?php echo $a[3]; ?></td>
                                <td><span class="status-pill <?php echo $a[4] === 'Graded' ? 'approved' : ($a[4] === 'Pending' ? 'pending' : 'rejected'); ?>"><?php echo $a[4]; ?></span></td>
                                <td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Upload"><i class="fa-solid fa-upload"></i></button></div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/navbar.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/animation.js"></script>
</body>

</html>
