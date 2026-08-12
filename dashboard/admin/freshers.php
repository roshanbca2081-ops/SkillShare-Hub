<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Freshers';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freshers | Admin - SkillShare Hub</title>
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

        <div class="dash-grid-4" style="margin-bottom:var(--spacing-5);">
            <div class="stat-card reveal"><div class="stat-icon primary"><i class="fa-solid fa-user-graduate"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="1165">0</h4><p>Total Freshers</p></div></div>
            <div class="stat-card reveal reveal-delay-1"><div class="stat-icon secondary"><i class="fa-solid fa-book-open"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="980">0</h4><p>Enrolled</p></div></div>
            <div class="stat-card reveal reveal-delay-2"><div class="stat-icon accent"><i class="fa-solid fa-user-check"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="145">0</h4><p>Active</p></div></div>
            <div class="stat-card reveal reveal-delay-3"><div class="stat-icon warning-bg"><i class="fa-solid fa-user-clock"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="40">0</h4><p>Inactive</p></div></div>
        </div>

        <div class="panel reveal">
            <div class="panel-header">
                <h5><i class="fa-solid fa-user-graduate" style="color:var(--primary);"></i> All Freshers</h5>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Add Fresher</button>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Fresher</th>
                            <th>Field</th>
                            <th>Courses</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $freshers = [
                            ['Sarah Johnson', 'Data Science', 3, 65, 'Active', 'avatar-1.svg'],
                            ['Emily Davis', 'Medicine', 2, 40, 'Active', 'avatar-2.svg'],
                            ['James Wilson', 'Engineering', 4, 80, 'Active', 'avatar-3.svg'],
                            ['Olivia Martinez', 'Business', 1, 25, 'Inactive', 'avatar-4.svg'],
                            ['Daniel Lee', 'Design', 5, 90, 'Active', 'avatar-5.svg'],
                            ['Sophia Moore', 'Law', 2, 35, 'Inactive', 'avatar-6.svg'],
                        ];
                        foreach ($freshers as $f): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><div class="user-cell"><img src="../../assets/images/profile/<?php echo $f[5]; ?>" alt=""><strong><?php echo $f[0]; ?></strong></div></td>
                                <td><?php echo $f[1]; ?></td>
                                <td><?php echo $f[2]; ?></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:0.6rem;">
                                        <div class="progress-track" style="flex:1;min-width:80px;"><div class="progress-fill" style="width:<?php echo $f[3]; ?>%;"></div></div>
                                        <span style="font-size:.8rem;font-weight:600;"><?php echo $f[3]; ?>%</span>
                                    </div>
                                </td>
                                <td><span class="status-pill <?php echo $f[4] === 'Active' ? 'approved' : 'rejected'; ?>"><?php echo $f[4]; ?></span></td>
                                <td>
                                    <div class="table-actions">
                                        <button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button>
                                        <button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button>
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
