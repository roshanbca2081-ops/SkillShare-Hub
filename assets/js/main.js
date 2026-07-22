document.addEventListener('DOMContentLoaded', () => {
  // ==========================================
  // 1. Animated Counters
  // ==========================================
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

  // ==========================================
  // 2. Parallax Effect for Premium Backgrounds
  // ==========================================
  const parallaxEls = Array.from(document.querySelectorAll('[data-parallax-x],[data-parallax-y]'));
  let ticking = false;
  const updateParallax = () => {
    ticking = false;
    const scrollY = window.scrollY || 0;
    parallaxEls.forEach((el) => {
      const px = Number(el.getAttribute('data-parallax-x') || 0);
      const py = Number(el.getAttribute('data-parallax-y') || 0);
      el.style.transform = `translate3d(${(px * scrollY) / 600}px, ${(py * scrollY) / 600}px, 0)`;
    });
  };
  const onScroll = () => {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(updateParallax);
  };
  if (parallaxEls.length) {
    window.addEventListener('scroll', onScroll, { passive: true });
    updateParallax();
  }

  // ==========================================
  // 3. Navbar Controls
  // ==========================================
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    const hamburger = navbar.querySelector('[data-hamburger]');
    const profile = navbar.querySelector('[data-profile]');

    // Hamburger toggle
    if (hamburger) {
      hamburger.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = navbar.getAttribute('data-mobile-open') === 'true';
        navbar.setAttribute('data-mobile-open', isOpen ? 'false' : 'true');
        if (profile) profile.setAttribute('data-open', 'false');
        // Prevent body scroll when mobile menu is open
        if (!isOpen) {
          document.body.style.overflow = 'hidden';
        } else {
          document.body.style.overflow = '';
        }
      });
    }

    // Profile dropdown
    if (profile) {
      const trigger = profile.querySelector('[data-profile-trigger]');
      if (trigger) {
        trigger.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          const isOpen = profile.getAttribute('data-open') === 'true';
          profile.setAttribute('data-open', isOpen ? 'false' : 'true');
        });
      }
    }

    // Click outside closes all
    document.addEventListener('click', (e) => {
      const profileOpen = profile?.getAttribute('data-open') === 'true';
      const mobileOpen = navbar.getAttribute('data-mobile-open') === 'true';
      if (!profileOpen && !mobileOpen) return;

      if (profile && profileOpen && !e.target.closest('[data-profile]')) {
        profile.setAttribute('data-open', 'false');
      }
      if (mobileOpen && !e.target.closest('[data-hamburger]') && !e.target.closest('.mobile-panel')) {
        navbar.setAttribute('data-mobile-open', 'false');
        document.body.style.overflow = '';
      }
    });
  }

  // ==========================================
  // 4. Scroll Reveal Animations
  // ==========================================
  const revealEls = document.querySelectorAll('.reveal');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
  revealEls.forEach((el) => revealObserver.observe(el));

  // ==========================================
  // 5. FAQ Accordion
  // ==========================================
  document.querySelectorAll('.faq-item').forEach((item) => {
    item.addEventListener('click', () => {
      item.classList.toggle('active');
    });
  });

  // ==========================================
  // 6. Password Strength Indicator
  // ==========================================
  document.querySelectorAll('[data-password-strength]').forEach((field) => {
    const bar = field.closest('.password-wrapper')?.querySelector('.password-strength-bar');
    const text = field.closest('.password-wrapper')?.querySelector('.password-strength-text');
    if (!bar) return;

    field.addEventListener('input', () => {
      const val = field.value;
      let strength = 'weak';
      if (val.length >= 8 && /[A-Z]/.test(val) && /[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) {
        strength = 'very-strong';
      } else if (val.length >= 6 && /[A-Z]/.test(val) && /[0-9]/.test(val)) {
        strength = 'strong';
      } else if (val.length >= 4 && /[a-zA-Z]/.test(val)) {
        strength = 'medium';
      }
      bar.className = 'password-strength-bar ' + strength;
      if (text) {
        text.className = 'password-strength-text ' + strength;
        const labels = { weak: 'Weak', medium: 'Medium', strong: 'Strong', 'very-strong': 'Very Strong' };
        text.textContent = val.length > 0 ? labels[strength] : '';
      }
    });
  });

  // ==========================================
  // 7. Form Validation
  // ==========================================
  document.querySelectorAll('.needs-validation').forEach((form) => {
    form.addEventListener('submit', (e) => {
      form.querySelectorAll('[required]').forEach((input) => {
        if (!input.value.trim()) {
          input.classList.add('is-invalid');
        } else {
          input.classList.remove('is-invalid');
        }
      });
      if (form.querySelectorAll('.is-invalid').length > 0) {
        e.preventDefault();
        showToast('Please fill in all required fields.', 'error');
      }
    });
    form.querySelectorAll('[required]').forEach((input) => {
      input.addEventListener('input', () => {
        if (input.value.trim()) {
          input.classList.remove('is-invalid');
        }
      });
    });
  });

  // ==========================================
  // 8. Password Toggle Visibility
  // ==========================================
  document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const field = button.parentElement?.querySelector('[data-password-field]');
      if (!field) return;
      const isPassword = field.getAttribute('type') === 'password';
      field.setAttribute('type', isPassword ? 'text' : 'password');
      const icon = button.querySelector('i');
      if (icon) icon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });
  });

  // ==========================================
  // 9. Confirm Password Match
  // ==========================================
  document.querySelectorAll('[data-password-match]').forEach((confirmField) => {
    const passwordField = document.querySelector(confirmField.getAttribute('data-password-match'));
    if (!passwordField) return;
    confirmField.addEventListener('input', () => {
      if (confirmField.value && confirmField.value !== passwordField.value) {
        confirmField.classList.add('is-invalid');
      } else {
        confirmField.classList.remove('is-invalid');
      }
    });
    passwordField.addEventListener('input', () => {
      if (confirmField.value && confirmField.value !== passwordField.value) {
        confirmField.classList.add('is-invalid');
      } else {
        confirmField.classList.remove('is-invalid');
      }
    });
  });
});

// ==========================================
// Global: showToast function
// ==========================================
function showToast(message, type = 'info', duration = 3500) {
  const container = document.getElementById('toast-container');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `toast toast--${type}`;
  toast.innerHTML = `
    <span class="icon">${type === 'success' ? '✔' : type === 'error' ? '✖' : 'ℹ'}</span>
    <span>${message}</span>
  `;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = 'toastOut 260ms ease forwards';
    toast.addEventListener('animationend', () => toast.remove());
  }, duration);
}

// ==========================================
// Global: Theme Toggle
// ==========================================
function setThemeMode(mode) {
  if (mode === 'dark') {
    document.body.classList.add('dark');
    localStorage.setItem('themeMode', 'dark');
  } else {
    document.body.classList.remove('dark');
    localStorage.setItem('themeMode', 'light');
  }
}

function filterCards(containerSelector, query, category) {
  const cards = document.querySelectorAll(containerSelector);
  const lowerQuery = query.toLowerCase().trim();
  cards.forEach(card => {
    const matchesSearch = lowerQuery === '' || (card.dataset.search || '').toLowerCase().includes(lowerQuery);
    const matchesCategory = category === 'all' || (card.dataset.category || '').toLowerCase() === category.toLowerCase();
    card.style.display = matchesSearch && matchesCategory ? 'grid' : 'none';
  });
}

function setActiveTab(tabGroup, activeTab) {
  const tabs = document.querySelectorAll(`${tabGroup} [data-tab]`);
  tabs.forEach(tab => tab.classList.toggle('active', tab.dataset.tab === activeTab));
}

function filterCourses(category) {
  setActiveTab('#courseTabs', category);
  const query = document.querySelector('#courseSearch')?.value || '';
  filterCards('.course-card', query, category);
}

function filterMentors(category) {
  setActiveTab('#mentorTabs', category);
  filterCards('.mentor-card', '', category);
}

function startCourse(courseName) {
  showToast(`Course started: ${courseName}`, 'success');
}

// On DOM ready
document.addEventListener('DOMContentLoaded', () => {
  // Loader
  const loader = document.querySelector('[data-site-loader]');
  if (loader) window.setTimeout(() => loader.classList.add('is-hidden'), 350);

  // Theme
  const savedTheme = localStorage.getItem('themeMode');
  if (savedTheme) setThemeMode(savedTheme);

  const themeToggle = document.querySelector('[data-theme-toggle]');
  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const isDark = document.body.classList.toggle('dark');
      setThemeMode(isDark ? 'dark' : 'light');
      showToast(isDark ? 'Dark mode activated' : 'Light mode activated', 'success');
    });
  }

  // Toast action buttons
  const actionButtons = document.querySelectorAll('[data-toast]');
  actionButtons.forEach(button => {
    button.addEventListener('click', () => {
      showToast(button.dataset.toast || 'Action performed');
    });
  });

  // Course search
  const courseSearch = document.querySelector('#courseSearch');
  if (courseSearch) {
    courseSearch.addEventListener('input', () => filterCourses(
      document.querySelector('#courseTabs [data-tab].active')?.dataset.tab || 'all'
    ));
  }
});

window.filterCourses = filterCourses;
window.filterMentors = filterMentors;
window.startCourse = startCourse;
window.showToast = showToast;
window.setThemeMode = setThemeMode;

