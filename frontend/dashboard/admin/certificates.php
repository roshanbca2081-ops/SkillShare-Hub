<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Certificates';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates | Admin - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
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
            <div class="panel-header">
                <h5><i class="fa-solid fa-award" style="color:var(--primary);"></i> Issued Certificates</h5>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Issue Certificate</button>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Certificate</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Issued Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $certs = [
                            ['CERT-2025-001', 'Sarah Johnson', 'Data Science Fundamentals', 'Jan 05, 2025', 'Valid'],
                            ['CERT-2025-002', 'Michael Chen', 'Machine Learning Mastery', 'Jan 03, 2025', 'Valid'],
                            ['CERT-2025-003', 'Emily Davis', 'Full-Stack Web Development', 'Dec 28, 2024', 'Valid'],
                            ['CERT-2025-004', 'James Wilson', 'Human Anatomy Basics', 'Dec 20, 2024', 'Revoked'],
                            ['CERT-2025-005', 'Olivia Martinez', 'Business Strategy 101', 'Dec 15, 2024', 'Valid'],
                            ['CERT-2025-006', 'Daniel Lee', 'UI/UX Design Principles', 'Dec 10, 2024', 'Expired'],
                        ];
                        foreach ($certs as $c): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><strong><?php echo $c[0]; ?></strong></td>
                                <td><div class="user-cell"><img src="../../assets/images/profile/avatar-1.svg" alt=""><strong><?php echo $c[1]; ?></strong></div></td>
                                <td><?php echo $c[2]; ?></td>
                                <td><?php echo $c[3]; ?></td>
                                <td><span class="status-pill <?php echo strtolower($c[4]) === 'valid' ? 'approved' : 'rejected'; ?>"><?php echo $c[4]; ?></span></td>
                                <td>
                                    <div class="table-actions">
                                        <button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button>
                                        <button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-file-arrow-down"></i></button>
                                        <button class="delete-btn" aria-label="Delete"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
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
