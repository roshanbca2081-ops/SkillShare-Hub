/* =============================================
   SkillShare Hub - Homepage JS (Typed text, counters)
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Typed text effect in hero
    const typedEl = document.getElementById('typedText');
    if (typedEl) {
        const words = ['Skills', 'Courses', 'Research', 'Knowledge', 'Careers'];
        let wordIndex = 0;
        let charIndex = 0;
        let deleting = false;

        function type() {
            const currentWord = words[wordIndex];
            if (deleting) {
                typedEl.textContent = currentWord.substring(0, charIndex - 1);
                charIndex--;
            } else {
                typedEl.textContent = currentWord.substring(0, charIndex + 1);
                charIndex++;
            }

            let delay = deleting ? 50 : 120;
            if (!deleting && charIndex === currentWord.length) {
                delay = 1800;
                deleting = true;
            } else if (deleting && charIndex === 0) {
                deleting = false;
                wordIndex = (wordIndex + 1) % words.length;
                delay = 400;
            }
            setTimeout(type, delay);
        }
        type();
    }

    // Animated counters
    const counters = document.querySelectorAll('.counter');
    if (counters.length) {
        const animateCounter = function (el) {
            const target = parseInt(el.getAttribute('data-target'), 10);
            const duration = 2000;
            const startTime = performance.now();

            function update(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                // Ease-out
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target).toLocaleString();
                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    el.textContent = target.toLocaleString();
                }
            }
            requestAnimationFrame(update);
        };

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(function (el) { observer.observe(el); });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href.length > 1) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
});
