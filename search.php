<?php
session_start();
$navbar_active = 'Courses';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | SkillShare Hub</title>

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/main.css">
    <link rel="stylesheet" href="frontend/assets/css/navbar.css">
    <link rel="stylesheet" href="frontend/assets/css/footer.css">
    <link rel="stylesheet" href="frontend/assets/css/course.css">
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
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </span>
                <h1 style="color:#fff;font-size:var(--font-size-4xl);">Search <span class="text-gradient-secondary">Results</span></h1>
                <p style="color:rgba(255,255,255,.7);margin:0;">
                    Showing results for <strong style="color:#fff;">"<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"</strong>
                </p>
            </div>
        </section>

        <section class="section-padding">
            <div class="container-max">
                <?php include 'frontend/components/search-bar.php'; ?>

                <div class="course-toolbar reveal">
                    <div class="results-count"><strong><span>6</span></strong> results found</div>
                    <div class="toolbar-sort">
                        <label for="sortResults">Sort by:</label>
                        <select id="sortResults" class="filter-select">
                            <option value="relevance">Relevance</option>
                            <option value="rating">Highest Rated</option>
                            <option value="price-low">Price: Low to High</option>
                        </select>
                    </div>
                </div>

                <?php include 'frontend/components/course-card.php'; ?>

                <?php include 'frontend/components/pagination.php'; ?>
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
