<?php
$page_title = 'About Us';
require_once 'config/session.php';
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="display-4 fw-bold text-center mb-4">About SkillShare Hub</h1>
            <p class="lead text-center text-muted mb-5">Bridging the gap between freshers and industry experts</p>
            
            <div class="mb-5">
                <h3>Our Mission</h3>
                <p>At SkillShare Hub, our mission is to democratize education and make quality learning accessible to everyone. We believe that everyone deserves an opportunity to learn from the best minds in the industry.</p>
            </div>
            
            <div class="mb-5">
                <h3>What We Do</h3>
                <p>We connect freshers with experienced mentors across various domains. Our platform offers:</p>
                <ul>
                    <li>Live interactive sessions with industry experts</li>
                    <li>Self-paced courses with practical projects</li>
                    <li>One-on-one mentorship opportunities</li>
                    <li>Research collaboration with experienced researchers</li>
                    <li>Interview preparation and career guidance</li>
                </ul>
            </div>
            
            <div class="mb-5">
                <h3>Why Choose Us</h3>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm text-center p-3">
                            <i class="fas fa-users fs-1 text-primary mb-2"></i>
                            <h5>Expert Mentors</h5>
                            <p class="text-muted small">Learn from professionals with real-world experience</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm text-center p-3">
                            <i class="fas fa-laptop fs-1 text-success mb-2"></i>
                            <h5>Practical Approach</h5>
                            <p class="text-muted small">Hands-on learning with real projects</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm text-center p-3">
                            <i class="fas fa-certificate fs-1 text-info mb-2"></i>
                            <h5>Recognized Certificates</h5>
                            <p class="text-muted small">Earn certificates valued by employers</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h3>Our Team</h3>
                <p>We are a team of passionate educators, developers, and designers committed to creating the best learning experience for our users.</p>
                <div class="row g-3">
                    <div class="col-md-3 col-6 text-center">
                        <div class="team-avatar-modern mb-2">JD</div>
                        <h6 class="mb-0">John Doe</h6>
                        <small class="text-muted">Founder & CEO</small>
                    </div>
                    <div class="col-md-3 col-6 text-center">
                        <div class="team-avatar-modern mb-2">JS</div>
                        <h6 class="mb-0">Jane Smith</h6>
                        <small class="text-muted">CTO</small>
                    </div>
                    <div class="col-md-3 col-6 text-center">
                        <div class="team-avatar-modern mb-2">MJ</div>
                        <h6 class="mb-0">Mike Johnson</h6>
                        <small class="text-muted">Head of Mentorship</small>
                    </div>
                    <div class="col-md-3 col-6 text-center">
                        <div class="team-avatar-modern mb-2">SW</div>
                        <h6 class="mb-0">Sarah Wilson</h6>
                        <small class="text-muted">Curriculum Designer</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>