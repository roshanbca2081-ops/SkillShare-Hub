<?php
session_start();
$navbar_active = 'Research';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research | SkillShare Hub</title>

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/main.css">
    <link rel="stylesheet" href="frontend/assets/css/navbar.css">
    <link rel="stylesheet" href="frontend/assets/css/footer.css">
    <link rel="stylesheet" href="frontend/assets/css/research.css">
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
                    <i class="fa-solid fa-flask"></i> Research Hub
                </span>
                <h1 style="color:#fff;font-size:var(--font-size-4xl);">Advance Your <span class="text-gradient-secondary">Research</span></h1>
                <p style="color:rgba(255,255,255,.7);margin:0;">From idea to publication — get the guidance you need.</p>
            </div>
        </section>

        <section class="section-padding">
            <div class="container-max">
                <?php include 'frontend/components/research-section.php'; ?>
            </div>
        </section>

        <section class="section-padding" style="background:var(--gray-100);">
            <div class="container-max">
                <div class="section-heading reveal">
                    <span class="eyebrow"><i class="fa-solid fa-file-lines"></i> Recent Publications</span>
                    <h2>Latest <span class="text-gradient">Research Papers</span></h2>
                    <p>Explore recent publications by our students and mentors.</p>
                </div>
                <div class="publication-list">
                    <?php
                    $papers = [
                        ['title' => 'Machine Learning Approaches for Early Disease Detection', 'author' => 'Dr. Aisha Khan', 'journal' => 'Journal of Medical Informatics', 'date' => 'Dec 2024', 'citations' => 128],
                        ['title' => 'Sustainable Energy Solutions for Urban Development', 'author' => 'Prof. James Carter', 'journal' => 'Renewable Energy Review', 'date' => 'Nov 2024', 'citations' => 94],
                        ['title' => 'The Impact of Digital Learning on Student Engagement', 'author' => 'Emily Chen', 'journal' => 'Educational Technology Research', 'date' => 'Oct 2024', 'citations' => 76],
                        ['title' => 'Blockchain Applications in Financial Security', 'author' => 'Robert Garcia', 'journal' => 'Finance & Technology Journal', 'date' => 'Sep 2024', 'citations' => 61],
                    ];
                    foreach ($papers as $paper): ?>
                        <div class="pub-card reveal">
                            <div class="pub-icon"><i class="fa-solid fa-file-arrow-down"></i></div>
                            <div class="pub-content">
                                <h5><?php echo $paper['title']; ?></h5>
                                <p>
                                    <span><i class="fa-solid fa-user"></i> <?php echo $paper['author']; ?></span>
                                    <span><i class="fa-solid fa-building-columns"></i> <?php echo $paper['journal']; ?></span>
                                    <span><i class="fa-regular fa-calendar"></i> <?php echo $paper['date']; ?></span>
                                </p>
                            </div>
                            <div class="pub-cite">
                                <strong><?php echo $paper['citations']; ?></strong>
                                <small>Citations</small>
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
