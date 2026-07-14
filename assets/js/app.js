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

document.addEventListener('DOMContentLoaded', () => {
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

  const actionButtons = document.querySelectorAll('[data-toast]');
  actionButtons.forEach(button => {
    button.addEventListener('click', () => {
      showToast(button.dataset.toast || 'Action performed');
    });
  });

  const courseSearch = document.querySelector('#courseSearch');
  if (courseSearch) {
    courseSearch.addEventListener('input', () => filterCourses(document.querySelector('#courseTabs [data-tab].active')?.dataset.tab || 'all'));
  }
});

window.filterCourses = filterCourses;
window.filterMentors = filterMentors;
window.startCourse = startCourse;
