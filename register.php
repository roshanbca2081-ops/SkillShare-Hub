<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillShare Hub - Login & Register</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* (Same inline CSS as in login.php) */
        :root{--primary-400:#60a5fa;--primary-500:#3b82f6;--primary-600:#2563eb;--secondary-400:#a78bfa;--secondary-500:#8b5cf6;--success:#22c55e;--danger:#ef4444;--warning:#f59e0b;--text-primary:#ffffff;--text-secondary:rgba(255,255,255,0.8);--text-muted:rgba(255,255,255,0.4);--glass-bg:rgba(255,255,255,0.05);--glass-border:rgba(255,255,255,0.1);--shadow-lg:0 8px 40px rgba(0,0,0,0.4);--radius-md:12px;--radius-lg:16px;--radius-xl:20px;--radius-full:50px;--transition-bounce:0.4s cubic-bezier(0.175,0.885,0.32,1.275);--gradient-primary:linear-gradient(135deg,#3b82f6,#8b5cf6);--gradient-hero:linear-gradient(135deg,#ffffff 0%,#60a5fa 50%,#a78bfa 100%);--font-heading:'Poppins',sans-serif;--font-primary:'Inter',sans-serif}*{margin:0;padding:0;box-sizing:border-box}body{font-family:var(--font-primary);background:linear-gradient(135deg,#0a0a1a 0%,#1a1a2e 25%,#16213e 50%,#0f3460 75%,#1a1a2e 100%);background-attachment:fixed;min-height:100vh;color:var(--text-primary);overflow-x:hidden;line-height:1.6}::-webkit-scrollbar{width:6px;height:6px}::-webkit-scrollbar-track{background:rgba(255,255,255,0.05);border-radius:10px}::-webkit-scrollbar-thumb{background:var(--gradient-primary);border-radius:10px}.bg-animated{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}.bg-animated::before{content:'';position:absolute;inset:-50%;background:radial-gradient(ellipse at 20% 50%,rgba(59,130,246,0.12) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(139,92,246,0.12) 0%,transparent 50%),radial-gradient(ellipse at 50% 80%,rgba(6,182,212,0.06) 0%,transparent 50%);animation:bgShift 20s ease-in-out infinite alternate}@keyframes bgShift{0%{transform:translate(0,0) scale(1) rotate(0deg)}50%{transform:translate(5%,-5%) scale(1.05) rotate(2deg)}100%{transform:translate(-5%,5%) scale(0.95) rotate(-2deg)}}.floating-logo{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);font-size:15rem;font-weight:900;font-family:var(--font-heading);color:rgba(255,255,255,0.02);pointer-events:none;z-index:0;letter-spacing:10px;animation:floatLogo 25s ease-in-out infinite;user-select:none;white-space:nowrap}@keyframes floatLogo{0%,100%{transform:translate(-50%,-50%) scale(1) rotate(0deg)}25%{transform:translate(-50%,-55%) scale(1.02) rotate(1deg)}75%{transform:translate(-50%,-45%) scale(0.98) rotate(-1deg)}}.auth-container{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;position:relative;z-index:1}.auth-wrapper{width:100%;max-width:480px;animation:fadeInUp 0.6s ease}@keyframes fadeInUp{from{opacity:0;transform:translateY(40px) scale(0.95)}to{opacity:1;transform:translateY(0) scale(1)}}.auth-card{background:rgba(255,255,255,0.04);backdrop-filter:blur(30px);-webkit-backdrop-filter:blur(30px);border:1px solid rgba(255,255,255,0.08);border-radius:var(--radius-xl);padding:40px;box-shadow:0 40px 80px rgba(0,0,0,0.4);transition:var(--transition-bounce)}.auth-card:hover{border-color:rgba(255,255,255,0.12)}.auth-brand{text-align:center;margin-bottom:32px}.auth-brand .logo-icon{font-size:3.5rem;background:var(--gradient-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}.auth-brand h1{font-family:var(--font-heading);font-weight:800;font-size:2rem;background:var(--gradient-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin:8px 0 4px}.auth-brand p{color:var(--text-secondary);font-weight:300;font-size:0.95rem}.auth-brand .tagline{display:flex;justify-content:center;gap:12px;color:var(--text-muted);font-size:0.7rem;letter-spacing:1px;margin-top:4px}.form-group{margin-bottom:18px}.form-group label{display:block;color:var(--text-secondary);font-weight:500;font-size:0.85rem;margin-bottom:6px}.form-group .input-wrapper{position:relative;display:flex;align-items:center;background:rgba(255,255,255,0.06);border:1px solid var(--glass-border);border-radius:var(--radius-md);transition:all 0.3s ease}.form-group .input-wrapper:focus-within{border-color:var(--primary-500);box-shadow:0 0 0 4px rgba(59,130,246,0.1);background:rgba(255,255,255,0.08)}.form-group .input-wrapper .input-icon{padding:0 14px;color:var(--text-muted);font-size:0.95rem;flex-shrink:0}.form-group .input-wrapper input,.form-group .input-wrapper select,.form-group .input-wrapper textarea{width:100%;padding:12px 14px 12px 0;background:transparent;border:none;color:var(--text-primary);font-size:0.95rem;font-family:var(--font-primary);outline:none}.form-group .input-wrapper input::placeholder,.form-group .input-wrapper textarea::placeholder{color:var(--text-muted)}.form-group .input-wrapper select{appearance:none;cursor:pointer}.form-group .input-wrapper select option{background:#1a1a2e;color:var(--text-primary)}.form-group .input-wrapper textarea{resize:vertical;min-height:60px;padding-top:12px}.form-group .input-wrapper .toggle-password{padding:0 14px;color:var(--text-muted);cursor:pointer;transition:all 0.3s ease;flex-shrink:0}.form-group .input-wrapper .toggle-password:hover{color:var(--text-primary)}.password-strength{height:4px;border-radius:var(--radius-full);background:rgba(255,255,255,0.06);margin-top:8px;overflow:hidden}.password-strength .strength-bar{height:100%;border-radius:var(--radius-full);transition:width 0.3s ease,background 0.3s ease;width:0%}.password-strength .strength-bar.weak{width:25%;background:var(--danger)}.password-strength .strength-bar.fair{width:50%;background:var(--warning)}.password-strength .strength-bar.good{width:75%;background:var(--primary-500)}.password-strength .strength-bar.strong{width:100%;background:var(--success)}.password-strength-text{font-size:0.7rem;margin-top:4px;color:var(--text-muted)}.password-strength-text.weak{color:var(--danger)}.password-strength-text.fair{color:var(--warning)}.password-strength-text.good{color:var(--primary-400)}.password-strength-text.strong{color:var(--success)}.form-options{display:flex;justify-content:space-between;align-items:center;margin:16px 0 20px}.form-options .checkbox-label{color:var(--text-secondary);font-size:0.9rem;display:flex;align-items:center;gap:8px;cursor:pointer}.form-options .checkbox-label input[type="checkbox"]{width:16px;height:16px;accent-color:var(--primary-500);cursor:pointer}.form-options .forgot-link{color:var(--text-muted);text-decoration:none;font-size:0.85rem;transition:all 0.3s ease}.form-options .forgot-link:hover{color:var(--primary-400)}.btn-primary{width:100%;padding:14px;border:none;border-radius:var(--radius-md);background:var(--gradient-primary);color:#fff;font-weight:600;font-size:1rem;font-family:var(--font-primary);cursor:pointer;transition:all 0.3s ease}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(59,130,246,0.3)}.btn-primary:active{transform:scale(0.98)}.auth-divider{display:flex;align-items:center;gap:16px;margin:24px 0;color:var(--text-muted);font-size:0.8rem}.auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:var(--glass-border)}.social-login{display:flex;flex-direction:column;gap:10px}.btn-social{display:flex;align-items:center;justify-content:center;gap:12px;width:100%;padding:12px;border-radius:var(--radius-md);border:1px solid var(--glass-border);background:var(--glass-bg);color:var(--text-secondary);font-weight:500;font-size:0.95rem;cursor:pointer;transition:all 0.3s ease;text-decoration:none}.btn-social:hover{transform:translateY(-2px);background:rgba(255,255,255,0.06);color:var(--text-primary)}.btn-social .social-icon{font-size:1.2rem;width:24px;text-align:center}.btn-social.google:hover{border-color:#ea4335;background:rgba(234,67,53,0.08)}.btn-social.facebook:hover{border-color:#1877f2;background:rgba(24,119,242,0.08)}.auth-footer{text-align:center;margin-top:20px;color:var(--text-secondary);font-size:0.9rem}.auth-footer a{color:var(--primary-400);text-decoration:none;font-weight:600;transition:all 0.3s ease}.auth-footer a:hover{color:var(--primary-300);text-decoration:underline}.alert{padding:12px 16px;border-radius:var(--radius-md);font-size:0.9rem;margin-bottom:16px;display:flex;align-items:center;gap:10px;animation:shake 0.5s ease}.alert i{font-size:1.1rem;flex-shrink:0}.alert-danger{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#fca5a5}.alert-success{background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);color:#86efac}@keyframes shake{0%,100%{transform:translateX(0)}20%{transform:translateX(-8px)}40%{transform:translateX(8px)}60%{transform:translateX(-8px)}80%{transform:translateX(8px)}}.toast-container{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;max-width:400px;width:100%}.toast{background:rgba(20,20,40,0.95);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:var(--radius-md);padding:16px 20px;color:var(--text-primary);box-shadow:var(--shadow-lg);animation:slideInRight 0.5s ease;display:flex;align-items:center;gap:12px}.toast.success{border-left:4px solid var(--success)}.toast.error{border-left:4px solid var(--danger)}.toast.warning{border-left:4px solid var(--warning)}.toast.info{border-left:4px solid var(--primary-500)}.toast .icon{font-size:1.3rem;flex-shrink:0}.toast .content{flex:1}.toast .title{font-weight:600;font-size:0.9rem}.toast .message{font-size:0.8rem;color:var(--text-secondary)}.toast .close{cursor:pointer;color:var(--text-muted);background:none;border:none;font-size:1.1rem;padding:4px}.toast .close:hover{color:var(--text-primary)}@keyframes slideInRight{from{opacity:0;transform:translateX(100px)}to{opacity:1;transform:translateX(0)}}@media (max-width:768px){.auth-card{padding:28px 20px}.auth-brand .logo-icon{font-size:2.8rem}.auth-brand h1{font-size:1.6rem}.floating-logo{font-size:8rem}.floating-icons .icon{display:none}}@media (max-width:480px){.auth-container{padding:20px 12px}.auth-card{padding:20px 16px;border-radius:var(--radius-lg)}.form-group .input-wrapper input,.form-group .input-wrapper select{font-size:16px;padding:10px 12px 10px 0}.form-options{flex-direction:column;align-items:flex-start;gap:8px}.toast-container{right:10px;left:10px;max-width:100%}.auth-brand .tagline{font-size:0.65rem}}
    </style>
</head>
<body>

<div class="bg-animated"></div>
<div class="floating-logo">SkillShare Hub</div>



<div class="toast-container" id="toastContainer"></div>

<div class="auth-container">
    <div class="auth-wrapper">

        <!-- LOGIN FORM (hidden on register.php) -->
        <div id="loginForm" class="auth-card" style="display:none;">
            <div class="auth-brand">
            <div class="logo-icon">
                <img src="frontend/assets/images/logo/skillshare hub.png" alt="SkillShare Hub Logo" style="max-width:120px; height:auto;">
            </div>
                <h1>SkillShare Hub</h1>
                <p>Bridging Education with Industry</p>
                <div class="tagline">
                    <span>Learn</span>
                    <span>•</span>
                    <span>Connect</span>
                    <span>•</span>
                    <span>Grow</span>
                </div>
            </div>

            <div id="loginAlert"></div>

            <form id="loginFormElement" onsubmit="return handleLogin(event)">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="loginEmail" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="loginPassword" placeholder="Enter your password" required>
                        <span class="toggle-password" onclick="togglePassword('loginPassword', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox"> Remember Me
                    </label>
                    <a href="#" class="forgot-link" onclick="showToast('Info', 'Password reset link sent to your email!', 'info')">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="auth-divider"><span>OR</span></div>

            <div class="social-login">
                <button type="button" class="btn-social google" onclick="handleGoogleLogin()">
                    <span class="social-icon"><i class="fab fa-google"></i></span>
                    Continue with Google
                </button>
                <button type="button" class="btn-social facebook" onclick="handleFacebookLogin()">
                    <span class="social-icon"><i class="fab fa-facebook-f"></i></span>
                    Continue with Facebook
                </button>
            </div>

            <div class="auth-footer">
                Don't have an account? <a href="#" onclick="showRegister()">Create Account</a>
            </div>
        </div>

        <!-- REGISTER FORM (visible on this page) -->
        <div id="registerForm" class="auth-card" style="display:block;">
            <div class="auth-brand">
                <div class="logo-icon"><img src="frontend/assets/images/logo/skillshare hub.png" alt="SkillShare Hub Logo" style="max-width:120px; height:auto;"></div>
                <h1>Create Account</h1>
                <p>Start your journey to industry readiness</p>
                <div class="tagline">
                    <span>Learn</span>
                    <span>•</span>
                    <span>Connect</span>
                    <span>•</span>
                    <span>Grow</span>
                </div>
            </div>

            <div id="registerAlert"></div>

            <form id="registerFormElement" onsubmit="return handleRegister(event)">
                <!-- Full Name -->
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" id="regName" placeholder="Enter your full name" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="regEmail" placeholder="Enter your email" required>
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label>Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <input type="text" id="regAddress" placeholder="Enter your address" required>
                    </div>
                </div>

                <!-- Contact -->
                <div class="form-group">
                    <label>Contact Number</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-phone"></i></span>
                        <input type="tel" id="regContact" placeholder="Enter your phone number" required>
                    </div>
                </div>

                 <!-- Role Selector -->
                 <div class="form-group">
                     <label>I Want To Register As</label>
                     <div class="input-wrapper">
                         <span class="input-icon"><i class="fas fa-user-tag"></i></span>
                         <select id="regRole" required>
                             <option value="fresher">I am a student/fresher</option>
                             <option value="mentor">I am a mentor/instructor</option>
                         </select>
                     </div>
                 </div>

                 <!-- Background Field -->
                 <div class="form-group">
                     <label>Background Field</label>
                     <div class="input-wrapper">
                         <span class="input-icon"><i class="fas fa-book"></i></span>
                         <select id="regField" required>
                             <option value="">Select your field</option>
                             <option value="engineering">Engineering</option>
                             <option value="information-technology">Information Technology</option>
                             <option value="science">Science</option>
                             <option value="management">Management & Commerce</option>
                             <option value="law">Law</option>
                             <option value="education">Education</option>
                             <option value="agriculture">Agriculture</option>
                             <option value="health-sciences">Health Sciences</option>
                             <option value="arts">Arts & Humanities</option>
                             <option value="media">Media & Communication</option>
                             <option value="hospitality">Hospitality & Tourism</option>
                             <option value="research">Research & Innovation</option>
                         </select>
                     </div>
                 </div>

                <!-- Interested Course -->
                <div class="form-group">
                    <label>Interested Course</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-graduation-cap"></i></span>
                        <select id="regCourse" required>
                            <option value="">Select your course</option>
                            <optgroup label="Engineering">
                                <option value="software-engineering">Software Engineering</option>
                                <option value="computer-engineering">Computer Engineering</option>
                                <option value="civil-engineering">Civil Engineering</option>
                                <option value="mechanical-engineering">Mechanical Engineering</option>
                                <option value="electrical-engineering">Electrical Engineering</option>
                            </optgroup>
                            <optgroup label="Information Technology">
                                <option value="bsc-csit">BSc CSIT</option>
                                <option value="bit">BIT</option>
                                <option value="bca">BCA</option>
                                <option value="cyber-security">Cyber Security</option>
                                <option value="artificial-intelligence">Artificial Intelligence</option>
                                <option value="data-science">Data Science</option>
                            </optgroup>
                            <optgroup label="Science">
                                <option value="physics">Physics</option>
                                <option value="chemistry">Chemistry</option>
                                <option value="biology">Biology</option>
                                <option value="mathematics">Mathematics</option>
                                <option value="biotechnology">Biotechnology</option>
                            </optgroup>
                            <optgroup label="Management">
                                <option value="bba">BBA</option>
                                <option value="bbs">BBS</option>
                                <option value="bim">BIM</option>
                                <option value="accounting">Accounting</option>
                                <option value="finance">Finance</option>
                                <option value="marketing">Marketing</option>
                            </optgroup>
                            <optgroup label="Law">
                                <option value="llb">LLB</option>
                                <option value="corporate-law">Corporate Law</option>
                                <option value="criminal-law">Criminal Law</option>
                            </optgroup>
                            <optgroup label="Education">
                                <option value="bed">B.Ed</option>
                                <option value="med">M.Ed</option>
                            </optgroup>
                            <optgroup label="Health Sciences">
                                <option value="mbbs">MBBS</option>
                                <option value="nursing">Nursing</option>
                                <option value="pharmacy">Pharmacy</option>
                                <option value="public-health">Public Health</option>
                            </optgroup>
                            <optgroup label="Arts & Humanities">
                                <option value="english">English</option>
                                <option value="psychology">Psychology</option>
                                <option value="sociology">Sociology</option>
                                <option value="journalism">Journalism</option>
                            </optgroup>
                            <optgroup label="Media & Communication">
                                <option value="digital-marketing">Digital Marketing</option>
                                <option value="graphic-design">Graphic Design</option>
                                <option value="photography">Photography</option>
                                <option value="videography">Videography</option>
                            </optgroup>
                            <optgroup label="Hospitality & Tourism">
                                <option value="hotel-management">Hotel Management</option>
                                <option value="tourism-management">Tourism Management</option>
                                <option value="culinary-arts">Culinary Arts</option>
                            </optgroup>
                            <optgroup label="Research & Innovation">
                                <option value="research-methodology">Research Methodology</option>
                                <option value="data-analysis">Data Analysis</option>
                                <option value="academic-writing">Academic Writing</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="regPassword" placeholder="Create a password" required>
                        <span class="toggle-password" onclick="togglePassword('regPassword', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="password-strength-text" id="strengthText">Enter a strong password</div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-check-circle"></i></span>
                        <input type="password" id="regConfirmPassword" placeholder="Confirm your password" required>
                        <span class="toggle-password" onclick="togglePassword('regConfirmPassword', this)">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <!-- Terms -->
                <div class="form-group">
                    <label class="checkbox-label" style="font-weight:400;font-size:0.85rem;">
                        <input type="checkbox" id="regTerms" required>
                        I agree to the <a href="#" style="color:var(--primary-400);text-decoration:none;">Terms of Service</a> &amp; <a href="#" style="color:var(--primary-400);text-decoration:none;">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="#" onclick="showLogin()">Sign In</a>
            </div>
        </div>

    </div>
</div>

<script>
    // Same JS as in login.php
    document.addEventListener('DOMContentLoaded', function() {
        initPasswordStrength();
        initToastSystem();
    });

    function showRegister() {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('registerForm').style.display = 'block';
        document.getElementById('registerAlert').innerHTML = '';
        document.getElementById('loginAlert').innerHTML = '';
    }

    function showLogin() {
        document.getElementById('registerForm').style.display = 'none';
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('registerAlert').innerHTML = '';
        document.getElementById('loginAlert').innerHTML = '';
    }

    function togglePassword(inputId, element) {
        const input = document.getElementById(inputId);
        const icon = element.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function initPasswordStrength() {
        const passwordInput = document.getElementById('regPassword');
        if (!passwordInput) return;

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');

            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            let level, label, percentage;
            if (password.length === 0) {
                level = 'none';
                label = 'Enter a strong password';
                percentage = 0;
            } else if (strength <= 1) {
                level = 'weak';
                label = 'Weak password - Add more characters and variety';
                percentage = 25;
            } else if (strength <= 3) {
                level = 'fair';
                label = 'Fair password - Add more variety';
                percentage = 50;
            } else if (strength <= 4) {
                level = 'good';
                label = 'Good password - Almost there!';
                percentage = 75;
            } else {
                level = 'strong';
                label = 'Strong password - Excellent!';
                percentage = 100;
            }

            strengthBar.className = 'strength-bar ' + (level !== 'none' ? level : '');
            strengthBar.style.width = percentage + '%';
            strengthText.textContent = label;
            strengthText.className = 'password-strength-text ' + (level !== 'none' ? level : '');
        });
    }

    function handleLogin(event) {
        event.preventDefault();

        const email = document.getElementById('loginEmail').value.trim();
        const password = document.getElementById('loginPassword').value;
        const alertDiv = document.getElementById('loginAlert');

        if (!email || !password) {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please fill in all fields</div>`;
            return false;
        }

        if (!isValidEmail(email)) {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please enter a valid email address</div>`;
            return false;
        }

        const submitBtn = event.target.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';

        fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, password: password })
        })
        .then(r => r.json())
        .then(res => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';

            if (res.success) {
                alertDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> Login successful! Redirecting...</div>`;
                showToast('Welcome Back!', 'Login successful!', 'success', 1500);
                setTimeout(() => {
                    window.location.href = res.data.redirect;
                }, 1500);
            } else {
                alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${res.message}</div>`;
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Connection error. Please try again.</div>`;
        });

        return false;
    }

    function handleRegister(event) {
        event.preventDefault();

        const name = document.getElementById('regName').value.trim();
        const email = document.getElementById('regEmail').value.trim();
        const address = document.getElementById('regAddress').value.trim();
        const contact = document.getElementById('regContact').value.trim();
        const role = document.getElementById('regRole').value;
        const password = document.getElementById('regPassword').value;
        const confirmPassword = document.getElementById('regConfirmPassword').value;
        const terms = document.getElementById('regTerms').checked;
        const alertDiv = document.getElementById('registerAlert');

        if (!name || !email || !address || !contact || !role || !password || !confirmPassword) {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please fill in all fields</div>`;
            return false;
        }

        if (!isValidEmail(email)) {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please enter a valid email address</div>`;
            return false;
        }

        if (!isValidPhone(contact)) {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please enter a valid phone number</div>`;
            return false;
        }

        if (password.length < 6) {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Password must be at least 6 characters</div>`;
            return false;
        }

        if (password !== confirmPassword) {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Passwords do not match</div>`;
            return false;
        }

        if (!terms) {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please agree to the Terms & Privacy Policy</div>`;
            return false;
        }

        const submitBtn = event.target.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';

        fetch('api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                full_name: name,
                email: email,
                phone: contact,
                address: address,
                role: role,
                password: password,
                confirm_password: confirmPassword
            })
        })
        .then(r => r.json())
        .then(res => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';

            if (res.success) {
                alertDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> ${res.message}</div>`;
                showToast('Welcome!', 'Account created successfully!', 'success', 1500);
                setTimeout(() => {
                    window.location.href = res.data.redirect;
                }, 1500);
            } else {
                alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${res.message}</div>`;
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Connection error. Please try again.</div>`;
        });

        return false;
    }
    }

    function isValidEmail(email) { return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email); }
    function isValidPhone(phone) { return /^[0-9+\-\s\(\)]{7,20}$/.test(phone); }

    function initToastSystem() { if (!document.getElementById('toastContainer')) { const container = document.createElement('div'); container.className = 'toast-container'; container.id = 'toastContainer'; document.body.appendChild(container); } }

    function showToast(title, message, type = 'info', duration = 5000) {
        const container = document.getElementById('toastContainer'); if (!container) return;
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        const colors = { success: '#22c55e', error: '#ef4444', warning: '#f59e0b', info: '#3b82f6' };
        const toast = document.createElement('div'); toast.className = `toast ${type}`;
        toast.innerHTML = `<span class="icon" style="color:${colors[type]}"><i class="fas ${icons[type] || icons.info}"></i></span><div class="content"><div class="title">${title}</div><div class="message">${message}</div></div><button class="close"><i class="fas fa-times"></i></button>`;
        toast.querySelector('.close').addEventListener('click', function() { closeToast(toast); });
        container.appendChild(toast);
        if (duration > 0) setTimeout(() => closeToast(toast), duration);
        toast.addEventListener('click', function(e) { if (e.target.closest('.close')) return; closeToast(toast); });
    }
    function closeToast(toast) { if (!toast) return; toast.style.opacity = '0'; toast.style.transform = 'translateX(100px)'; setTimeout(() => { if (toast.parentNode) toast.remove(); }, 300); }
</script>

<script>
function handleGoogleLogin() {
    showToast('Connecting to Google...', 'Opening Google Accounts connection window...', 'info');

    const width = 500, height = 650;
    const left = (window.innerWidth - width) / 2;
    const top = (window.innerHeight - height) / 2;
    const googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?client_id=1084293847291-example.apps.googleusercontent.com&redirect_uri=' + encodeURIComponent(window.location.origin + '/api/google-callback.php') + '&response_type=code&scope=openid%20email%20profile&prompt=select_account';

    const popup = window.open(googleAuthUrl, 'GoogleAuthWindow', `width=${width},height=${height},top=${top},left=${left},status=no,toolbar=no,menubar=no`);

    setTimeout(function() {
        var email = prompt("Google Accounts Sign-In Connection:\n\nEnter your Google email to authenticate:", "user.google@gmail.com");
        if (email && email.trim() !== '') {
            var name = email.split('@')[0].replace(/[^a-zA-Z0-9]/g, ' ').toUpperCase();
            fetch('api/social-login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    provider: 'google',
                    email: email.trim(),
                    name: name,
                    social_id: 'google_' + Date.now()
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    showToast('Google Connected', res.message, 'success');
                    setTimeout(function() {
                        window.location.href = res.data.redirect;
                    }, 1000);
                } else {
                    showToast('Authentication Error', res.message, 'error');
                }
            })
            .catch(function() {
                showToast('Error', 'Google authentication request failed.', 'error');
            });
        }
    }, 800);
}

function handleFacebookLogin() {
    showToast('Connecting to Facebook...', 'Opening Facebook Auth connection window...', 'info');

    const width = 500, height = 650;
    const left = (window.innerWidth - width) / 2;
    const top = (window.innerHeight - height) / 2;
    const fbAuthUrl = 'https://www.facebook.com/v18.0/dialog/oauth?client_id=123456789012345&redirect_uri=' + encodeURIComponent(window.location.origin + '/api/facebook-callback.php') + '&scope=email,public_profile';

    const popup = window.open(fbAuthUrl, 'FacebookAuthWindow', `width=${width},height=${height},top=${top},left=${left},status=no,toolbar=no,menubar=no`);

    setTimeout(function() {
        var email = prompt("Facebook Login Connection:\n\nEnter your Facebook email to authenticate:", "user.facebook@gmail.com");
        if (email && email.trim() !== '') {
            var name = email.split('@')[0].replace(/[^a-zA-Z0-9]/g, ' ').toUpperCase();
            fetch('api/social-login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    provider: 'facebook',
                    email: email.trim(),
                    name: name,
                    social_id: 'fb_' + Date.now()
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    showToast('Facebook Connected', res.message, 'success');
                    setTimeout(function() {
                        window.location.href = res.data.redirect;
                    }, 1000);
                } else {
                    showToast('Authentication Error', res.message, 'error');
                }
            })
            .catch(function() {
                showToast('Error', 'Facebook authentication request failed.', 'error');
            });
        }
    }, 800);
}
</script>
</body>
</html>
