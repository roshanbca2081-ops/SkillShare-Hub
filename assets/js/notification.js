// ============================================
// Notifications JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Mark notification as read
    document.querySelectorAll('.mark-read').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationId = this.dataset.id;
            markAsRead(notificationId);
        });
    });
    
    // Mark all as read
    const markAllBtn = document.getElementById('markAllRead');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markAllAsRead();
        });
    }
    
    // Delete notification
    document.querySelectorAll('.delete-notification').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Delete this notification?')) {
                const notificationId = this.dataset.id;
                deleteNotification(notificationId);
            }
        });
    });
    
    // Real-time notification polling
    if (document.getElementById('notificationBell')) {
        startNotificationPolling();
    }
});

// ============================================
// Mark as Read
// ============================================

function markAsRead(notificationId) {
    ajaxRequest('api/mark-read.php', 'POST', { id: notificationId })
        .then(data => {
            if (data.success) {
                const el = document.querySelector(`[data-id="${notificationId}"]`);
                if (el) {
                    el.closest('.notification-item').classList.remove('unread');
                    const badge = el.closest('.notification-item').querySelector('.badge');
                    if (badge) badge.remove();
                }
                updateNotificationCount();
            }
        })
        .catch(() => {
            showNotification('Failed to mark as read', 'error');
        });
}

// ============================================
// Mark All as Read
// ============================================

function markAllAsRead() {
    ajaxRequest('api/mark-all-read.php', 'POST')
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item.unread').forEach(el => {
                    el.classList.remove('unread');
                    const badge = el.querySelector('.badge');
                    if (badge) badge.remove();
                });
                updateNotificationCount();
                showNotification('All notifications marked as read', 'success');
            }
        })
        .catch(() => {
            showNotification('Failed to mark all as read', 'error');
        });
}

// ============================================
// Delete Notification
// ============================================

function deleteNotification(notificationId) {
    ajaxRequest('api/delete-notification.php', 'POST', { id: notificationId })
        .then(data => {
            if (data.success) {
                const el = document.querySelector(`[data-id="${notificationId}"]`);
                if (el) {
                    el.closest('.notification-item').remove();
                }
                updateNotificationCount();
                showNotification('Notification deleted', 'success');
            }
        })
        .catch(() => {
            showNotification('Failed to delete notification', 'error');
        });
}

// ============================================
// Update Notification Count
// ============================================

function updateNotificationCount() {
    const badge = document.getElementById('notificationBadge');
    if (!badge) return;
    
    const unreadItems = document.querySelectorAll('.notification-item.unread');
    const count = unreadItems.length;
    
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline';
    } else {
        badge.style.display = 'none';
    }
}

// ============================================
// Real-time Polling
// ============================================

function startNotificationPolling() {
    setInterval(() => {
        ajaxRequest('api/unread-count.php')
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    const count = data.count || 0;
                    if (count > 0) {
                        badge.textContent = count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(() => {
                // Silent fail
            });
    }, 30000); // Check every 30 seconds
}

// ============================================
// Show Notification Toast
// ============================================

function showNotificationToast(title, message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.role = 'alert';
    toast.ariaLive = 'assertive';
    toast.ariaAtomic = 'true';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}</strong>
                <br>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}