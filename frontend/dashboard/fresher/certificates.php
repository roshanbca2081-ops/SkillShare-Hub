<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Certificates';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates | Fresher - SkillShare Hub</title>
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

        <div class="dash-card-grid">
            <?php
            $fresher_certs = [
                ['Python for Beginners', 'Prof. James Carter', 'Jan 05, 2025', 'A+', 'intro-datascience.svg'],
                ['Data Science Fundamentals', 'Dr. Aisha Khan', 'Dec 28, 2024', 'A', 'ml-mastery.svg'],
                ['Statistics Essentials', 'Dr. Emily Chen', 'Dec 15, 2024', 'A-', 'statistics.svg'],
            ];
            foreach ($fresher_certs as $c): ?>
                <div class="card-base cert-card" style="padding:var(--spacing-5);text-align:center;background:linear-gradient(135deg,var(--primary-soft),#fff);">
                    <div style="width:70px;height:70px;border-radius:50%;background:var(--gradient-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto var(--spacing-4);"><i class="fa-solid fa-award"></i></div>
                    <h6 style="margin-bottom:0.3rem;"><?php echo $c[0]; ?></h6>
                    <p style="font-size:.8rem;color:var(--gray-500);margin-bottom:0.3rem;"><?php echo $c[1]; ?></p>
                    <p style="font-size:.8rem;color:var(--gray-400);margin-bottom:var(--spacing-4);">Issued: <?php echo $c[2]; ?></p>
                    <div style="display:flex;justify-content:center;gap:0.5rem;margin-bottom:var(--spacing-4);"><span class="badge badge-success">Grade <?php echo $c[3]; ?></span></div>
                    <div style="display:flex;gap:0.5rem;">
                        <button class="btn btn-primary btn-sm btn-block"><i class="fa-solid fa-download"></i> Download</button>
                        <button class="btn btn-outline btn-sm btn-block"><i class="fa-solid fa-eye"></i> View</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/navbar.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/animation.js"></script>
</body>

</html>
