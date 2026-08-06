/* =============================================
   SkillShare Hub - Main JavaScript
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Preloader
    const loader = document.getElementById('preloader');
    if (loader) {
        window.addEventListener('load', function () {
            loader.classList.add('done');
            setTimeout(function () {
                loader.style.display = 'none';
            }, 600);
        });
        // Fallback in case window load already fired
        setTimeout(function () {
            loader.classList.add('done');
        }, 2500);
    }

    // Back to top button
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 400) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Mobile hamburger toggle
    const hamburger = document.querySelector('.hamburger');
    const mobileLinks = document.querySelector('.site-links');
    const overlay = document.querySelector('.nav-overlay');
    if (hamburger && mobileLinks) {
        hamburger.addEventListener('click', function () {
            mobileLinks.classList.toggle('show');
            if (overlay) overlay.classList.toggle('show');
        });
        if (overlay) {
            overlay.addEventListener('click', function () {
                mobileLinks.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
    }

    // Password visibility toggle
    document.querySelectorAll('.input-eye').forEach(function (eye) {
        eye.addEventListener('click', function () {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });

    // Scroll reveal fallback (IntersectionObserver)
    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        revealEls.forEach(function (el) { observer.observe(el); });
    } else {
        revealEls.forEach(function (el) { el.classList.add('revealed'); });
    }

    // Wishlist toggle
    document.querySelectorAll('.course-wishlist').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const icon = btn.querySelector('i');
            icon.classList.toggle('fa-regular');
            icon.classList.toggle('fa-solid');
            icon.style.color = icon.classList.contains('fa-solid') ? 'var(--accent)' : '';
        });
    });

    // Form validation helper
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const required = form.querySelectorAll('[required]');
            let valid = true;
            required.forEach(function (field) {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            if (valid) {
                // Simulate success
                const msg = document.createElement('div');
                msg.className = 'alert alert-success';
                msg.innerHTML = '<i class="fa-solid fa-circle-check"></i> Submitted successfully!';
                form.prepend(msg);
                form.reset();
                setTimeout(function () { msg.remove(); }, 4000);
            }
        });
    });
});
