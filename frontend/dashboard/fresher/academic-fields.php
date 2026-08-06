<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Academic Fields';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Fields | Fresher - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/field.css">
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
            <div class="panel-header"><h5><i class="fa-solid fa-layer-group" style="color:var(--primary);"></i> Explore Academic Fields</h5></div>
            <div class="dash-card-grid">
                <?php
                $fresher_fields = [
                    ['fa-microchip', 'Engineering & Technology', '84 courses', 'var(--gradient-primary)'],
                    ['fa-stethoscope', 'Medicine & Healthcare', '56 courses', 'var(--gradient-secondary)'],
                    ['fa-briefcase', 'Business & Finance', '62 courses', 'var(--gradient-accent)'],
                    ['fa-palette', 'Creative Arts & Design', '45 courses', '#fdcb6e'],
                    ['fa-scale-balanced', 'Law & Justice', '28 courses', '#a29bfe'],
                    ['fa-chalkboard-user', 'Education & Teaching', '38 courses', 'var(--gradient-secondary)'],
                ];
                foreach ($fresher_fields as $f): ?>
                    <div class="card-base field-card" style="padding:var(--spacing-5);text-align:center;background:linear-gradient(135deg,#fff,var(--gray-100));">
                        <div style="width:70px;height:70px;border-radius:50%;background:<?php echo $f[3]; ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.6rem;margin:0 auto var(--spacing-4);"><i class="fa-solid <?php echo $f[0]; ?>"></i></div>
                        <h6 style="margin-bottom:0.3rem;"><?php echo $f[1]; ?></h6>
                        <p style="font-size:.8rem;color:var(--gray-500);margin-bottom:var(--spacing-4);"><?php echo $f[2]; ?></p>
                        <button class="btn btn-outline btn-sm btn-block"><i class="fa-solid fa-arrow-right"></i> Explore</button>
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
