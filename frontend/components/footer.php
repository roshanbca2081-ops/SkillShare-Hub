<?php
// Site Footer
$footer_visible = isset($footer_visible) ? $footer_visible : true;
if (!$footer_visible) return;
?>
<footer class="site-footer">
    <div class="footer-top">
        <div class="container-max">
            <div class="footer-grid">
                <!-- Brand -->
                <div class="footer-brand">
                    <div class="f-logo">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>SkillShare Hub</span>
                    </div>
                    <p>Empowering learners and mentors to share skills, grow together, and shape the future of education through a vibrant community.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="about.php"><i class="fa-solid fa-chevron-right"></i> About Us</a></li>
                        <li><a href="courses.php"><i class="fa-solid fa-chevron-right"></i> Courses</a></li>
                        <li><a href="mentor.php"><i class="fa-solid fa-chevron-right"></i> Our Mentors</a></li>
                        <li><a href="academic-filed.php"><i class="fa-solid fa-chevron-right"></i> Academic Fields</a></li>
                        <li><a href="research.php"><i class="fa-solid fa-chevron-right"></i> Research</a></li>
                        <li><a href="faq.php"><i class="fa-solid fa-chevron-right"></i> FAQ</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="contact.php"><i class="fa-solid fa-chevron-right"></i> Contact Us</a></li>
                        <li><a href="terms.php"><i class="fa-solid fa-chevron-right"></i> Terms &amp; Conditions</a></li>
                        <li><a href="privacy-policy.php"><i class="fa-solid fa-chevron-right"></i> Privacy Policy</a></li>
                        <li><a href="login.php"><i class="fa-solid fa-chevron-right"></i> Login</a></li>
                        <li><a href="register.php"><i class="fa-solid fa-chevron-right"></i> Register</a></li>
                        <li><a href="404.php"><i class="fa-solid fa-chevron-right"></i> 404 Page</a></li>
                    </ul>
                </div>

                <!-- Newsletter & Contact -->
                <div class="footer-col">
                    <h4>Stay Updated</h4>
                    <div class="footer-contact">
                        <ul>
                            <li><i class="fa-solid fa-location-dot"></i> 123 Learning Avenue, Education City</li>
                            <li><i class="fa-solid fa-phone"></i> +1 (555) 123-4567</li>
                            <li><i class="fa-solid fa-envelope"></i> support@skillsharehub.com</li>
                        </ul>
                    </div>
                    <div class="footer-newsletter">
                        <p style="font-size:.85rem;color:rgba(255,255,255,.6);">Subscribe for the latest courses &amp; tips</p>
                        <form action="#" method="post" id="newsletterForm">
                            <input type="email" placeholder="Your email address" required>
                            <button type="submit" aria-label="Subscribe"><i class="fa-solid fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container-max">
            <div class="footer-bottom-inner">
                <p>&copy; <?php echo date('Y'); ?> <a href="index.php">SkillShare Hub</a>. All rights reserved.</p>
                <div class="footer-links">
                    <a href="terms.php">Terms</a>
                    <a href="privacy-policy.php">Privacy</a>
                    <a href="faq.php">Help</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to top button -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<!-- Newsletter success toast -->
<div class="newsletter-toast" id="newsletterToast" style="display:none;position:fixed;bottom:2rem;left:50%;transform:translateX(-50%);background:#fff;box-shadow:var(--shadow-lg);border-radius:var(--border-radius-md);padding:.9rem 1.5rem;z-index:1100;border-left:4px solid var(--success);font-size:.9rem;font-weight:500;color:var(--gray-800);">
    <i class="fa-solid fa-check" style="color:var(--success);margin-right:.5rem;"></i> Subscribed successfully! Check your inbox.
</div>
