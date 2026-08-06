/* ============================================
   SkillShare Hub - Authentication JS
   Handles Login & Register API integration
   with centered glassmorphism UI
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    initPasswordStrength();
    initToastSystem();
    initAuthForms();
});

/* ============================================
   TOGGLE BETWEEN LOGIN & REGISTER
   ============================================ */
function showRegister() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').style.display = 'block';
    document.getElementById('registerAlert').innerHTML = '';
    document.getElementById('loginAlert').innerHTML = '';
    document.body.classList.add('register-mode');
}

function showLogin() {
    document.getElementById('registerForm').style.display = 'none';
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('registerAlert').innerHTML = '';
    document.getElementById('loginAlert').innerHTML = '';
    document.body.classList.remove('register-mode');
}

/* ============================================
   TOGGLE PASSWORD VISIBILITY
   ============================================ */
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

/* ============================================
   PASSWORD STRENGTH
   ============================================ */
function initPasswordStrength() {
    const passwordInput = document.getElementById('regPassword');
    if (!passwordInput) return;

    passwordInput.addEventListener('input', function () {
        const password = this.value;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        if (!strengthBar || !strengthText) return;

        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;

        let level, label, percentage;
        if (password.length === 0) {
            level = 'none'; label = 'Enter a strong password'; percentage = 0;
        } else if (strength <= 1) {
            level = 'weak'; label = 'Weak password - Add more characters'; percentage = 25;
        } else if (strength <= 3) {
            level = 'fair'; label = 'Fair password - Add more variety'; percentage = 50;
        } else if (strength <= 4) {
            level = 'good'; label = 'Good password - Almost there!'; percentage = 75;
        } else {
            level = 'strong'; label = 'Strong password - Excellent!'; percentage = 100;
        }

        strengthBar.className = 'strength-bar ' + (level !== 'none' ? level : '');
        strengthBar.style.width = percentage + '%';
        strengthText.textContent = label;
        strengthText.className = 'password-strength-text ' + (level !== 'none' ? level : '');
    });
}

/* ============================================
   AUTH FORM SUBMISSION (API INTEGRATION)
   ============================================ */
function initAuthForms() {
    const loginForm = document.getElementById('loginFormElement');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    const registerForm = document.getElementById('registerFormElement');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }
}

/* ============================================
   HANDLE LOGIN (API CALL)
   ============================================ */
async function handleLogin(event) {
    event.preventDefault();

    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value.trim();
    const remember = document.getElementById('rememberMe') ? document.getElementById('rememberMe').checked : false;
    const alertDiv = document.getElementById('loginAlert');

    // Client-side validation
    if (!email || !password) {
        alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please fill in all fields</div>`;
        return false;
    }
    if (!isValidEmail(email)) {
        alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please enter a valid email address</div>`;
        return false;
    }

    // Show loading state
    const btn = event.target.querySelector('button[type="submit"]');
    const btnText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';

    try {
        const res = await fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password, remember })
        });

        const data = await res.json();

        if (data.success) {
            alertDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> Login successful! Redirecting...</div>`;
            showToast('Welcome Back!', data.message, 'success', 3000);
            setTimeout(() => {
                window.location.href = data.data.redirect || 'index.php';
            }, 1500);
        } else {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${data.message}</div>`;
            showToast('Login Failed', data.message, 'error', 4000);
        }
    } catch (err) {
        alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Unable to connect. Please try again.</div>`;
        showToast('Error', 'Network error. Please try again.', 'error', 4000);
    } finally {
        btn.disabled = false;
        btn.innerHTML = btnText;
    }

    return false;
}

/* ============================================
   HANDLE REGISTER (API CALL)
   ============================================ */
async function handleRegister(event) {
event.preventDefault();

    const firstname = document.getElementById('regFname').value.trim();
    const lastname = document.getElementById('regLname').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const role = document.querySelector('input[name="role"]:checked') ?
        document.querySelector('input[name="role"]:checked').value :
        (document.getElementById('regRole') ? document.getElementById('regRole').value : 'fresher');
    const password = document.getElementById('regPassword').value;
    const confirmPassword = document.getElementById('regConfirm').value;
    const terms = document.getElementById('agreeTerms') ? document.getElementById('agreeTerms').checked : false;
    const alertDiv = document.getElementById('registerAlert');

    // Validation
    if (!firstname || !lastname || !email || !role || !password || !confirmPassword) {
        alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please fill in all fields</div>`;
        return false;
    }
    if (!isValidEmail(email)) {
        alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Please enter a valid email address</div>`;
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

    // Show loading state
    const btn = event.target.querySelector('button[type="submit"]');
    const btnText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating account...';

    try {
        const res = await fetch('api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ firstname, lastname, email, role, password, confirm_password: confirmPassword })
        });

        const data = await res.json();

        if (data.success) {
            alertDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle"></i> Account created successfully! Welcome aboard!</div>`;
            showToast('Welcome!', data.message, 'success', 3000);
            setTimeout(() => {
                window.location.href = data.data.redirect || 'index.php';
            }, 1500);
        } else {
            alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ${data.message}</div>`;
            showToast('Registration Failed', data.message, 'error', 4000);
        }
    } catch (err) {
        alertDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Unable to connect. Please try again.</div>`;
        showToast('Error', 'Network error. Please try again.', 'error', 4000);
    } finally {
        btn.disabled = false;
        btn.innerHTML = btnText;
    }

    return false;
}

/* ============================================
   VALIDATION HELPERS
   ============================================ */
function isValidEmail(email) {
    return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);
}

function isValidPhone(phone) {
    return /^[0-9+\-\s\(\)]{7,20}$/.test(phone);
}

/* ============================================
   TOAST SYSTEM
   ============================================ */
function initToastSystem() {
    if (!document.getElementById('toastContainer')) {
        const container = document.createElement('div');
        container.className = 'toast-container';
        container.id = 'toastContainer';
        document.body.appendChild(container);
    }
}

function showToast(title, message, type = 'info', duration = 5000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    const colors = {
        success: '#22c55e',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <span class="icon" style="color:${colors[type]}"><i class="fas ${icons[type] || icons.info}"></i></span>
        <div class="content">
            <div class="title">${title}</div>
            <div class="message">${message}</div>
        </div>
        <button class="close"><i class="fas fa-times"></i></button>
    `;

    toast.querySelector('.close').addEventListener('click', function () {
        closeToast(toast);
    });

    container.appendChild(toast);

    if (duration > 0) {
        setTimeout(() => closeToast(toast), duration);
    }
}

function closeToast(toast) {
    if (!toast) return;
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100px)';
    setTimeout(() => {
        if (toast.parentNode) toast.remove();
    }, 300);
}

console.log('🔐 SkillShare Hub - Authentication System Loaded');
console.log('📱 Responsive: Enabled');
console.log('🔒 Security: Active');
