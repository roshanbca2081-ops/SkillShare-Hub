// SkillShare Hub Dashboard JavaScript
(function() {
    'use strict';

    window.SkillShare = {
        showToast: function(title, message, type, duration) {
            duration = duration || 4000;
            var container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container';
                document.body.appendChild(container);
            }
            var icon = type === 'success' ? 'check-circle' :
                       type === 'error' ? 'exclamation-circle' :
                       type === 'warning' ? 'exclamation-triangle' : 'info-circle';
            var toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.innerHTML = '<div class="icon"><i class="fas fa-' + icon + '"></i></div>' +
                             '<div class="content"><div class="title">' + title + '</div>' +
                             '<div class="message">' + message + '</div></div>' +
                             '<button class="close">&times;</button>';
            container.appendChild(toast);

            setTimeout(function() {
                if (toast.parentNode) toast.remove();
            }, duration);

            var closeBtn = toast.querySelector('.close');
            if (closeBtn) {
                closeBtn.onclick = function() { toast.remove(); };
            }
        },

        escapeHtml: function(text) {
            if (text === null || text === undefined) return '';
            var div = document.createElement('div');
            div.textContent = String(text);
            return div.innerHTML;
        },

        apiFetch: function(url, options) {
            options = options || {};
            options.headers = Object.assign({'Content-Type': 'application/json'}, options.headers || {});
            if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
                options.body = JSON.stringify(options.body);
            }
            return fetch(url, options).then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            }).catch(function(err) {
                SkillShare.showToast('Error', err.message || 'Connection error', 'error', 5000);
                return {success: false, message: err.message};
            });
        },

        formatDate: function(dateStr) {
            if (!dateStr) return '';
            var d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return d.getDate() + ' ' + months[d.getMonth()] + ', ' + d.getFullYear();
        },

        formatTime: function(timeStr) {
            if (!timeStr) return '';
            var parts = timeStr.split(':');
            if (parts.length < 2) return timeStr;
            var h = parseInt(parts[0]);
            var m = parts[1];
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            if (h === 0) h = 12;
            return h + ':' + m + ' ' + ampm;
        },

        confirm: function(message, title) {
            return new Promise(function(resolve) {
                title = title || 'Confirm';
                var confirmed = window.confirm(message || 'Are you sure?');
                resolve(confirmed);
            });
        },

        formatCurrency: function(amount) {
            return '$' + parseFloat(amount || 0).toFixed(2);
        },

        formatDuration: function(minutes) {
            if (!minutes) return '0 min';
            var h = Math.floor(minutes / 60);
            var m = minutes % 60;
            var parts = [];
            if (h > 0) parts.push(h + 'h');
            if (m > 0) parts.push(m + 'm');
            return parts.join(' ') || '0 min';
        },

        ratingStars: function(rating) {
            rating = parseFloat(rating) || 0;
            var full = Math.floor(rating);
            var half = rating - full >= 0.25 && rating - full < 0.75;
            var empty = Math.floor(5 - rating);
            var stars = '';
            for (var i = 0; i < full; i++) stars += '<i class="fas fa-star"></i>';
            if (half) stars += '<i class="fas fa-star-half-alt"></i>';
            for (var i = 0; i < empty; i++) stars += '<i class="far fa-star"></i>';
            return '<div class="rating-stars">' + stars + '</div>';
        },

        timeAgo: function(dateStr) {
            if (!dateStr) return '';
            var d = new Date(dateStr);
            if (isNaN(d.getTime())) return '';
            var now = new Date();
            var diff = Math.floor((now - d) / 1000);
            if (diff < 60) return diff + 's ago';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
            return Math.floor(diff / 604800) + 'w ago';
        }
    };

    // Sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.getElementById('menuToggle');
        var sidebar = document.getElementById('sidebar');
        if (toggle && sidebar) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 992 &&
                    !sidebar.contains(e.target) &&
                    !toggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            });
        }
    });

    // Profile dropdown
    document.addEventListener('click', function(e) {
        var profile = document.getElementById('profileDropdown');
        if (profile) {
            var menu = document.getElementById('profileMenu');
            if (menu) {
                if (profile.contains(e.target)) {
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                } else if (!menu.contains(e.target)) {
                    menu.style.display = 'none';
                }
            }
        }
    });
})();
