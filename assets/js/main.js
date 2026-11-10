// ============================================
// Main JavaScript
// ============================================

const THEME_STORAGE_KEY = 'skillshare-theme';
const SERVER_THEME_PREFERENCE = document.querySelector('meta[name="csrf-token"]')
    ? (document.documentElement.dataset.themePreference || 'system')
    : 'system';

function getSystemTheme() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function getSavedThemePreference() {
    if (document.querySelector('meta[name="csrf-token"]')) {
        return SERVER_THEME_PREFERENCE;
    }
    try {
        return localStorage.getItem(THEME_STORAGE_KEY) || 'system';
    } catch (error) {
        return 'system';
    }
}

function applyTheme(themePreference, savePreference = true) {
    const safePreference = ['light', 'dark', 'system'].includes(themePreference) ? themePreference : 'system';
    const resolvedTheme = safePreference === 'system' ? getSystemTheme() : safePreference;

    document.documentElement.setAttribute('data-theme', resolvedTheme);
    document.body.setAttribute('data-theme', resolvedTheme);

    try {
        localStorage.setItem(THEME_STORAGE_KEY, safePreference);
    } catch (error) {
        // Ignore storage issues in restricted browsers.
    }

    // Save to database if user is logged in
    if (savePreference) {
        saveThemeToServer(safePreference);
    }

    const themeButtons = document.querySelectorAll('.theme-option');
    themeButtons.forEach(button => {
        const isActive = button.getAttribute('data-theme-option') === safePreference;
        button.classList.toggle('active', isActive);
        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    const themeLabels = document.querySelectorAll('.theme-label');
    themeLabels.forEach(label => {
        const labelTheme = safePreference.charAt(0).toUpperCase() + safePreference.slice(1);
        label.textContent = labelTheme;
    });

    document.querySelectorAll('[data-theme-select]').forEach(select => {
        select.value = safePreference;
    });

    document.dispatchEvent(new CustomEvent('themechange', {
        detail: { preference: safePreference, resolvedTheme }
    }));
}

function saveThemeToServer(themePreference) {
    // Only save if user appears to be logged in (check for session indicators)
    const isLoggedIn = document.body.classList.contains('fresher-layout-page') || 
                       document.body.classList.contains('mentor-dashboard-page') ||
                       document.querySelector('a[href*="logout.php"]');
    
    if (isLoggedIn) {
        fetch(appUrl('api/theme-preference.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                theme_preference: themePreference,
                csrf_token: document.querySelector('meta[name="csrf-token"]')?.content || ''
            })
        }).catch(() => {
            // Silently fail - localStorage will still work
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const storedTheme = (() => {
        try {
            return document.querySelector('meta[name="csrf-token"]')
                ? SERVER_THEME_PREFERENCE
                : (localStorage.getItem(THEME_STORAGE_KEY) || 'system');
        } catch (error) {
            return SERVER_THEME_PREFERENCE;
        }
    })();

    applyTheme(storedTheme, false);

    if (window.matchMedia) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleSystemThemeChange = () => {
            let preference = 'system';
            try {
                preference = getSavedThemePreference();
            } catch (error) {
                // Keep the system preference when storage is unavailable.
            }
            if (preference === 'system') {
                applyTheme('system');
            }
        };

        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', handleSystemThemeChange);
        } else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(handleSystemThemeChange);
        }
    }

    document.querySelectorAll('.theme-option').forEach(button => {
        button.addEventListener('click', function() {
            const selectedTheme = this.getAttribute('data-theme-option');
            applyTheme(selectedTheme);
        });
    });

    document.querySelectorAll('[data-theme-select]').forEach(select => {
        select.addEventListener('change', function() {
            applyTheme(this.value);
        });
    });

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));
    
    // Initialize popovers
    const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
    popovers.forEach(el => new bootstrap.Popover(el));
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        }, 5000);
    });
    
    // Confirm delete
    document.querySelectorAll('.confirm-delete').forEach(el => {
        el.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Fresher mobile drawer: keep page scrolling locked while the drawer is open.
    const sidebarToggle = document.getElementById('fresherSidebarToggle');
    const fresherSidebar = document.getElementById('fresherSidebar');
    const setFresherSidebarOpen = open => {
        document.body.classList.toggle('fresher-sidebar-open', open);
        if (sidebarToggle) {
            sidebarToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            sidebarToggle.setAttribute('aria-label', open ? 'Close student navigation' : 'Open student navigation');
        }
    };

    if (sidebarToggle && fresherSidebar) {
        sidebarToggle.addEventListener('click', function() {
            setFresherSidebarOpen(!document.body.classList.contains('fresher-sidebar-open'));
        });

        document.querySelectorAll('[data-fresher-sidebar-close]').forEach(button => {
            button.addEventListener('click', () => setFresherSidebarOpen(false));
        });

        fresherSidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => setFresherSidebarOpen(false));
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && document.body.classList.contains('fresher-sidebar-open')) {
                setFresherSidebarOpen(false);
                sidebarToggle.focus();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) setFresherSidebarOpen(false);
        });
    }
});

// ============================================
// AJAX Helper Functions
// ============================================

function ajaxRequest(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        if (method === 'POST') {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
        
        xhr.onload = function() {
            if (this.status >= 200 && this.status < 300) {
                resolve(JSON.parse(this.response));
            } else {
                reject({
                    status: this.status,
                    statusText: this.statusText
                });
            }
        };
        
        xhr.onerror = function() {
            reject({
                status: this.status,
                statusText: this.statusText
            });
        };
        
        if (data) {
            xhr.send(new URLSearchParams(data));
        } else {
            xhr.send();
        }
    });
}

// ============================================
// Form Validation
// ============================================

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePassword(password) {
    return password.length >= 6;
}

function validatePhone(phone) {
    const re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
    return re.test(phone);
}

// ============================================
// Search
// ============================================

function liveSearch(inputId, resultsId, url) {
    const input = document.getElementById(inputId);
    const results = document.getElementById(resultsId);
    
    let timeout = null;
    
    input.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = this.value.trim();
            if (query.length >= 2) {
                ajaxRequest(url + '?q=' + encodeURIComponent(query))
                    .then(data => {
                        results.innerHTML = data.html;
                        results.style.display = 'block';
                    })
                    .catch(() => {
                        results.innerHTML = '<div class="text-danger">Error loading results</div>';
                    });
            } else {
                results.style.display = 'none';
            }
        }, 300);
    });
    
    document.addEventListener('click', function(e) {
        if (!results.contains(e.target) && e.target !== input) {
            results.style.display = 'none';
        }
    });
}

// ============================================
// Infinite Scroll
// ============================================

function infiniteScroll(containerId, url, page = 1) {
    const container = document.getElementById(containerId);
    let loading = false;
    let hasMore = true;
    
    function loadMore() {
        if (loading || !hasMore) return;
        loading = true;
        
        ajaxRequest(url + '?page=' + page)
            .then(data => {
                container.insertAdjacentHTML('beforeend', data.html);
                hasMore = data.hasMore;
                page++;
                loading = false;
            })
            .catch(() => {
                loading = false;
            });
    }
    
    window.addEventListener('scroll', function() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
            loadMore();
        }
    });
}

// ============================================
// Notifications
// ============================================

function showNotification(message, type = 'info', duration = 3000) {
    const colors = {
        success: '#48bb78',
        error: '#fc8181',
        warning: '#ed8936',
        info: '#4299e1'
    };
    
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background: ${colors[type] || colors.info};
        color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        max-width: 400px;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), 300);
    }, duration);
}

// Add slideIn animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);