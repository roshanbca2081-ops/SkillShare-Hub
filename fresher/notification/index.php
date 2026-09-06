<?php
$page_title = 'Notifications';
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/functions.php';
require_once '../../config/auth.php';

requireFresher();

$user_id = getUserId();

// Get notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

// Mark all as read
if (isset($_GET['mark_all'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ?");
    $stmt->execute([$user_id]);
    redirect('index.php');
}

// Mark single as read
if (isset($_GET['read']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    redirect('index.php');
}

// Delete notification
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    redirect('index.php');
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
            <?php include '../../includes/sidebar.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Notifications</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <?php if (!empty($notifications)): ?>
                        <a href="?mark_all=1" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-check-double"></i> Mark All Read
                        </a>
                    <?php endif; ?>
                    <a href="../dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                </div>
            </div>
            
            <?php if (!empty($notifications)): ?>
                <div class="notifications-list">
                    <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?> border-bottom py-3">
                        <div class="d-flex align-items-start">
                            <div class="notification-item-icon notification-item-icon-<?php echo $notification['type'] ?? 'info'; ?> me-3">
                                <i class="fas fa-<?php echo $notification['icon'] ?? 'bell'; ?>"></i>
                            </div>
                            <div class="notification-item-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="notification-item-title"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                        <p class="notification-item-message"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        <small class="notification-item-time"><?php echo getTimeAgo($notification['created_at']); ?></small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if (!$notification['is_read']): ?>
                                            <a href="?read=1&id=<?php echo $notification['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="?delete=1&id=<?php echo $notification['id']; ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Delete this notification?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php if ($notification['link']): ?>
                                    <a href="<?php echo $notification['link']; ?>" class="btn btn-sm btn-primary mt-2">
                                        View Details
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                    <h5>No notifications</h5>
                    <p class="text-muted">You're all caught up!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>