<?php
/**
 * Admin Navbar Include
 */
$unreadNotifications = 0;
$unreadMessages = 0;
try {
    $pdo = getDB();
    $unreadNotifications = (int)$pdo->query("SELECT COUNT(*) FROM notifications WHERE user_id = " . getUserId() . " AND is_read = 0")->fetchColumn();
    $unreadMessages = (int)$pdo->query("SELECT COUNT(*) FROM messages WHERE to_user = " . getUserId() . " AND is_read = 0")->fetchColumn();
} catch (Exception $e) {}
?>
<header class="admin-topbar">
    <div class="admin-topbar-left">
        <button class="admin-menu-toggle" id="adminMenuToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <h2 class="admin-page-title"><?php echo htmlspecialchars($pageTitle ?? 'Dashboard'); ?></h2>
    </div>
    <div class="admin-topbar-right">
        <a href="<?php echo ADMIN_URL; ?>notifications.php" class="admin-icon-btn" title="Notifications">
            <i class="fas fa-bell"></i>
            <?php if ($unreadNotifications > 0): ?>
                <span class="admin-badge"><?php echo $unreadNotifications; ?></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo ADMIN_URL; ?>messages.php" class="admin-icon-btn" title="Messages">
            <i class="fas fa-envelope"></i>
            <?php if ($unreadMessages > 0): ?>
                <span class="admin-badge"><?php echo $unreadMessages; ?></span>
            <?php endif; ?>
        </a>
        <div class="admin-profile-dropdown">
            <img src="<?php echo $avatarUrl; ?>" alt="Admin" class="admin-avatar">
            <span class="admin-user-name"><?php echo htmlspecialchars($adminName); ?></span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>
</header>
