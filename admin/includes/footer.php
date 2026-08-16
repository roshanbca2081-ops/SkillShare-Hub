<?php
/**
 * Admin Footer Include
 * Closes HTML tags and includes scripts
 */
$adminUser = getAdminUser();
?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<script>
// Admin global utilities
window.AdminPanel = {
    baseUrl: '<?php echo ADMIN_URL; ?>',
    apiUrl: '<?php echo BASE_URL; ?>api/',
    siteUrl: '<?php echo BASE_URL; ?>',
    escapeHtml: function(text) {
        if (text === null || text === undefined) return '';
        var div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    },
    showToast: function(title, message, type, duration) {
        type = type || 'info';
        duration = duration || 4000;
        var container = document.getElementById('adminToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'adminToastContainer';
            container.className = 'admin-toast-container';
            document.body.appendChild(container);
        }
        var icons = { success: 'check-circle', error: 'exclamation-circle', warning: 'exclamation-triangle', info: 'info-circle' };
        var toast = document.createElement('div');
        toast.className = 'admin-toast ' + type;
        toast.innerHTML = '<div class="admin-toast-icon"><i class="fas fa-' + (icons[type] || 'info-circle') + '"></i></div><div class="admin-toast-content"><div class="admin-toast-title">' + title + '</div><div class="admin-toast-message">' + message + '</div></div><button class="admin-toast-close">&times;</button>';
        container.appendChild(toast);
        toast.querySelector('.admin-toast-close').onclick = function() { toast.remove(); };
        setTimeout(function() { if (toast.parentNode) toast.remove(); }, duration);
    },
    confirm: function(message) {
        return confirm(message || 'Are you sure?');
    },
    formatDate: function(dateStr) {
        if (!dateStr) return '';
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return d.getDate() + ' ' + months[d.getMonth()] + ', ' + d.getFullYear();
    },
    formatDateTime: function(dateStr) {
        if (!dateStr) return '';
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var h = d.getHours();
        var m = d.getMinutes();
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        if (h === 0) h = 12;
        return d.getDate() + ' ' + months[d.getMonth()] + ', ' + d.getFullYear() + ' ' + h + ':' + (m < 10 ? '0' : '') + m + ' ' + ampm;
    },
    formatCurrency: function(amount) {
        return '$' + parseFloat(amount || 0).toFixed(2);
    }
};

// Sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('adminMenuToggle');
    var sidebar = document.getElementById('adminSidebar');
    var close = document.getElementById('sidebarClose');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function() { sidebar.classList.toggle('open'); });
        if (close) close.addEventListener('click', function() { sidebar.classList.remove('open'); });
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }
});
</script>
<script src="<?php echo ADMIN_ASSETS_URL; ?>js/admin.js"></script>
</body>
</html>
