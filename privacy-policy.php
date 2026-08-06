<?php
session_start();
$navbar_active = 'Contact';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | SkillShare Hub</title>

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
                    <i class="fa-solid fa-shield-halved"></i> Legal
                </span>
                <h1 style="color:#fff;font-size:var(--font-size-4xl);">Privacy <span class="text-gradient-secondary">Policy</span></h1>
                <p style="color:rgba(255,255,255,.7);margin:0;">How we collect, use, and protect your information.</p>
            </div>
        </section>

        <section class="section-padding">
            <div class="container-max">
                <div class="legal-content">
                    <div class="legal-meta">
                        <span><i class="fa-regular fa-calendar"></i> Last updated: January 2025</span>
                        <span><i class="fa-solid fa-file-shield"></i> Version 2.0</span>
                    </div>

                    <h3>1. Information We Collect</h3>
                    <p>We collect information you provide directly, including your name, email address, phone number, academic details, and payment information when you create an account, enroll in courses, or contact us.</p>

                    <h3>2. How We Use Your Information</h3>
                    <p>We use your information to provide and improve our services, process payments, personalize your learning experience, communicate with you, and ensure platform security.</p>

                    <h3>3. Data Security</h3>
                    <p>We implement industry-standard security measures including encryption, secure servers, and access controls to protect your personal information from unauthorized access or disclosure.</p>

                    <h3>4. Cookies</h3>
                    <p>We use cookies to enhance your browsing experience, remember your preferences, and analyze site traffic. You can control cookie settings through your browser.</p>

                    <h3>5. Third-Party Services</h3>
                    <p>We may share limited information with trusted third-party service providers who assist us in operating our platform, processing payments, and delivering courses, subject to confidentiality agreements.</p>

                    <h3>6. Your Rights</h3>
                    <p>You have the right to access, correct, update, or delete your personal information at any time. You may also opt out of marketing communications.</p>

                    <h3>7. Contact Us</h3>
                    <p>If you have questions about this Privacy Policy, please contact us at <a href="mailto:privacy@skillsharehub.com">privacy@skillsharehub.com</a> or call +1 (555) 123-4567.</p>
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
