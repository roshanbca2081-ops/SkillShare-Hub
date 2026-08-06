<?php
// Why Choose Us Section
$features = isset($features) && is_array($features) ? $features : [
    ['icon' => 'fa-user-tie', 'title' => 'Expert Mentors', 'desc' => 'Learn directly from industry professionals and academic experts with years of real-world experience.'],
    ['icon' => 'fa-laptop-code', 'title' => 'Flexible Learning', 'desc' => 'Study at your own pace with recorded sessions, live classes, and downloadable resources.'],
    ['icon' => 'fa-certificate', 'title' => 'Verified Certification', 'desc' => 'Earn recognized certificates that add genuine value to your resume and professional profile.'],
    ['icon' => 'fa-headset', 'title' => '24/7 Support', 'desc' => 'Get round-the-clock assistance from our support team and active community of learners.'],
    ['icon' => 'fa-handshake', 'title' => 'Career Counseling', 'desc' => 'Landed interviews, resume reviews, and interview preparation from dedicated career coaches.'],
    ['icon' => 'fa-flask', 'title' => 'Research Guidance', 'desc' => 'Publish papers, work on projects, and build a strong academic profile with mentor support.'],
];
?>
<section class="section-padding" id="why-us" style="background:var(--primary-soft);">
    <div class="container-max">
        <div class="section-heading reveal">
            <span class="eyebrow"><i class="fa-solid fa-medal"></i> Why Choose Us</span>
            <h2>Why Students <span class="text-gradient">Love SkillShare Hub</span></h2>
            <p>We go beyond traditional learning to provide a complete ecosystem for your growth.</p>
        </div>

        <div class="grid-3">
            <?php foreach ($features as $f): ?>
                <div class="feature-card reveal" style="background:#fff;padding:var(--spacing-6);border-radius:var(--border-radius-lg);box-shadow:var(--shadow-sm);transition:var(--transition-base);">
                    <div class="feature-icon" style="background:var(--primary-soft);color:var(--primary);"><i class="fa-solid <?php echo $f['icon']; ?>"></i></div>
                    <h5><?php echo $f['title']; ?></h5>
                    <p style="color:var(--gray-500);font-size:var(--font-size-sm);margin:0;"><?php echo $f['desc']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .feature-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg) !important; }
    .feature-icon {
        width: 64px;
        height: 64px;
        border-radius: var(--border-radius-lg);
        background: var(--gradient-primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: var(--spacing-4);
        box-shadow: var(--shadow-primary);
    }
    .feature-card h5 { margin-bottom: 0.8rem; }
    .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--spacing-5); }
    @media (max-width: 991px) { .grid-3 { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .grid-3 { grid-template-columns: 1fr; } }
</style>
