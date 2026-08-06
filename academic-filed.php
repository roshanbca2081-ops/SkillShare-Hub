<?php
session_start();
$navbar_active = 'Academic Fields';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Fields | SkillShare Hub</title>

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/main.css">
    <link rel="stylesheet" href="frontend/assets/css/navbar.css">
    <link rel="stylesheet" href="frontend/assets/css/footer.css">
    <link rel="stylesheet" href="frontend/assets/css/field.css">
    <link rel="stylesheet" href="frontend/assets/css/responsive.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <?php include 'frontend/components/loader.php'; ?>
    <?php include 'frontend/components/navbar.php'; ?>

    <div class="with-v-nav">

        <?php include 'frontend/components/header.php'; ?>

        <section class="page-banner" style="background:var(--gradient-dark);">
            <div class="container-max">
                <span class="eyebrow" style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.1);color:var(--secondary);padding:.4rem 1.2rem;border-radius:999px;font-weight:600;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:var(--spacing-4);">
                    <i class="fa-solid fa-layer-group"></i> Disciplines
                </span>
                <h1 style="color:#fff;font-size:var(--font-size-4xl);">Explore Academic <span class="text-gradient-secondary">Fields</span></h1>
                <p style="color:rgba(255,255,255,.7);margin:0;">Discover the right discipline for your career path.</p>
            </div>
        </section>

        <section class="section-padding">
            <div class="container-max">
                <?php include 'frontend/components/field-card.php'; ?>
            </div>
        </section>

        <section class="section-padding" style="background:var(--gray-100);">
            <div class="container-max">
                <div class="section-heading reveal">
                    <span class="eyebrow"><i class="fa-solid fa-compass"></i> Career Paths</span>
                    <h2>Popular <span class="text-gradient">Career Streams</span></h2>
                    <p>Take a closer look at the most sought-after career journeys.</p>
                </div>
                <div class="grid-2">
                    <?php
                    $paths = [
                        ['title' => 'Technology & Engineering', 'desc' => 'Software, AI, Data Science, Robotics and more. Perfect for logical thinkers and problem solvers.', 'icon' => 'fa-microchip', 'jobs' => '120K+ openings'],
                        ['title' => 'Medicine & Healthcare', 'desc' => 'Doctor, nursing, pharmacy, public health and biomedical research careers.', 'icon' => 'fa-stethoscope', 'jobs' => '85K+ openings'],
                        ['title' => 'Business & Finance', 'desc' => 'Management, marketing, accounting, banking and entrepreneurship pathways.', 'icon' => 'fa-briefcase', 'jobs' => '95K+ openings'],
                        ['title' => 'Creative Arts & Design', 'desc' => 'Graphic design, UX/UI, animation, media and entertainment industries.', 'icon' => 'fa-palette', 'jobs' => '45K+ openings'],
                    ];
                    foreach ($paths as $path): ?>
                        <div class="career-card card-base reveal">
                            <div class="career-icon"><i class="fa-solid <?php echo $path['icon']; ?>"></i></div>
                            <div>
                                <h5><?php echo $path['title']; ?></h5>
                                <p><?php echo $path['desc']; ?></p>
                                <span class="badge badge-success"><i class="fa-solid fa-chart-line"></i> <?php echo $path['jobs']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <?php include 'frontend/components/footer.php'; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/assets/js/main.js"></script>
    <script src="frontend/assets/js/navbar.js"></script>
    <script src="frontend/assets/js/animation.js"></script>

</body>

</html>
