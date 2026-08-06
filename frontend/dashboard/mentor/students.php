<?php
session_start();
$sidebar_role = 'mentor';
$sidebar_active = 'Students';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | Mentor - SkillShare Hub</title>
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
                <h5><i class="fa-solid fa-user-graduate" style="color:var(--primary);"></i> My Students</h5>
                <div style="display:flex;gap:0.5rem;"><button class="btn btn-outline btn-sm"><i class="fa-solid fa-filter"></i> Filter</button></div>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr><th><input type="checkbox" id="checkAll"></th><th>Student</th><th>Course</th><th>Progress</th><th>Last Active</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $students = [
                            ['Sarah Johnson', 'avatar-1.svg', 'Data Science', 75, '2h ago', 'Active'],
                            ['Michael Chen', 'avatar-2.svg', 'Machine Learning', 50, '1d ago', 'Active'],
                            ['Emily Davis', 'avatar-3.svg', 'Python for Beginners', 90, '3h ago', 'Active'],
                            ['James Wilson', 'avatar-4.svg', 'Deep Learning', 30, '4d ago', 'At Risk'],
                            ['Olivia Martinez', 'avatar-5.svg', 'Statistics', 85, '1h ago', 'Active'],
                            ['Daniel Lee', 'avatar-6.svg', 'Data Science', 15, '1w ago', 'Inactive'],
                        ];
                        foreach ($students as $s): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><div class="user-cell"><img src="../../assets/images/profile/<?php echo $s[1]; ?>" alt=""><strong><?php echo $s[0]; ?></strong></div></td>
                                <td><?php echo $s[2]; ?></td>
                                <td><div style="display:flex;align-items:center;gap:0.6rem;"><div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:<?php echo $s[3]; ?>%;"></div></div><span style="font-size:.8rem;font-weight:600;"><?php echo $s[3]; ?>%</span></div></td>
                                <td><?php echo $s[4]; ?></td>
                                <td><span class="status-pill <?php echo $s[5] === 'Active' ? 'approved' : ($s[5] === 'At Risk' ? 'pending' : 'rejected'); ?>"><?php echo $s[5]; ?></span></td>
                                <td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Message"><i class="fa-solid fa-comment"></i></button></div></td>
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
