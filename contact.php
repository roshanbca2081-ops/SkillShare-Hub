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
        ->minLength('subject', 3, 'Subject must be at least 3 characters.')
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

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="display-4 fw-bold text-center mb-4">Contact Us</h1>
            <p class="text-center text-muted mb-5">Have questions or feedback? We'd love to hear from you.</p>
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center p-4 h-100">
                        <i class="fas fa-map-marker-alt fs-1 text-primary mb-3"></i>
                        <h6>Address</h6>
                        <p class="text-muted small">123 Learning Street<br>Education City, EC 12345</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center p-4 h-100">
                        <i class="fas fa-envelope fs-1 text-primary mb-3"></i>
                        <h6>Email</h6>
                        <p class="text-muted small">support@skillsharehub.com<br>info@skillsharehub.com</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center p-4 h-100">
                        <i class="fas fa-phone fs-1 text-primary mb-3"></i>
                        <h6>Phone</h6>
                        <p class="text-muted small">+1 (555) 123-4567<br>+1 (555) 987-6543</p>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="mb-4">Send us a Message</h3>
                    <form method="POST" id="contactForm" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?php echo $_SESSION['old']['name'] ?? ''; ?>" required>
                                <div class="invalid-feedback" id="contactNameError"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo $_SESSION['old']['email'] ?? ''; ?>" required>
                                <div class="invalid-feedback" id="contactEmailError"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" 
                                       value="<?php echo $_SESSION['old']['subject'] ?? ''; ?>" required>
                                <div class="invalid-feedback" id="contactSubjectError"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea name="message" rows="5" class="form-control" required><?php echo $_SESSION['old']['message'] ?? ''; ?></textarea>
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
</div>

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

<?php include 'includes/footer.php'; ?>