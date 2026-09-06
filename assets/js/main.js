// ============================================
// Main JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function() {
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
    
    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('show');
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