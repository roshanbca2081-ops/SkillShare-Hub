<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="mb-3"><i class="fas fa-graduation-cap"></i> SkillShare Hub</h5>
                <p>Connecting freshers with experienced mentors for skill development and career growth.</p>
                <div class="mt-3">
                    <a href="#" class="text-white-50 me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white-50 me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white-50 me-3"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-white-50"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo appUrl('about.php'); ?>" class="text-white-50 text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href="<?php echo appUrl('contact.php'); ?>" class="text-white-50 text-decoration-none">Contact</a></li>
                    <li class="mb-2"><a href="<?php echo appUrl('public/faq.php'); ?>" class="text-white-50 text-decoration-none">FAQ</a></li>
                    <li class="mb-2"><a href="<?php echo appUrl('public/privacy-policy.php'); ?>" class="text-white-50 text-decoration-none">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h5 class="mb-3">For Learners</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo appUrl('public/courses.php'); ?>" class="text-white-50 text-decoration-none">Courses</a></li>
                    <li class="mb-2"><a href="<?php echo appUrl('public/mentor.php'); ?>" class="text-white-50 text-decoration-none">Mentors</a></li>
                    <li class="mb-2"><a href="<?php echo appUrl('public/academic-field.php'); ?>" class="text-white-50 text-decoration-none">Fields</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4 mb-4">
                <h5 class="mb-3">Newsletter</h5>
                <p>Subscribe to get updates about new courses and features.</p>
                <form action="#" method="POST" class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="Your Email" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
        <hr>
        <p class="text-center mb-0">&copy; <?php echo date('Y'); ?> SkillShare Hub. All rights reserved.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo appUrl('assets/js/main.js'); ?>"></script>
<?php if (isset($page_js)): ?>
    <script src="<?php echo appUrl('assets/js/' . $page_js); ?>"></script>
<?php endif; ?>
</body>
</html>