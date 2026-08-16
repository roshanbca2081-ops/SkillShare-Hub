<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Notifications';
$pdo = getDB();

$sql = "SELECT n.*, u.full_name as user_name FROM notifications n JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC LIMIT 100";
$notifications = $pdo->query($sql)->fetchAll();

$totalNotifications = (int)$pdo->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
$unreadNotifications = (int)$pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Notification Management</h3>
        <button class="admin-btn admin-btn-primary admin-btn-sm" onclick="openNotificationModal()"><i class="fas fa-plus"></i> Send Notification</button>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-bell"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalNotifications); ?></div>
                <div class="admin-stat-label">Total Notifications</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i class="fas fa-envelope"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($unreadNotifications); ?></div>
                <div class="admin-stat-label">Unread</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Read</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($notifications)): ?>
                    <tr><td colspan="7" class="admin-empty-state"><i class="fas fa-bell"></i><p>No notifications found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                        <tr>
                            <td>#<?php echo $notif['id']; ?></td>
                            <td><?php echo htmlspecialchars($notif['user_name']); ?></td>
                            <td><span class="admin-badge admin-badge-info"><?php echo htmlspecialchars($notif['type'] ?: 'general'); ?></span></td>
                            <td><?php echo htmlspecialchars(truncate($notif['payload'] ?: '', 80)); ?></td>
                            <td>
                                <span class="admin-badge <?php echo $notif['is_read'] ? 'admin-badge-secondary' : 'admin-badge-warning'; ?>">
                                    <?php echo $notif['is_read'] ? 'Read' : 'Unread'; ?>
                                </span>
                            </td>
                            <td><?php echo admin_time_ago($notif['created_at']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="admin-action-btn delete" title="Delete" onclick="adminConfirmDelete(<?php echo $notif['id']; ?>, '<?php echo ADMIN_URL; ?>actions/user-actions.php?action=delete_notification', 'Delete this notification?')"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Send Notification Modal -->
<div class="admin-modal-overlay" id="notificationModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h5>Send Notification</h5>
            <button class="admin-modal-close" onclick="adminCloseModal('notificationModal')">&times;</button>
        </div>
        <div class="admin-modal-body">
            <form id="notificationForm">
                <?php echo admin_csrf_field(); ?>
                <div class="admin-form-group">
                    <label class="admin-form-label">Target Audience</label>
                    <select name="target" id="notif_target" class="admin-form-control">
                        <option value="all">All Users</option>
                        <option value="mentors">All Mentors</option>
                        <option value="freshers">All Freshers</option>
                        <option value="selected">Selected Users</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Type</label>
                    <select name="type" id="notif_type" class="admin-form-control">
                        <option value="announcement">Announcement</option>
                        <option value="booking">Booking Reminder</option>
                        <option value="system">System</option>
                        <option value="course">Course Update</option>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Message</label>
                    <textarea name="message" id="notif_message" class="admin-form-control" rows="4" required placeholder="Enter notification message..."></textarea>
                </div>
            </form>
        </div>
        <div class="admin-modal-footer">
            <button class="admin-btn admin-btn-secondary" onclick="adminCloseModal('notificationModal')">Cancel</button>
            <button class="admin-btn admin-btn-primary" onclick="sendNotification()">Send</button>
        </div>
    </div>
</div>

<script>
function openNotificationModal() {
    document.getElementById('notificationForm').reset();
    adminOpenModal('notificationModal');
}

function sendNotification() {
    var form = document.getElementById('notificationForm');
    var formData = new FormData(form);
    var data = {};
    formData.forEach(function(v, k) { data[k] = v; });
    
    AdminUtils.apiFetch(AdminPanel.baseUrl + 'actions/notification-actions.php?action=send', {
        method: 'POST',
        body: data
    }).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', 'Notification sent', 'success');
            adminCloseModal('notificationModal');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

