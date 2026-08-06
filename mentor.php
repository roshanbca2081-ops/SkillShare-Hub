<?php
session_start();
$navbar_active = 'Mentors';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentors | SkillShare Hub</title>

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/main.css">
    <link rel="stylesheet" href="frontend/assets/css/navbar.css">
    <link rel="stylesheet" href="frontend/assets/css/footer.css">
    <link rel="stylesheet" href="frontend/assets/css/mentor.css">
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
                    <i class="fa-solid fa-user-tie"></i> Our Mentors
                </span>
                <h1 style="color:#fff;font-size:var(--font-size-4xl);">Meet Our <span class="text-gradient-secondary">Expert Mentors</span></h1>
                <p style="color:rgba(255,255,255,.7);margin:0;">Learn directly from industry leaders and academic experts.</p>
            </div>
        </section>

        <section class="section-padding">
            <div class="container-max">
                <?php include 'frontend/components/search-bar.php'; ?>

                <div class="mentor-toolbar reveal">
                    <div class="results-count"><strong><span>6</span></strong> mentors available</div>
                    <div class="toolbar-sort">
                        <label for="sortMentors">Sort by:</label>
                        <select id="sortMentors" class="filter-select">
                            <option value="rating">Top Rated</option>
                            <option value="students">Most Students</option>
                            <option value="courses">Most Courses</option>
                        </select>
                    </div>
                </div>

                <?php include 'frontend/components/mentor-card.php'; ?>
            </div>
        </section>

        <!-- Become a mentor CTA -->
        <section class="section-padding" style="background:var(--primary-soft);">
            <div class="container-max">
                <div class="cta-box reveal" style="background:var(--gradient-secondary);border-radius:var(--border-radius-xl);padding:var(--spacing-8);text-align:center;box-shadow:var(--shadow-secondary);">
                    <h2 style="color:var(--dark);margin-bottom:var(--spacing-4);">Want to Become a <span class="text-gradient">Mentor?</span></h2>
                    <p style="color:rgba(30,30,46,.7);max-width:600px;margin:0 auto var(--spacing-6);">Share your knowledge, earn income, and make a real impact on the next generation of professionals.</p>
                    <a href="register.php" class="btn btn-primary btn-lg"><i class="fa-solid fa-user-plus"></i> Apply as Mentor</a>
                </div>
            </div>
        </section>

        <?php include 'frontend/components/footer.php'; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/assets/js/main.js"></script>
    <script src="frontend/assets/js/navbar.js"></script>
    <script src="frontend/assets/js/search.js"></script>
    <script src="frontend/assets/js/animation.js"></script>

</body>

</html>
