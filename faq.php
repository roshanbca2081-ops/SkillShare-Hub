<?php
session_start();
$navbar_active = 'Contact';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ | SkillShare Hub</title>

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/main.css">
    <link rel="stylesheet" href="frontend/assets/css/navbar.css">
    <link rel="stylesheet" href="frontend/assets/css/footer.css">
    <link rel="stylesheet" href="frontend/assets/css/contact.css">
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
                    <i class="fa-solid fa-circle-question"></i> Help Center
                </span>
                <h1 style="color:#fff;font-size:var(--font-size-4xl);">Frequently Asked <span class="text-gradient-secondary">Questions</span></h1>
                <p style="color:rgba(255,255,255,.7);margin:0;">Find quick answers to common queries.</p>
            </div>
        </section>

        <section class="section-padding">
            <div class="container-max">
                <div class="faq-layout">
                    <div class="faq-sidebar reveal">
                        <h5>Categories</h5>
                        <ul>
                            <li><a href="#" class="active"><i class="fa-solid fa-caret-right"></i> General</a></li>
                            <li><a href="#"><i class="fa-solid fa-caret-right"></i> Courses</a></li>
                            <li><a href="#"><i class="fa-solid fa-caret-right"></i> Mentorship</a></li>
                            <li><a href="#"><i class="fa-solid fa-caret-right"></i> Payments</a></li>
                            <li><a href="#"><i class="fa-solid fa-caret-right"></i> Account &amp; Billing</a></li>
                        </ul>
                        <div class="faq-help-box">
                            <i class="fa-solid fa-headset"></i>
                            <h5>Still have questions?</h5>
                            <p>Our support team is here to help.</p>
                            <a href="contact.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-envelope"></i> Contact Us</a>
                        </div>
                    </div>

                    <div class="faq-content reveal reveal-delay-2">
                        <div class="accordion" id="faqAccordion">
                            <?php
                            $faqs = [
                                ['q' => 'What is SkillShare Hub?', 'a' => 'SkillShare Hub is an online learning and mentorship platform that connects freshers and students with expert mentors across various academic fields including engineering, medicine, business, arts, and law.'],
                                ['q' => 'How do I enroll in a course?', 'a' => 'Simply create a free account, browse our courses catalog, and click "Enroll" on any course that interests you. You can start learning immediately after enrollment.'],
                                ['q' => 'Are the courses free?', 'a' => 'We offer a mix of free and premium courses. Premium courses provide advanced content, certificates, and direct mentor access. You can filter by price in our course catalog.'],
                                ['q' => 'How does mentorship work?', 'a' => 'After enrolling, you can book 1-on-1 sessions with your assigned mentor. Sessions can be conducted online via video call. You can book, reschedule, or cancel sessions from your dashboard.'],
                                ['q' => 'Can I become a mentor?', 'a' => 'Yes! If you have industry or academic expertise, you can apply to become a mentor. Once approved, you can create courses, host sessions, and earn income by sharing your knowledge.'],
                                ['q' => 'What payment methods are accepted?', 'a' => 'We accept all major credit/debit cards, PayPal, and bank transfers. All payments are processed securely through our encrypted payment gateway.'],
                                ['q' => 'Do I get a certificate after completing a course?', 'a' => 'Yes, completing premium courses earns you a verified certificate that you can showcase on LinkedIn and your resume.'],
                                ['q' => 'How can I get support?', 'a' => 'You can reach our support team via the Contact Us page, email at support@skillsharehub.com, or call +1 (555) 123-4567 during working hours.'],
                            ];
                            foreach ($faqs as $i => $faq): ?>
                                <div class="faq-item">
                                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?php echo $i; ?>">
                                        <span><?php echo $faq['q']; ?></span>
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                    <div id="faqCollapse<?php echo $i; ?>" class="accordion-collapse collapse <?php echo ($i === 0) ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                                        <div class="faq-answer">
                                            <p><?php echo $faq['a']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
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
