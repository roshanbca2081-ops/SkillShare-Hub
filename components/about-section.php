<?php
// About Section for homepage
?>
<section class="section-padding" id="about">
    <div class="container-max">
        <div class="about-grid">
            <div class="about-visual reveal">
                <img src="frontend/assets/images/hero/about-illustration.svg" alt="About SkillShare Hub" class="about-img">
                <div class="about-exp-badge">
                    <h3>10+</h3>
                    <p>Years of<br>Excellence</p>
                </div>
            </div>
            <div class="about-content reveal reveal-delay-2">
                <span class="eyebrow"><i class="fa-solid fa-circle-info"></i> About Us</span>
                <h2>Building the Future of <span class="text-gradient">Education & Mentorship</span></h2>
                <div class="divider"></div>
                <p>
                    SkillShare Hub is a vibrant learning ecosystem where freshers, students, and professionals
                    connect with experienced mentors across <strong>engineering, medical, business, arts, and law</strong>.
                    We provide structured courses, personalized mentorship sessions, research guidance, and career development tools — all in one place.
                </p>
                <ul class="about-features">
                    <li><i class="fa-solid fa-circle-check"></i> Expert mentors from top industries & universities</li>
                    <li><i class="fa-solid fa-circle-check"></i> Structured learning paths for every field</li>
                    <li><i class="fa-solid fa-circle-check"></i> 1-on-1 mentorship & career coaching</li>
                    <li><i class="fa-solid fa-circle-check"></i> Research publication & internship support</li>
                </ul>
                <div class="about-actions">
                    <a href="about.php" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-right"></i> Learn More About Us
                    </a>
                    <a href="register.php" class="btn btn-outline">
                        <i class="fa-solid fa-user-graduate"></i> Become a Member
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .about-grid { display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: var(--spacing-8); }
    .about-visual { position: relative; }
    .about-img { border-radius: var(--border-radius-xl); box-shadow: var(--shadow-lg); width: 100%; }
    .about-exp-badge {
        position: absolute;
        bottom: -20px;
        right: 20px;
        background: var(--gradient-primary);
        color: #fff;
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-4) var(--spacing-5);
        text-align: center;
        box-shadow: var(--shadow-primary);
        animation: float 5s ease-in-out infinite;
    }
    .about-exp-badge h3 { color: #fff; margin: 0; font-size: var(--font-size-2xl); }
    .about-exp-badge p { margin: 0; font-size: var(--font-size-xs); opacity: 0.9; }
    .about-content .eyebrow { display: inline-flex; align-items: center; gap: 0.5rem; font-size: var(--font-size-sm); font-weight: 600; color: var(--primary); background: var(--primary-soft); padding: 0.4rem 1.2rem; border-radius: var(--border-radius-full); margin-bottom: var(--spacing-4); text-transform: uppercase; letter-spacing: 0.5px; }
    .about-content h2 { margin-bottom: var(--spacing-4); }
    .about-content p { color: var(--gray-600); }
    .about-features { margin: var(--spacing-5) 0; }
    .about-features li { display: flex; align-items: center; gap: 0.7rem; padding: 0.5rem 0; color: var(--gray-700); font-weight: 500; }
    .about-features li i { color: var(--success); }
    .about-actions { display: flex; gap: var(--spacing-4); flex-wrap: wrap; margin-top: var(--spacing-5); }
    @media (max-width: 991px) { .about-grid { grid-template-columns: 1fr; } }
</style>
