document.addEventListener('DOMContentLoaded', () => {
  // Animated counters (landing page)
  const counters = document.querySelectorAll('[data-count]');
  counters.forEach((counter) => {
    const target = Number(counter.dataset.count || 0);
    let current = 0;
    const step = Math.max(1, Math.floor(target / 40));
    const interval = setInterval(() => {
      current += step;
      if (current >= target) {
        counter.textContent = target.toLocaleString();
        clearInterval(interval);
      } else {
        counter.textContent = current.toLocaleString();
      }
    }, 30);
  });

  // Pure CSS/JS navbar:
  // - hamburger toggles mobile panel
  // - profile dropdown toggles on click (desktop + mobile)

  const navbar = document.querySelector('.navbar');
  if (navbar) {
    const hamburger = navbar.querySelector('[data-hamburger]');
    const profile = navbar.querySelector('[data-profile]');

    if (hamburger) {
      hamburger.addEventListener('click', () => {
        const isOpen = navbar.getAttribute('data-mobile-open') === 'true';
        navbar.setAttribute('data-mobile-open', isOpen ? 'false' : 'true');

        // Close dropdown when opening mobile panel
        if (profile) profile.setAttribute('data-open', 'false');
      });
    }

    if (profile) {
      const trigger = profile.querySelector('[data-profile-trigger]');
      if (trigger) {
        trigger.addEventListener('click', (e) => {
          e.preventDefault();
          const isOpen = profile.getAttribute('data-open') === 'true';
          profile.setAttribute('data-open', isOpen ? 'false' : 'true');
        });
      }
    }

    // Click outside closes dropdown + mobile panel
    document.addEventListener('click', (e) => {
      const profileOpen = profile?.getAttribute('data-open') === 'true';
      if (!profileOpen && navbar.getAttribute('data-mobile-open') !== 'true') return;

      const clickedProfile = e.target && e.target.closest && e.target.closest('[data-profile]');
      const clickedHamburger = e.target && e.target.closest && e.target.closest('[data-hamburger]');

      if (profile && profileOpen && !clickedProfile) profile.setAttribute('data-open', 'false');

      const mobileOpen = navbar.getAttribute('data-mobile-open') === 'true';
      if (mobileOpen && !clickedHamburger && !e.target.closest('.mobile-panel')) {
        navbar.setAttribute('data-mobile-open', 'false');
      }
    });
  }
});

