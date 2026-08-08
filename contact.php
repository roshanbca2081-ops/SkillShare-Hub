<?php
session_start();
$page_title = 'Contact | SkillShare Hub';
$page_active = 'Contact';
include 'frontend/components/platform-header.php';
?>

<div class="container">
    <div class="academic-header reveal">
        <h1>Get in Touch</h1>
        <p>We'd love to hear from you. Reach out anytime!</p>
    </div>

    <div class="contact-grid">
        <div>
            <div class="contact-info-card reveal" style="margin-bottom:20px;">
                <i class="fa-solid fa-location-dot"></i>
                <h5>Our Location</h5>
                <p>123 Learning Avenue, Education City</p>
            </div>
            <div class="contact-info-card reveal" style="margin-bottom:20px;">
                <i class="fa-solid fa-phone"></i>
                <h5>Call Us</h5>
                <p>+1 (555) 123-4567</p>
            </div>
            <div class="contact-info-card reveal">
                <i class="fa-solid fa-envelope"></i>
                <h5>Email Us</h5>
                <p>support@skillsharehub.com</p>
            </div>
        </div>

        <div class="contact-form reveal">
            <div class="form-group">
                <label>Your Name</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fa-solid fa-user"></i></span>
                    <input type="text" placeholder="Enter your name">
                </div>
            </div>
            <div class="form-group">
                <label>Your Email</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" placeholder="Enter your email">
                </div>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fa-solid fa-tag"></i></span>
                    <input type="text" placeholder="Enter subject">
                </div>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea placeholder="Write your message here..."></textarea>
            </div>
            <button class="btn-primary" onclick="showToast('Success', 'Message sent successfully! We\'ll get back to you soon.', 'success')">
                <i class="fa-solid fa-paper-plane"></i> Send Message
            </button>
        </div>
    </div>
</div>

<?php include 'frontend/components/platform-footer.php'; ?>
