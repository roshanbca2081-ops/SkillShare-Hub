<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Academic Fields';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Fields | Admin - SkillShare Hub</title>
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
                <h5><i class="fa-solid fa-layer-group" style="color:var(--primary);"></i> Academic Fields</h5>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Add Field</button>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Field</th>
                            <th>Icon</th>
                            <th>Courses</th>
                            <th>Mentors</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $fields = [
                            ['Engineering & Technology', 'fa-microchip', 84, 22, 'Active'],
                            ['Medicine & Healthcare', 'fa-stethoscope', 56, 15, 'Active'],
                            ['Business & Finance', 'fa-briefcase', 62, 12, 'Active'],
                            ['Creative Arts & Design', 'fa-palette', 45, 10, 'Active'],
                            ['Law & Justice', 'fa-scale-balanced', 28, 8, 'Inactive'],
                            ['Education & Teaching', 'fa-chalkboard-user', 38, 9, 'Active'],
                        ];
                        foreach ($fields as $f): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><strong><?php echo $f[0]; ?></strong></td>
                                <td><span class="badge badge-primary"><i class="fa-solid <?php echo $f[1]; ?>"></i></span></td>
                                <td><?php echo $f[2]; ?></td>
                                <td><?php echo $f[3]; ?></td>
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
