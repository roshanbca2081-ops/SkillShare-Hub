<?php
$assetBase = file_exists('assets/js/main.js') ? 'assets/' : (file_exists('../assets/js/main.js') ? '../assets/' : '../../assets/');
?>
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <!-- Brand Column -->
      <div class="footer-brand">
        <span class="site-logo" aria-hidden="true"></span>
        <p class="footer-desc">
          ShareSkill Hub connects learners and mentors across 50+ academic fields. Learn practical skills, book mentorship sessions, and advance your career.
        </p>
        <div class="footer-social">
          <a href="#" class="footer-social__icon" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="footer-social__icon" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="#" class="footer-social__icon" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="#" class="footer-social__icon" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="footer-social__icon" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
        </div>
      </div>

      <!-- Quick Links -->
      <div>
        <h4 class="footer-heading">Quick Links</h4>
        <nav class="footer-links">
          <a href="<?php echo $assetBase; ?>index.php">Home</a>
          <a href="<?php echo $assetBase; ?>course.php">Courses</a>
          <a href="<?php echo $assetBase; ?>mentor.php">Mentors</a>
          <a href="<?php echo $assetBase; ?>fresher/mentorship/book.php">Book Session</a>
          <a href="<?php echo $assetBase; ?>fresher/assignments/index.php">Assignments</a>
          <a href="<?php echo $assetBase; ?>fresher/research/index.php">Research</a>
        </nav>
      </div>

      <!-- Resources -->
      <div>
        <h4 class="footer-heading">Resources</h4>
        <nav class="footer-links">
          <a href="<?php echo $assetBase; ?>fresher/placement/index.php">Placement Prep</a>
          <a href="<?php echo $assetBase; ?>fresher/certificates/index.php">Certificates</a>
          <a href="<?php echo $assetBase; ?>fresher/interview/index.php">Interview Prep</a>
          <a href="<?php echo $assetBase; ?>fresher/practical-skills/index.php">Practical Skills</a>
          <a href="<?php echo $assetBase; ?>fresher/enrolled-courses/index.php">My Courses</a>
          <a href="<?php echo $assetBase; ?>about.php">About</a>
        </nav>
      </div>

      <!-- Newsletter & Contact -->
      <div class="footer-newsletter">
        <h4 class="footer-heading">Stay Updated</h4>
        <p>Get the latest updates on new courses, mentors, and platform features.</p>
        <div class="footer-form">
          <input type="email" placeholder="Your email address" aria-label="Email for newsletter" />
          <button class="btn btn--primary" type="button"><i class="fa-regular fa-paper-plane"></i></button>
        </div>
        <br>
        <p style="font-size:.82rem;color:var(--muted);">
          <i class="fa-regular fa-envelope"></i> hello@shareskillhub.dev<br>
          <i class="fa-regular fa-clock"></i> Mon-Fri 9AM-6PM
        </p>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; 2026 <span class="footer-tagline">ShareSkill Hub</span>. All rights reserved.</p>
      <div style="display:flex;gap:16px;">
        <a href="#" style="color:var(--muted);text-decoration:none;font-weight:600;">Privacy</a>
        <a href="#" style="color:var(--muted);text-decoration:none;font-weight:600;">Terms</a>
        <a href="#" style="color:var(--muted);text-decoration:none;font-weight:600;">Cookies</a>
      </div>
    </div>
  </div>
</footer>

<script src="<?php echo $assetBase; ?>js/main.js"></script>
<script src="<?php echo $assetBase; ?>js/app.js"></script>
<script src="<?php echo $assetBase; ?>js/premium-ui.js"></script>
</body>
</html>
