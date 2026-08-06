<?php
session_start();
$navbar_active = 'Home';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillShare Hub | Learn. Share. Grow Together.</title>

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/main.css">
    <link rel="stylesheet" href="frontend/assets/css/navbar.css">
    <link rel="stylesheet" href="frontend/assets/css/footer.css">
    <link rel="stylesheet" href="frontend/assets/css/home.css">
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

        <?php include 'frontend/components/hero.php'; ?>
        <?php include 'frontend/components/statistics.php'; ?>
        <section class="section-padding" id="academic-fields">
            <div class="container-max">
                <div class="section-heading reveal">
                    <span class="eyebrow"><i class="fa-solid fa-layer-group"></i> Academic Fields</span>
                    <h2>Explore Diverse <span class="text-gradient">Academic Fields</span></h2>
                    <p>Discover the right path for your career across multiple disciplines guided by expert mentors.</p>
                </div>
                <?php include 'frontend/components/field-card.php'; ?>
            </div>
        </section>
        <?php include 'frontend/components/why-choose-us.php'; ?>
        <?php include 'frontend/components/research-section.php'; ?>
        <?php include 'frontend/components/cta.php'; ?>
        <?php include 'frontend/components/footer.php'; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/assets/js/main.js"></script>
    <script src="frontend/assets/js/navbar.js"></script>
    <script src="frontend/assets/js/home.js"></script>
    <script src="frontend/assets/js/animation.js"></script>

</body>

</html>
