<?php
session_start();
$navbar_active = 'About';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | SkillShare Hub</title>

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/main.css">
    <link rel="stylesheet" href="frontend/assets/css/navbar.css">
    <link rel="stylesheet" href="frontend/assets/css/footer.css">
    <link rel="stylesheet" href="frontend/assets/css/about.css">
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

        <!-- Page Banner -->
        <section class="page-banner" style="background:var(--gradient-dark);">
            <div class="container-max">
                <span class="eyebrow" style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.1);color:var(--secondary);padding:.4rem 1.2rem;border-radius:999px;font-weight:600;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:var(--spacing-4);">
                    <i class="fa-solid fa-circle-info"></i> About Us
                </span>
                <h1 style="color:#fff;font-size:var(--font-size-4xl);">About <span class="text-gradient-secondary">SkillShare Hub</span></h1>
                <p style="color:rgba(255,255,255,.7);margin:0;">Empowering the next generation of learners and mentors</p>
            </div>
        </section>

        <section class="section-padding">
            <div class="container-max">
                <div class="about-page-grid">
                    <div class="reveal">
                        <span class="eyebrow" style="display:inline-flex;align-items:center;gap:.5rem;background:var(--primary-soft);color:var(--primary);padding:.4rem 1.2rem;border-radius:999px;font-weight:600;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;margin-bottom:var(--spacing-4);">
                            <i class="fa-solid fa-bullseye"></i> Who We Are
                        </span>
                        <h2>Our <span class="text-gradient">Mission & Vision</span></h2>
                        <div class="divider"></div>
                        <p>
                            At SkillShare Hub, we believe that every student deserves access to quality education,
                            expert mentorship, and real-world opportunities. Founded by educators and industry leaders,
                            our platform bridges the gap between academic learning and professional success.
                        </p>
                        <p>
                            We connect freshers with experienced mentors across engineering, medical, business, arts,
                            and law — providing structured courses, 1-on-1 coaching, research guidance, and career support
                            all in one vibrant community.
                        </p>
                        <div class="about-values">
                            <div class="value-item">
                                <i class="fa-solid fa-lightbulb"></i>
                                <div>
                                    <h6>Innovation</h6>
                                    <p>Modern teaching methods & tools</p>
                                </div>
                            </div>
                            <div class="value-item">
                                <i class="fa-solid fa-handshake"></i>
                                <div>
                                    <h6>Collaboration</h6>
                                    <p>Learning is a shared journey</p>
                                </div>
                            </div>
                            <div class="value-item">
                                <i class="fa-solid fa-shield-heart"></i>
                                <div>
                                    <h6>Excellence</h6>
                                    <p>Commitment to quality outcomes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="reveal reveal-delay-2">
                        <img src="frontend/assets/images/hero/about-illustration.svg" alt="About us" style="border-radius:var(--border-radius-xl);box-shadow:var(--shadow-lg);width:100%;">
                    </div>
                </div>
            </div>
        </section>

        <?php include 'frontend/components/statistics.php'; ?>

        <section class="section-padding" id="team" style="background:var(--gray-100);">
            <div class="container-max">
                <div class="section-heading reveal">
                    <span class="eyebrow"><i class="fa-solid fa-users"></i> Our Team</span>
                    <h2>Meet the <span class="text-gradient">Leadership</span></h2>
                    <p>The passionate people driving SkillShare Hub forward.</p>
                </div>
                <div class="grid-3">
                    <?php
                    $team = [
                        ['name' => 'Emma Stone', 'role' => 'Founder & CEO', 'img' => 'frontend/assets/images/mentor/mentor-1.svg'],
                        ['name' => 'Liam Johnson', 'role' => 'Chief Technology Officer', 'img' => 'frontend/assets/images/mentor/mentor-2.svg'],
                        ['name' => 'Olivia Davis', 'role' => 'Head of Mentorship', 'img' => 'frontend/assets/images/mentor/mentor-3.svg'],
                    ];
                    foreach ($team as $member): ?>
                        <div class="mentor-card reveal">
                            <div class="mentor-cover"></div>
                            <div class="mentor-avatar"><img src="<?php echo $member['img']; ?>" alt="<?php echo $member['name']; ?>"></div>
                            <div class="mentor-body">
                                <h5><?php echo $member['name']; ?></h5>
                                <p class="mentor-title"><?php echo $member['role']; ?></p>
                            </div>
                            <div class="mentor-actions" style="justify-content:center;padding:var(--spacing-4);">
                                <a href="#" class="btn btn-primary btn-sm"><i class="fa-brands fa-linkedin-in"></i> LinkedIn</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="section-padding">
            <div class="container-max">
                <div class="cta-box reveal" style="background:var(--gradient-primary);border-radius:var(--border-radius-xl);padding:var(--spacing-8);text-align:center;color:#fff;box-shadow:var(--shadow-primary);">
                    <h2 style="color:#fff;margin-bottom:var(--spacing-4);">Want to Be Part of Our <span style="color:var(--secondary);">Story?</span></h2>
                    <p style="color:rgba(255,255,255,.85);max-width:600px;margin:0 auto var(--spacing-6);">Join us as a learner, mentor, or partner and help us shape the future of education.</p>
                    <div style="display:flex;gap:var(--spacing-4);justify-content:center;flex-wrap:wrap;">
                        <a href="register.php" class="btn btn-light btn-lg"><i class="fa-solid fa-user-plus"></i> Join Us</a>
                        <a href="contact.php" class="btn btn-ghost btn-lg"><i class="fa-solid fa-envelope"></i> Get in Touch</a>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'frontend/components/footer.php'; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/assets/js/main.js"></script>
    <script src="frontend/assets/js/navbar.js"></script>
    <script src="frontend/assets/js/home.js"></script>
    <script src="frontend/assets/js/animation.js"></script>

</body>

</html>
