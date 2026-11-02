<?php
$page_title = 'About Us';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<main class="landing-home public-info-page">
    <section class="public-page-hero">
        <div class="container public-page-hero-inner">
            <span class="public-page-kicker"><i class="fas fa-sparkles" aria-hidden="true"></i> Our story</span>
            <h1>Learning grows<br><span>when we grow together.</span></h1>
            <p>SkillShare Hub brings aspiring learners and experienced professionals together to make meaningful career growth more accessible.</p>
            <a href="<?php echo appUrl('contact.php'); ?>" class="btn-hero-primary">Meet us halfway <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="public-hero-orb public-hero-orb-one" aria-hidden="true"></div>
        <div class="public-hero-orb public-hero-orb-two" aria-hidden="true"></div>
    </section>

    <section class="public-story-section">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-7">
                    <article class="public-story-card h-100">
                        <span class="public-section-kicker">Why we are here</span>
                        <h2>Knowledge should open doors for everyone.</h2>
                        <p>Our mission is to make quality, practical learning accessible by connecting freshers with people who have real-world experience and a willingness to share it.</p>
                        <p>From guided learning and project support to career conversations, we help turn curiosity into confidence and skills into new opportunities.</p>
                    </article>
                </div>
                <div class="col-lg-5">
                    <article class="public-story-card public-story-highlight h-100">
                        <span class="public-story-icon"><i class="fas fa-book-open" aria-hidden="true"></i></span>
                        <h3>Learn with purpose</h3>
                        <p>Build practical skills, get thoughtful guidance, and become part of a community that celebrates progress.</p>
                        <div class="public-story-signoff"><span>Learn</span><i class="fas fa-circle" aria-hidden="true"></i><span>Connect</span><i class="fas fa-circle" aria-hidden="true"></i><span>Grow</span></div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="public-benefits-section">
        <div class="container">
            <div class="landing-section-heading">
                <span class="landing-heading-rule" aria-hidden="true"></span>
                <h2>What makes us different?</h2>
                <span class="landing-heading-rule" aria-hidden="true"></span>
                <p>Support for every step of your learning journey.</p>
            </div>
            <div class="row g-3 landing-benefit-grid">
                <div class="col-sm-6 col-xl-3"><article class="landing-benefit-card benefit-experts"><span class="landing-benefit-icon"><i class="fas fa-user-tie" aria-hidden="true"></i></span><span><strong>Guidance from Experts</strong><small>Learn from professionals with firsthand experience.</small></span></article></div>
                <div class="col-sm-6 col-xl-3"><article class="landing-benefit-card benefit-career"><span class="landing-benefit-icon"><i class="fas fa-laptop-code" aria-hidden="true"></i></span><span><strong>Practical Learning</strong><small>Apply knowledge through projects and practice.</small></span></article></div>
                <div class="col-sm-6 col-xl-3"><article class="landing-benefit-card benefit-peers"><span class="landing-benefit-icon"><i class="fas fa-people-group" aria-hidden="true"></i></span><span><strong>Community</strong><small>Make connections and keep learning together.</small></span></article></div>
                <div class="col-sm-6 col-xl-3"><article class="landing-benefit-card benefit-achieve"><span class="landing-benefit-icon"><i class="fas fa-award" aria-hidden="true"></i></span><span><strong>Recognize Progress</strong><small>Celebrate achievements as you move forward.</small></span></article></div>
            </div>
        </div>
    </section>

    <section class="public-cta-section">
        <div class="container public-cta-card">
            <div><span class="public-section-kicker">Your next step starts here</span><h2>Ready to learn, connect and grow?</h2></div>
            <a href="<?php echo appUrl('register.php'); ?>" class="btn-hero-primary">Join SkillShare Hub <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/landing-footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo appUrl('assets/js/main.js'); ?>"></script>
</body>
</html>
