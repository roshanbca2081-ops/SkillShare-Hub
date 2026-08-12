<?php
session_start();
$sidebar_role = 'mentor';
$sidebar_active = 'My Courses';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses | Mentor - SkillShare Hub</title>
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
                <h5><i class="fa-solid fa-book-open" style="color:var(--primary);"></i> My Courses</h5>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Create Course</button>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr><th><input type="checkbox" id="checkAll"></th><th>Course</th><th>Students</th><th>Rating</th><th>Price</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $mycourses = [
                            ['Data Science Fundamentals', 'intro-datascience', 240, '4.8', '$49.99', 'Published'],
                            ['Machine Learning Mastery', 'ml-mastery', 180, '4.9', '$79.99', 'Published'],
                            ['Python for Beginners', 'python-basics', 320, '4.7', '$29.99', 'Published'],
                            ['Deep Learning Advanced', 'deep-learning', 95, '4.6', '$99.99', 'Draft'],
                            ['Statistics Essentials', 'statistics', 150, '4.5', '$39.99', 'Published'],
                        ];
                        foreach ($mycourses as $c): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><div class="user-cell"><img src="../../assets/images/course/<?php echo $c[1]; ?>.svg" alt=""><strong><?php echo $c[0]; ?></strong></div></td>
                                <td><?php echo $c[2]; ?></td>
                                <td><i class="fa-solid fa-star" style="color:#fdcb6e;"></i> <?php echo $c[3]; ?></td>
                                <td><strong><?php echo $c[4]; ?></strong></td>
                                <td><span class="status-pill <?php echo $c[5] === 'Published' ? 'approved' : 'pending'; ?>"><?php echo $c[5]; ?></span></td>
                                <td><div class="table-actions"><button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button><button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-pen"></i></button><button class="delete-btn" aria-label="Delete"><i class="fa-solid fa-trash"></i></button></div></td>
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
