<?php
session_start();
$navbar_active = 'Contact';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms &amp; Conditions | SkillShare Hub</title>

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
                    <i class="fa-solid fa-file-contract"></i> Legal
                </span>
                <h1 style="color:#fff;font-size:var(--font-size-4xl);">Terms &amp; <span class="text-gradient-secondary">Conditions</span></h1>
                <p style="color:rgba(255,255,255,.7);margin:0;">Please read these terms carefully before using our platform.</p>
            </div>
        </section>

        <section class="section-padding">
            <div class="container-max">
                <div class="legal-content">
                    <div class="legal-meta">
                        <span><i class="fa-regular fa-calendar"></i> Last updated: January 2025</span>
                        <span><i class="fa-solid fa-file-contract"></i> Version 1.5</span>
                    </div>

                    <h3>1. Acceptance of Terms</h3>
                    <p>By accessing and using SkillShare Hub, you agree to comply with and be bound by these Terms and Conditions. If you do not agree, please do not use our services.</p>

                    <h3>2. User Accounts</h3>
                    <p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You must provide accurate and complete information.</p>

                    <h3>3. Courses &amp; Mentorship</h3>
                    <p>Enrollment in courses and mentorship programs grants you a personal, non-transferable license to access the content for your educational use. Redistribution of course materials is prohibited.</p>

                    <h3>4. Payments &amp; Refunds</h3>
                    <p>All payments are non-refundable unless otherwise specified. Refund requests must be submitted within 14 days of purchase and are subject to our refund policy.</p>

                    <h3>5. Acceptable Use</h3>
                    <p>You agree not to misuse the platform, including attempting to disrupt services, accessing other users' accounts, or engaging in any fraudulent or unlawful activity.</p>

                    <h3>6. Intellectual Property</h3>
                    <p>All content, logos, trademarks, and materials on SkillShare Hub are the property of SkillShare Hub or its licensors and are protected by intellectual property laws.</p>

                    <h3>7. Limitation of Liability</h3>
                    <p>SkillShare Hub is provided "as is" without warranties of any kind. We are not liable for any indirect, incidental, or consequential damages arising from your use of the platform.</p>

                    <h3>8. Changes to Terms</h3>
                    <p>We reserve the right to modify these terms at any time. Continued use of the platform after changes constitutes acceptance of the revised terms.</p>
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
