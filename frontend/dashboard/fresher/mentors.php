<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Mentors';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentors | Fresher - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/mentor.css">
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
            <div class="panel-header"><h5><i class="fa-solid fa-user-tie" style="color:var(--primary);"></i> Browse Mentors</h5>
                <div class="dt-search" style="width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search mentors..."></div>
            </div>
            <div class="dash-card-grid">
                <?php
                $fresher_mentors = [
                    ['avatar-2.svg', 'Dr. Aisha Khan', 'Data Science', '4.9', '240 students', 'Available'],
                    ['avatar-3.svg', 'Prof. James Carter', 'Machine Learning', '4.8', '180 students', 'Available'],
                    ['avatar-4.svg', 'Robert Garcia', 'Web Development', '4.7', '320 students', 'Busy'],
                    ['avatar-5.svg', 'Dr. Emily Chen', 'Medicine', '4.9', '150 students', 'Available'],
                    ['avatar-6.svg', 'David Brown', 'Business', '4.6', '200 students', 'Available'],
                    ['avatar-1.svg', 'Lisa Anderson', 'Design', '4.8', '175 students', 'Busy'],
                ];
                foreach ($fresher_mentors as $m): ?>
                    <div class="card-base mentor-card" style="text-align:center;padding:var(--spacing-5);">
                        <div style="position:relative;width:90px;height:90px;margin:0 auto var(--spacing-4);">
                            <img src="../../assets/images/profile/<?php echo $m[0]; ?>" alt="" style="width:90px;height:90px;border-radius:50%;border:4px solid var(--primary-soft);">
                            <span style="position:absolute;bottom:4px;right:4px;width:14px;height:14px;border-radius:50%;background:<?php echo $m[6] === 'Available' ? 'var(--success)' : 'var(--warning)'; ?>;border:2px solid #fff;"></span>
                        </div>
                        <h6 style="margin-bottom:0.2rem;"><?php echo $m[1]; ?></h6>
                        <p style="font-size:.8rem;color:var(--gray-500);margin-bottom:0.6rem;"><?php echo $m[2]; ?></p>
                        <div style="display:flex;justify-content:center;gap:0.5rem;margin-bottom:var(--spacing-4);flex-wrap:wrap;">
                            <span class="badge badge-warning"><i class="fa-solid fa-star"></i> <?php echo $m[3]; ?></span>
                            <span class="badge badge-secondary"><?php echo $m[4]; ?></span>
                        </div>
                        <button class="btn btn-primary btn-sm btn-block"><i class="fa-solid fa-calendar-check"></i> Book Session</button>
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
