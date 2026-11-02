<?php
$page_title = 'Contact Us';
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'config/functions.php';
require_once 'config/validation.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator
        ->required('name', 'Full name is required.')
        ->minLength('name', 2, 'Name must be at least 2 characters.')
        ->required('email', 'Email is required.')
        ->email('email', 'Please enter a valid email address.')
        ->required('subject', 'Subject is required.')
        ->minLength('subject', 7, 'Subject must be at least 7 characters.')
        ->required('message', 'Message is required.')
        ->minLength('message', 10, 'Message must be at least 10 characters.');
    
    if ($validator->passes()) {
        // Send email (in production)
        $_SESSION['alert'] = [
            'type' => 'success',
            'icon' => 'check-circle',
            'message' => 'Thank you for your message! We will get back to you soon.'
        ];
        redirect('contact.php');
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'icon' => 'exclamation-circle',
            'message' => $validator->errorsString()
        ];
        $_SESSION['old'] = $_POST;
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/alerts.php'; ?>

<main class="landing-home public-info-page">
    <section class="public-page-hero public-contact-hero">
        <div class="container public-page-hero-inner">
            <span class="public-page-kicker"><i class="fas fa-paper-plane" aria-hidden="true"></i> We're listening</span>
            <h1>Let’s start a<br><span>conversation.</span></h1>
            <p>Have a question, idea, or feedback? Send us a note and the SkillShare Hub team will be glad to hear from you.</p>
        </div>
        <div class="public-hero-orb public-hero-orb-one" aria-hidden="true"></div>
        <div class="public-hero-orb public-hero-orb-two" aria-hidden="true"></div>
    </section>

    <section class="public-contact-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="public-contact-intro">
                        <span class="public-section-kicker">Get in touch</span>
                        <h2>We’re here to help.</h2>
                        <p>Reach out about learning, mentorship, or anything else you’d like to share.</p>
                    </div>
            
            <div class="row g-3 mt-2">
                <div class="col-12">
                    <div class="public-contact-card">
                        <span class="public-contact-icon benefit-experts"><i class="fas fa-location-dot" aria-hidden="true"></i></span>
                        <div><h6>Address</h6><p>kavre<br>Banepa-5</p></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="public-contact-card">
                        <span class="public-contact-icon benefit-peers"><i class="fas fa-envelope" aria-hidden="true"></i></span>
                        <div><h6>Email</h6><p><a href="mailto:support@skillsharehub.com">skillsharehub123@gmail.com</a><br><a href="mailto:info@skillsharehub.com">info@skillsharehub.com</a></p></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="public-contact-card">
                        <span class="public-contact-icon benefit-career"><i class="fas fa-phone" aria-hidden="true"></i></span>
                        <div><h6>Phone</h6><p>9768438873<br>+9767650006</p></div>
                    </div>
                </div>
            </div>
                </div>
                <div class="col-lg-8">
                    <div class="public-contact-form-card">
                    <div class="public-contact-form-heading"><span class="public-contact-icon benefit-achieve"><i class="fas fa-message" aria-hidden="true"></i></span><div><h2>Send us a message</h2><p>Fill out the form below and we’ll get back to you.</p></div></div>
                    <form method="POST" id="contactForm" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" 
                                        value="<?php echo htmlspecialchars((string)($_SESSION['old']['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                <div class="invalid-feedback" id="contactNameError"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" 
                                        value="<?php echo htmlspecialchars((string)($_SESSION['old']['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                <div class="invalid-feedback" id="contactEmailError"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" 
                                        value="<?php echo htmlspecialchars((string)($_SESSION['old']['subject'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                                <div class="invalid-feedback" id="contactSubjectError"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea name="message" rows="5" class="form-control" required><?php echo htmlspecialchars((string)($_SESSION['old']['message'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
                                <div class="invalid-feedback" id="contactMessageError"></div>
                                <small class="text-muted" id="charCount">0 / 1000 characters</small>
                            </div>
                            <div class="col-12">
                                <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_KEY"></div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const messageField = document.querySelector('textarea[name="message"]');
    const charCount = document.getElementById('charCount');
    
    // Character counter
    messageField.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = `${count} / 1000 characters`;
        if (count > 1000) {
            charCount.classList.add('text-danger');
        } else {
            charCount.classList.remove('text-danger');
        }
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validate name
        const name = document.querySelector('input[name="name"]');
        if (!name.value.trim() || name.value.trim().length < 2) {
            name.classList.add('is-invalid');
            document.getElementById('contactNameError').textContent = 'Name must be at least 2 characters.';
            isValid = false;
        } else {
            name.classList.remove('is-invalid');
        }
        
        // Validate email
        const email = document.querySelector('input[name="email"]');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value.trim()) {
            email.classList.add('is-invalid');
            document.getElementById('contactEmailError').textContent = 'Email is required.';
            isValid = false;
        } else if (!emailRegex.test(email.value)) {
            email.classList.add('is-invalid');
            document.getElementById('contactEmailError').textContent = 'Please enter a valid email address.';
            isValid = false;
        } else {
            email.classList.remove('is-invalid');
        }
        
        // Validate subject
        const subject = document.querySelector('input[name="subject"]');
        if (!subject.value.trim() || subject.value.trim().length < 3) {
            subject.classList.add('is-invalid');
            document.getElementById('contactSubjectError').textContent = 'Subject must be at least 3 characters.';
            isValid = false;
        } else {
            subject.classList.remove('is-invalid');
        }
        
        // Validate message
        if (!messageField.value.trim() || messageField.value.trim().length < 10) {
            messageField.classList.add('is-invalid');
            document.getElementById('contactMessageError').textContent = 'Message must be at least 10 characters.';
            isValid = false;
        } else if (messageField.value.length > 1000) {
            messageField.classList.add('is-invalid');
            document.getElementById('contactMessageError').textContent = 'Message must not exceed 1000 characters.';
            isValid = false;
        } else {
            messageField.classList.remove('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

<?php include 'includes/landing-footer.php'; ?>