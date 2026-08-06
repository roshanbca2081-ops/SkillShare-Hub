<?php
// Hero Section with interactive typed text
$typed_words = isset($typed_words) ? $typed_words : ['Skills', 'Courses', 'Research', 'Knowledge', 'Careers'];
?>
<section class="hero" id="home">
    <div class="container-max">
        <div class="hero-grid">
            <div class="hero-content reveal">
                <div class="hero-eyebrow">
                    <i class="fa-solid fa-bolt"></i> Learn. Share. Grow together.
                </div>
                <h1>
                    Unlock Your Potential with
                    <span class="typed-text" id="typedText"></span><span class="typing-cursor"></span>
                </h1>
                <p class="hero-desc">
                    SkillShare Hub connects aspiring freshers with expert mentors across academic fields — offering courses, mentorship, research guidance, and career growth opportunities.
                </p>
                <div class="hero-actions">
                    <a href="courses.php" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-rocket"></i> Explore Courses
                    </a>
                    <a href="register.php" class="btn btn-outline btn-lg">
                        <i class="fa-solid fa-user-plus"></i> Join as Fresher
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <h4>10K+</h4>
                        <p>Active Learners</p>
                    </div>
                    <div class="hero-stat">
                        <h4>500+</h4>
                        <p>Expert Mentors</p>
                    </div>
                    <div class="hero-stat">
                        <h4>120+</h4>
                        <p>Courses</p>
                    </div>
                </div>
            </div>

            <div class="hero-visual reveal reveal-delay-2">
                <div class="hero-float-card float-1">
                    <div class="fc-icon" style="background:var(--primary-soft);color:var(--primary);">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h6>10,000+ Students</h6>
                        <p>Learning today</p>
                    </div>
                </div>

                <div class="hero-img hero-img-placeholder">
                    <div class="hero-placeholder-shape hero-shape-1"></div>
                    <div class="hero-placeholder-shape hero-shape-2"></div>
                    <div class="hero-placeholder-shape hero-shape-3"></div>
                </div>

                <div class="hero-float-card float-2">
                    <div class="fc-icon" style="background:var(--secondary-soft);color:var(--secondary-dark);">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <h6>Certified Courses</h6>
                        <p>Verified completion</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
