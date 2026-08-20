<?php
/*
 * Shared Platform Footer - closes the main content wrap,
 * adds footer, toast container and shared JS.
 */
?>
<style><?php readfile(__DIR__ . '/../assets/css/figma-modules.css'); ?></style>
        </main>
    </div><!-- /.with-v-nav -->

<!-- ============================================
   FOOTER
   ============================================ -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="index.php" class="brand footer-brand-logo">
                    <img src="frontend/assets/images/logo/skillshare hub.png" alt="SkillShare Hub logo">
                    <span>SkillShare Hub</span>
                </a>
                <p>Bridging Education with Industry Through Practical Learning and Mentorship.</p>
                <div class="footer-social">
                    <a href="#" onclick="showToast('Info', 'Facebook page coming soon!', 'info')"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" onclick="showToast('Info', 'Twitter page coming soon!', 'info')"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" onclick="showToast('Info', 'LinkedIn page coming soon!', 'info')"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" onclick="showToast('Info', 'YouTube channel coming soon!', 'info')"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h6>Platform</h6>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="academic-fields.php">Academic Fields</a></li>
                    <li><a href="courses.php">Courses</a></li>
                    <li><a href="mentor.php">Mentors</a></li>
                    <li><a href="research.php">Research</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h6>Resources</h6>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                </ul>
            </div>
            <div class="footer-newsletter">
                <h6>Stay Updated</h6>
                <p>Subscribe to our newsletter for updates and news</p>
                <div class="input-group">
                    <input type="email" placeholder="Your email" aria-label="Email">
                    <button onclick="showToast('Success', 'Subscribed successfully!', 'success')"><i class="fa-solid fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> SkillShare Hub. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <span>Learn • Connect • Grow</span>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================
   TOAST CONTAINER
   ============================================ -->
<div class="toast-container" id="toastContainer"></div>

<!-- ============================================
   SHARED JAVASCRIPT
   ============================================ -->
<script src="frontend/assets/js/navbar.js"></script>
<script src="frontend/assets/js/platform.js"></script>
</body>
</html>
