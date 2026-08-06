<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Courses';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses | Fresher - SkillShare Hub</title>
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
            <div class="panel-header"><h5><i class="fa-solid fa-book-open" style="color:var(--primary);"></i> Available Courses</h5>
                <div class="dt-search" style="width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search courses..."></div>
            </div>
            <div class="dash-card-grid">
                <?php
                $fresher_courses = [
                    ['intro-datascience.svg', 'Data Science Fundamentals', 'Dr. Aisha Khan', '$49.99', '4.8', 'Beginner'],
                    ['ml-mastery.svg', 'Machine Learning Mastery', 'Prof. James Carter', '$79.99', '4.9', 'Advanced'],
                    ['web-dev.svg', 'Full-Stack Web Development', 'Robert Garcia', '$59.99', '4.7', 'Intermediate'],
                    ['python-basics.svg', 'Python for Beginners', 'Prof. James Carter', '$29.99', '4.6', 'Beginner'],
                    ['deep-learning.svg', 'Deep Learning Advanced', 'Dr. Aisha Khan', '$99.99', '4.8', 'Expert'],
                    ['statistics.svg', 'Statistics Essentials', 'Dr. Emily Chen', '$39.99', '4.5', 'Beginner'],
                ];
                foreach ($fresher_courses as $c): ?>
                    <div class="card-base course-card">
                        <div class="course-thumb" style="position:relative;height:150px;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;">
                            <img src="../../assets/images/course/<?php echo $c[0]; ?>" alt="" style="max-height:100px;">
                            <span class="badge badge-accent" style="position:absolute;top:10px;left:10px;"><?php echo $c[5]; ?></span>
                        </div>
                        <div class="course-card-body" style="padding:var(--spacing-4);">
                            <h6 style="margin-bottom:0.3rem;"><?php echo $c[1]; ?></h6>
                            <p style="font-size:.8rem;color:var(--gray-500);margin-bottom:0.6rem;"><?php echo $c[2]; ?></p>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.8rem;">
                                <span class="badge badge-primary"><i class="fa-solid fa-star"></i> <?php echo $c[4]; ?></span>
                                <strong style="color:var(--primary);"><?php echo $c[3]; ?></strong>
                            </div>
                            <button class="btn btn-primary btn-sm btn-block"><i class="fa-solid fa-cart-shopping"></i> Enroll Now</button>
                        </div>
                    </div>
                <?php endforeach; ?>
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
