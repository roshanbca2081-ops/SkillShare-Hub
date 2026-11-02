<?php
$page_title = 'Home';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';

// Keep the landing statistics tied to current database records.
$stmt = $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'");
$total_courses = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mentor' AND is_active = 1");
$total_mentors = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'fresher' AND is_active = 1");
$total_students = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM certificates WHERE is_valid = 1");
$total_success_stories = (int) $stmt->fetchColumn();

$landing_stats = [
    ['value' => $total_courses, 'label' => 'Courses Available', 'icon' => 'fa-book-open', 'tone' => 'violet'],
    ['value' => $total_mentors, 'label' => 'Active Mentors', 'icon' => 'fa-users', 'tone' => 'green'],
    ['value' => $total_students, 'label' => 'Registered Students', 'icon' => 'fa-user-group', 'tone' => 'orange'],
    ['value' => $total_success_stories, 'label' => 'Success Stories', 'icon' => 'fa-trophy', 'tone' => 'purple'],
];
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<main class="landing-home">
    <section class="hero-modern landing-hero">
        <div class="landing-hero-glow landing-hero-glow-one" aria-hidden="true"></div>
        <div class="landing-hero-glow landing-hero-glow-two" aria-hidden="true"></div>
        <div class="container landing-hero-container">
            <div class="row align-items-center landing-hero-row">
                <div class="col-lg-6 hero-content landing-hero-copy">
                    <h1 class="hero-title landing-hero-title">Learn from<br><span class="highlight">Industry Experts</span></h1>
                    <p class="hero-subtitle landing-hero-subtitle">Connect with experienced mentors, get project support, and kickstart your career with SkillShare Hub.</p>
                    <div class="hero-buttons landing-hero-buttons">
                        <?php if (isLoggedIn()): ?>
                            <?php
                            $dashboardPath = isFresher()
                                ? 'fresher/dashboard.php'
                                : (isMentor() ? 'mentor/dashboard.php' : 'admin/dashboard.php');
                            ?>
                            <a href="<?php echo appUrl($dashboardPath); ?>" class="btn-hero-primary"><i class="fas fa-rocket" aria-hidden="true"></i> Go to Dashboard <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        <?php else: ?>
                            <a href="<?php echo appUrl('register.php'); ?>" class="btn-hero-primary"><i class="fas fa-rocket" aria-hidden="true"></i> Get Started Free <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        <?php endif; ?>
                        <a href="#why-choose" class="btn-hero-secondary"><i class="fas fa-circle-play" aria-hidden="true"></i> Explore More</a>
                    </div>
                </div>
                <div class="col-lg-6 landing-hero-art-wrap">
                    <div class="landing-hero-art" aria-label="Books, graduation cap, and laptop illustration">
                        <span class="landing-float-label landing-float-learn"><i class="fas fa-lightbulb" aria-hidden="true"></i> Learn</span>
                        <img src="<?php echo appUrl('assets/images/landing-education.svg'); ?>" alt="A graduation cap resting on books beside a laptop" class="landing-education-art">
                        <span class="landing-float-label landing-float-grow"><i class="fas fa-chart-line" aria-hidden="true"></i> Grow</span>
                        <span class="landing-art-caption">Build a Better Future</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="landing-hero-wave" aria-hidden="true"></div>
    </section>

    <section class="landing-stats-section" aria-label="SkillShare Hub community statistics">
        <div class="container">
            <div class="landing-stats-card">
                <?php foreach ($landing_stats as $index => $stat): ?>
                    <div class="landing-stat landing-stat-<?php echo htmlspecialchars($stat['tone']); ?>">
                        <span class="landing-stat-icon"><i class="fas <?php echo htmlspecialchars($stat['icon']); ?>" aria-hidden="true"></i></span>
                        <span class="landing-stat-copy">
                            <strong class="landing-stat-number" data-count="<?php echo (int) $stat['value']; ?>">0</strong>
                            <span class="landing-stat-label"><?php echo htmlspecialchars($stat['label']); ?></span>
                        </span>
                    </div>
                    <?php if ($index < count($landing_stats) - 1): ?><span class="landing-stat-divider" aria-hidden="true"></span><?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="landing-benefits-section" id="why-choose">
        <div class="container">
            <div class="landing-section-heading">
                <span class="landing-heading-rule" aria-hidden="true"></span>
                <h2>Why Choose SkillShare Hub?</h2>
                <span class="landing-heading-rule" aria-hidden="true"></span>
                <p>Your one-stop platform to learn, connect and grow.</p>
            </div>
            <div class="row g-3 landing-benefit-grid">
                <div class="col-sm-6 col-xl-3">
                    <article class="landing-benefit-card benefit-experts">
                        <span class="landing-benefit-icon"><i class="fas fa-graduation-cap" aria-hidden="true"></i></span>
                        <span><strong>Learn from Experts</strong><small>Gain practical skills from industry professionals.</small></span>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <article class="landing-benefit-card benefit-career">
                        <span class="landing-benefit-icon"><i class="fas fa-rocket" aria-hidden="true"></i></span>
                        <span><strong>Grow Your Career</strong><small>Get ready for internships, jobs and opportunities.</small></span>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <article class="landing-benefit-card benefit-peers">
                        <span class="landing-benefit-icon"><i class="fas fa-handshake-angle" aria-hidden="true"></i></span>
                        <span><strong>Connect with Peers</strong><small>Build a strong learning community.</small></span>
                    </article>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <article class="landing-benefit-card benefit-achieve">
                        <span class="landing-benefit-icon"><i class="fas fa-star" aria-hidden="true"></i></span>
                        <span><strong>Achieve More</strong><small>Turn your knowledge into success.</small></span>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/landing-footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo appUrl('assets/js/main.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const counters = document.querySelectorAll('.landing-stat-number');
    const duration = 1100;

    counters.forEach(function (counter) {
        const target = Number(counter.dataset.count || 0);
        const startTime = performance.now();

        function updateCounter(now) {
            const progress = Math.min((now - startTime) / duration, 1);
            const easedProgress = 1 - Math.pow(1 - progress, 3);
            counter.textContent = Math.floor(target * easedProgress).toLocaleString();
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        }

        requestAnimationFrame(updateCounter);
    });
});
</script>
</body>
</html>
