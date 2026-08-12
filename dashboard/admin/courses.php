<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Courses';
require_once __DIR__ . '/_shared.php';
// Override active
$sidebar_active = 'Courses';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses | Admin - SkillShare Hub</title>
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
            <div class="stat-card reveal"><div class="stat-icon primary"><i class="fa-solid fa-book-open"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="320">0</h4><p>Total Courses</p></div></div>
            <div class="stat-card reveal reveal-delay-1"><div class="stat-icon secondary"><i class="fa-solid fa-circle-check"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="285">0</h4><p>Published</p></div></div>
            <div class="stat-card reveal reveal-delay-2"><div class="stat-icon accent"><i class="fa-solid fa-pen-clip"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="25">0</h4><p>Draft</p></div></div>
            <div class="stat-card reveal reveal-delay-3"><div class="stat-icon warning-bg"><i class="fa-solid fa-eye-slash"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="10">0</h4><p>Archived</p></div></div>
        </div>

        <div class="panel reveal">
            <div class="panel-header">
                <h5><i class="fa-solid fa-book-open" style="color:var(--primary);"></i> All Courses</h5>
                <div style="display:flex;gap:0.5rem;">
                    <button class="btn btn-outline btn-sm"><i class="fa-solid fa-filter"></i> Filter</button>
                    <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Add Course</button>
                </div>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $courses = [
                            ['Data Science Fundamentals', 'Dr. Aisha Khan', 'Data Science', '$49.99', '1,240', 'Published', 'course-1.svg'],
                            ['Machine Learning Mastery', 'Prof. James Carter', 'Engineering', '$79.99', '980', 'Published', 'course-2.svg'],
                            ['Full-Stack Web Development', 'Robert Garcia', 'Web Dev', '$59.99', '1,560', 'Published', 'course-3.svg'],
                            ['Human Anatomy Basics', 'Dr. Emily Chen', 'Medicine', '$39.99', '720', 'Draft', 'course-4.svg'],
                            ['Business Strategy 101', 'David Brown', 'Business', '$29.99', '890', 'Published', 'course-5.svg'],
                            ['UI/UX Design Principles', 'Lisa Anderson', 'Design', '$44.99', '650', 'Archived', 'course-6.svg'],
                        ];
                        foreach ($courses as $c): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><div class="user-cell"><img src="../../assets/images/course/<?php echo $c[7]; ?>" alt=""><strong><?php echo $c[0]; ?></strong></div></td>
                                <td><?php echo $c[1]; ?></td>
                                <td><span class="badge badge-primary"><?php echo $c[2]; ?></span></td>
                                <td><strong><?php echo $c[3]; ?></strong></td>
                                <td><?php echo $c[4]; ?></td>
                                <td><span class="status-pill <?php echo strtolower($c[5]) === 'published' ? 'approved' : (strtolower($c[5]) === 'draft' ? 'pending' : 'rejected'); ?>"><?php echo $c[5]; ?></span></td>
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
