/* =============================================
   SkillShare Hub - Shared Platform JS
   Toast system, reveal on scroll, dashboard helpers
   ============================================= */

// Mark that JS is enabled (so reveal elements know to animate)
document.documentElement.classList.add('js-enabled');

// ============================================
// TOAST SYSTEM
// ============================================
function showToast(title, message, type = 'info', duration = 4000) {
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

    toast.querySelector('.close').addEventListener('click', function() {
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
    toast.style.transform = 'translateX(50px)';
    setTimeout(() => {
        if (toast.parentNode) toast.remove();
    }, 300);
}

// ============================================
// REVEAL ON SCROLL
// ============================================
function initReveal() {
    const revealEls = document.querySelectorAll('.reveal');
    if (!revealEls.length) return;

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        revealEls.forEach(el => observer.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('visible'));
    }
}

// ============================================
// TOGGLE PASSWORD VISIBILITY
// ============================================
function togglePassword(inputId, element) {
    const input = document.getElementById(inputId);
    const icon = element.querySelector('i');
    if (!input) return;
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

// ============================================
// VALIDATION HELPERS
// ============================================
function isValidEmail(email) {
    return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);
}

function isValidPhone(phone) {
    return /^[0-9+\-\s\(\)]{7,20}$/.test(phone);
}

// ============================================
// DOM READY
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    initReveal();

    // Welcome toast on public pages
    const welcome = document.getElementById('welcomeToast');
    if (welcome) {
        setTimeout(() => {
            showToast('👋 Welcome!', welcome.dataset.message || 'Welcome to SkillShare Hub. Start your learning journey today!', 'info', 5000);
        }, 1000);
    }
});
</content>
