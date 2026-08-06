<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Research';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research | Fresher - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/research.css">
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
            <div class="panel-header"><h5><i class="fa-solid fa-flask" style="color:var(--primary);"></i> Research Projects</h5></div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Project</th><th>Mentor</th><th>Field</th><th>Progress</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $fresher_research = [
                            ['AI in Healthcare', 'Dr. Aisha Khan', 'Medicine', 75, 'In Progress'],
                            ['Machine Learning Models', 'Prof. James Carter', 'Engineering', 50, 'In Progress'],
                            ['Data Visualization', 'Lisa Anderson', 'Technology', 90, 'Review'],
                            ['Predictive Analytics', 'David Brown', 'Business', 30, 'Draft'],
                            ['Neural Networks', 'Dr. Aisha Khan', 'Engineering', 85, 'Review'],
                        ];
                        foreach ($fresher_research as $r): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><strong><?php echo $r[0]; ?></strong></td>
                                <td><?php echo $r[1]; ?></td>
                                <td><span class="badge badge-primary"><?php echo $r[2]; ?></span></td>
                                <td><div style="display:flex;align-items:center;gap:0.6rem;"><div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:<?php echo $r[3]; ?>%;"></div></div><span style="font-size:.8rem;font-weight:600;"><?php echo $r[3]; ?>%</span></div></td>
                                <td><span class="status-pill <?php echo $r[4] === 'Review' ? 'approved' : ($r[4] === 'Draft' ? 'rejected' : 'pending'); ?>"><?php echo $r[4]; ?></span></td>
                                <td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button></div></td>
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
