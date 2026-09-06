<?php
$page_title = 'Home';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';

// Get featured courses
$stmt = $pdo->prepare("SELECT c.*, u.full_name as mentor_name, u.avatar 
                       FROM courses c 
                       JOIN users u ON c.mentor_id = u.id 
                       WHERE c.status = 'active' 
                       ORDER BY c.created_at DESC LIMIT 6");
$stmt->execute();
$featured_courses = $stmt->fetchAll();

// Get top mentors
$stmt = $pdo->prepare("SELECT u.*, COUNT(c.id) as course_count,
                       (SELECT AVG(rating) FROM ratings WHERE mentor_id = u.id) as avg_rating
                       FROM users u
                       LEFT JOIN courses c ON u.id = c.mentor_id AND c.status = 'active'
                       WHERE u.role = 'mentor' AND u.is_active = 1
                       GROUP BY u.id
                       ORDER BY avg_rating DESC LIMIT 4");
$stmt->execute();
$top_mentors = $stmt->fetchAll();

// Get featured academic fields
$stmt = $pdo->query("SELECT * FROM academic_fields WHERE is_active = 1 ORDER BY name LIMIT 6");
$academic_fields = $stmt->fetchAll();

// Get stats
$stmt = $pdo->query("SELECT COUNT(*) as total_courses FROM courses WHERE status = 'active'");
$total_courses = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as total_mentors FROM users WHERE role = 'mentor' AND is_active = 1");
$total_mentors = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as total_students FROM users WHERE role = 'fresher' AND is_active = 1");
$total_students = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as total_lessons FROM course_lessons WHERE is_published = 1");
$total_lessons = $stmt->fetchColumn();
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-modern">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6 hero-content">
                <h1 class="hero-title animate-fade-in-up">
                    Learn from <br><span class="highlight">Industry Experts</span>
                </h1>
                <p class="hero-subtitle animate-fade-in-up animate-delay-1">
                    Connect with experienced mentors, learn new skills, and accelerate your career growth with personalized guidance.
                </p>
                <div class="hero-buttons animate-fade-in-up animate-delay-2">
                    <?php if (isLoggedIn()): ?>
                        <?php if (isFresher()): ?>
                            <a href="fresher/dashboard.php" class="btn-hero-primary">
                                <i class="fas fa-rocket"></i> Go to Dashboard
                            </a>
                        <?php elseif (isMentor()): ?>
                            <a href="mentor/dashboard.php" class="btn-hero-primary">
                                <i class="fas fa-rocket"></i> Go to Dashboard
                            </a>
                        <?php elseif (isAdmin()): ?>
                            <a href="admin/dashboard.php" class="btn-hero-primary">
                                <i class="fas fa-rocket"></i> Go to Dashboard
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="register.php" class="btn-hero-primary">
                            <i class="fas fa-rocket"></i> Get Started Free
                        </a>
                        <a href="public/courses.php" class="btn-hero-secondary">
                            <i class="fas fa-play-circle"></i> Browse Courses
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hero-stats animate-fade-in-up animate-delay-3">
                    <div class="hero-stat">
                        <span class="hero-stat-number" id="statCourses"><?php echo $total_courses; ?></span>
                        <span class="hero-stat-label">Courses</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number"><?php echo $total_mentors; ?></span>
                        <span class="hero-stat-label">Mentors</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number"><?php echo $total_students; ?></span>
                        <span class="hero-stat-label">Students</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number"><?php echo $total_lessons; ?></span>
                        <span class="hero-stat-label">Lessons</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 hero-image animate-fade-in-up animate-delay-2">
                <div class="hero-visual" aria-label="Learning and mentorship illustration">
                    <div class="hero-visual-orbit orbit-one"></div>
                    <div class="hero-visual-orbit orbit-two"></div>
                    <div class="hero-visual-core"><i class="fas fa-graduation-cap"></i></div>
                    <div class="hero-visual-chip chip-one"><i class="fas fa-code"></i> Practice</div>
                    <div class="hero-visual-chip chip-two"><i class="fas fa-star"></i> Grow</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Academic Fields -->
<section class="section-modern fields-section">
    <div class="container">
        <div class="section-heading-row">
            <div>
                <span class="eyebrow">Explore your direction</span>
                <h2 class="section-title">Start with a field that fits you</h2>
                <p class="section-subtitle">Browse real learning paths shaped by the skills our community is building.</p>
            </div>
            <a href="public/academic-field.php" class="btn-modern btn-modern-outline">View fields <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row g-4">
            <?php if (!empty($academic_fields)): ?>
                <?php foreach ($academic_fields as $field): ?>
                <div class="col-md-6 col-lg-4">
                    <a class="field-card-modern" href="public/courses.php?field=<?php echo (int) $field['id']; ?>">
                        <span class="field-icon-modern"><i class="fas <?php echo htmlspecialchars($field['icon'] ?: 'fa-book'); ?>"></i></span>
                        <span class="field-card-copy"><strong><?php echo htmlspecialchars($field['name']); ?></strong><small><?php echo htmlspecialchars($field['description'] ?: 'Find courses and mentors in this field.'); ?></small></span>
                        <i class="fas fa-arrow-up-right-from-square field-arrow"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12"><div class="empty-state-modern">Academic fields will appear here as soon as they are added.</div></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section-modern">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Why Choose SkillShare Hub?</h2>
            <p class="section-subtitle">Learn from the best and accelerate your career with our modern learning platform</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 animate-fade-in-up animate-delay-1">
                <div class="feature-card-modern">
                    <div class="feature-icon-modern">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3 class="feature-title-modern">Expert Mentors</h3>
                    <p class="feature-desc-modern">Learn from industry professionals with years of real-world experience and expertise.</p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-in-up animate-delay-2">
                <div class="feature-card-modern">
                    <div class="feature-icon-modern" style="background: var(--gradient-success);">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3 class="feature-title-modern">Practical Learning</h3>
                    <p class="feature-desc-modern">Hands-on projects, real-world assignments, and interactive learning experiences.</p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-in-up animate-delay-3">
                <div class="feature-card-modern">
                    <div class="feature-icon-modern" style="background: var(--gradient-warning);">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3 class="feature-title-modern">Industry Certification</h3>
                    <p class="feature-desc-modern">Earn recognized certificates upon course completion to showcase your skills.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section class="section-modern" style="background: #f7fafc;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title" style="-webkit-text-fill-color: #2d3748; background: none;">Featured Courses</h2>
                <p class="text-muted">Explore our most popular courses</p>
            </div>
            <a href="public/courses.php" class="btn-modern btn-modern-outline">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($featured_courses)): ?>
                <?php foreach ($featured_courses as $course): ?>
                <div class="col-md-4 animate-fade-in-up">
                    <div class="course-card-modern">
                        <div class="course-image-modern">
                            <img src="<?php echo $course['thumbnail'] ?? 'assets/images/course-placeholder.jpg'; ?>" alt="<?php echo $course['title']; ?>">
                            <span class="course-badge-modern"><?php echo getCourseLevel($course['level']); ?></span>
                        </div>
                        <div class="course-body-modern">
                            <h3 class="course-title-modern"><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p class="course-meta-modern">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($course['mentor_name']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo $course['duration']; ?>h</span>
                            </p>
                            <p class="text-muted small"><?php echo truncateText($course['description'], 80); ?></p>
                        </div>
                        <div class="course-footer-modern">
                            <span class="course-price-modern">$<?php echo number_format($course['price'], 2); ?></span>
                            <a href="public/course-detail.php?id=<?php echo $course['id']; ?>" class="btn-modern btn-modern-primary">
                                Enroll Now
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No courses available yet. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Top Mentors -->
<section class="section-modern">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Top Mentors</h2>
            <p class="section-subtitle">Learn from the best in the industry</p>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($top_mentors)): ?>
                <?php foreach ($top_mentors as $mentor): ?>
                <div class="col-lg-3 col-md-6 animate-fade-in-up">
                    <div class="mentor-card-modern">
                        <img src="<?php echo getAvatar($mentor); ?>" alt="<?php echo $mentor['full_name']; ?>" class="mentor-avatar-modern">
                        <h3 class="mentor-name-modern"><?php echo htmlspecialchars($mentor['full_name']); ?></h3>
                        <p class="mentor-title-modern"><?php echo htmlspecialchars($mentor['title'] ?? 'Industry Expert'); ?></p>
                        <div class="mentor-stats-modern">
                            <div class="mentor-stat-modern">
                                <span class="mentor-stat-number-modern"><?php echo $mentor['course_count'] ?? 0; ?></span>
                                <span class="mentor-stat-label-modern">Courses</span>
                            </div>
                            <div class="mentor-stat-modern">
                                <span class="mentor-stat-number-modern"><?php echo number_format($mentor['avg_rating'] ?? 0, 1); ?></span>
                                <span class="mentor-stat-label-modern">Rating</span>
                            </div>
                        </div>
                        <a href="public/mentor-profile.php?id=<?php echo $mentor['id']; ?>" class="btn-modern btn-modern-outline w-100">
                            View Profile
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No mentors available yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="section-modern" style="background: var(--gradient-hero); color: white;">
    <div class="container text-center">
        <h2 style="font-family: var(--font-secondary); font-size: 2.5rem; font-weight: 800; margin-bottom: 16px;">
            Ready to Start Your Learning Journey?
        </h2>
        <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto 32px;">
            Join thousands of students and start learning from industry experts today.
        </p>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn-hero-primary" style="background: white; color: var(--primary);">
                <i class="fas fa-user-plus"></i> Create Free Account
            </a>
        <?php else: ?>
            <a href="<?php echo isFresher() ? 'fresher/dashboard.php' : (isMentor() ? 'mentor/dashboard.php' : 'admin/dashboard.php'); ?>" class="btn-hero-primary" style="background: white; color: var(--primary);">
                <i class="fas fa-rocket"></i> Go to Dashboard
            </a>
        <?php endif; ?>
    </div>
</section>

<!-- Footer -->
<footer class="footer-modern">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h4 class="footer-title-modern">
                    <i class="fas fa-graduation-cap" style="color: var(--primary);"></i> SkillShare Hub
                </h4>
                <p style="margin-bottom: 16px;">Connecting freshers with experienced mentors for skill development and career growth.</p>
                <div class="footer-social-modern">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h4 class="footer-title-modern">Quick Links</h4>
                <a href="about.php" class="footer-link-modern">About Us</a>
                <a href="contact.php" class="footer-link-modern">Contact</a>
                <a href="faq.php" class="footer-link-modern">FAQ</a>
                <a href="privacy-policy.php" class="footer-link-modern">Privacy Policy</a>
                <a href="terms.php" class="footer-link-modern">Terms of Service</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="footer-title-modern">For Learners</h4>
                <a href="public/courses.php" class="footer-link-modern">Courses</a>
                <a href="public/mentor.php" class="footer-link-modern">Mentors</a>
                <a href="public/academic-field.php" class="footer-link-modern">Academic Fields</a>
                <a href="fresher/sessions/index.php" class="footer-link-modern">Live Sessions</a>
            </div>
            <div class="col-lg-3 col-md-6">
                <h4 class="footer-title-modern">Newsletter</h4>
                <p style="margin-bottom: 12px;">Subscribe for updates and new courses.</p>
                <form action="#" method="POST" class="d-flex gap-2">
                    <input type="email" class="form-control-modern" placeholder="Your Email" style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: white;">
                    <button type="submit" class="btn-modern btn-modern-primary">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="footer-bottom-modern">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> SkillShare Hub. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar-glass');
    if (!navbar) return;
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Animated counter
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            start = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(start).toLocaleString();
    }, 16);
}

// Animate stats on load
document.addEventListener('DOMContentLoaded', function() {
    const statCourses = document.getElementById('statCourses');
    if (statCourses) {
        animateCounter(statCourses, <?php echo $total_courses; ?>);
    }
});
</script>
</body>
</html>