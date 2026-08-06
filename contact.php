<?php
session_start();
$navbar_active = 'Contact';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | SkillShare Hub</title>

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
                    <i class="fa-solid fa-envelope"></i> Get in Touch
                </span>
                <h1 style="color:#fff;font-size:var(--font-size-4xl);">Contact <span class="text-gradient-secondary">SkillShare Hub</span></h1>
                <p style="color:rgba(255,255,255,.7);margin:0;">We'd love to hear from you. Reach out anytime.</p>
            </div>
        </section>

        <section class="section-padding">
            <div class="container-max">
                <div class="contact-grid">
                    <div class="contact-info reveal">
                        <h3>Let's Start a <span class="text-gradient">Conversation</span></h3>
                        <p>Have questions about courses, mentorship, research, or partnerships? Our team is here to help.</p>

                        <div class="contact-item">
                            <div class="ci-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <h6>Visit Us</h6>
                                <p>123 Learning Avenue, Education City, EC 10001</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="ci-icon"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <h6>Call Us</h6>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="ci-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <h6>Email Us</h6>
                                <p>support@skillsharehub.com</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="ci-icon"><i class="fa-solid fa-clock"></i></div>
                            <div>
                                <h6>Working Hours</h6>
                                <p>Mon - Sat: 9:00 AM - 6:00 PM</p>
                            </div>
                        </div>

                        <div class="contact-social">
                            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        </div>
                    </div>

                    <div class="contact-form-wrap card-base reveal reveal-delay-2">
                        <h4>Send Us a <span class="text-gradient">Message</span></h4>
                        <form action="#" method="post" data-validate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Your Name</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" data-validate="required" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="email">Your Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" data-validate="required|email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="subject">Subject</label>
                                        <select class="form-control" id="subject" name="subject">
                                            <option value="general">General Inquiry</option>
                                            <option value="courses">Courses</option>
                                            <option value="mentorship">Mentorship</option>
                                            <option value="research">Research</option>
                                            <option value="billing">Billing</option>
                                            <option value="support">Technical Support</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="phone">Phone (optional)</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="+1 555 000 0000" data-validate="phone">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="message">Message</label>
                                        <textarea class="form-control" id="message" name="message" placeholder="Write your message here..." data-validate="required" required></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                                        <i class="fa-solid fa-paper-plane"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Map placeholder -->
        <section style="padding-bottom:var(--spacing-9);">
            <div class="container-max">
                <div class="map-placeholder" style="background:var(--gradient-dark);border-radius:var(--border-radius-xl);padding:var(--spacing-9);text-align:center;">
                    <i class="fa-solid fa-map-location-dot" style="font-size:3rem;color:var(--secondary);margin-bottom:var(--spacing-4);"></i>
                    <h5 style="color:#fff;margin-bottom:0.5rem;">Interactive Map</h5>
                    <p style="color:rgba(255,255,255,.6);margin:0;">We're located in the heart of Education City.</p>
                </div>
            </div>
        </section>

        <?php include 'frontend/components/footer.php'; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/assets/js/main.js"></script>
    <script src="frontend/assets/js/navbar.js"></script>
    <script src="frontend/assets/js/validation.js"></script>
    <script src="frontend/assets/js/animation.js"></script>

</body>

</html>
