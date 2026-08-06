/* =============================================
   SkillShare Hub - Animations JS
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Scroll reveal with stagger
    const revealElements = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        revealElements.forEach(function (el) { revealObserver.observe(el); });
    } else {
        revealElements.forEach(function (el) { el.classList.add('revealed'); });
    }

    // Parallax movement on hero visuals
    const heroImg = document.querySelector('.hero-img');
    if (heroImg) {
        window.addEventListener('mousemove', function (e) {
            const x = (e.clientX / window.innerWidth - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;
            heroImg.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
        });
    }

    // Floating cards gentle bob handled via CSS animation

    // Animated gradient background for hero
    const hero = document.querySelector('.hero');
    if (hero) {
        let hue = 250;
        setInterval(function () {
            hue = (hue + 1) % 360;
            hero.style.background = 'linear-gradient(135deg, hsl(' + hue + ',60%,96%), hsl(' + (hue + 20) + ',70%,92%))';
        }, 100);
    }

    // Button ripple effect
    document.querySelectorAll('.btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const rect = btn.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
            btn.appendChild(ripple);
            setTimeout(function () { ripple.remove(); }, 600);
        });
    });

    // Card tilt effect
    document.querySelectorAll('[data-tilt]').forEach(function (card) {
        card.addEventListener('mousemove', function (e) {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            card.style.transform = 'perspective(800px) rotateY(' + x * 8 + 'deg) rotateX(' + (-y * 8) + 'deg)';
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = 'perspective(800px) rotateY(0) rotateX(0)';
        });
    });
});
