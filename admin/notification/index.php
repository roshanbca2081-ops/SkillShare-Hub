<?php
$page_title = 'Notifications';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

$readFilter = $_GET['read'] ?? '';
if (!in_array($readFilter, ['', '0', '1'], true)) {
	$readFilter = '';
}
$search = trim((string)($_GET['q'] ?? ''));
$conditions = [];
$params = [];
if ($readFilter !== '') {
	$conditions[] = 'n.is_read = ?';
	$params[] = (int)$readFilter;
}
if ($search !== '') {
	$conditions[] = '(n.title LIKE ? OR n.message LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)';
	for ($i = 0; $i < 4; $i++) {
		$params[] = '%' . $search . '%';
	}
}
$where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
$stmt = $pdo->prepare("SELECT n.id, n.type, n.title, n.message, n.is_read, n.created_at, u.full_name, u.email FROM notifications n LEFT JOIN users u ON u.id = n.user_id$where ORDER BY n.created_at DESC LIMIT 200");
$stmt->execute($params);
$notifications = $stmt->fetchAll();
$totalNotifications = (int)$pdo->query('SELECT COUNT(*) FROM notifications')->fetchColumn();
$unreadNotifications = (int)$pdo->query('SELECT COUNT(*) FROM notifications WHERE is_read = 0')->fetchColumn();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell"><?php include __DIR__ . '/../includes/sidebar.php'; ?><main class="admin-main">
	<div class="admin-topbar"><div><h1 class="admin-title">Notifications</h1><p class="admin-subtitle">Review delivery records across user accounts.</p></div></div>
	<div class="admin-kpis"><div class="admin-card admin-kpi"><span>Total notifications</span><strong><?php echo $totalNotifications; ?></strong></div><div class="admin-card admin-kpi"><span>Unread</span><strong><?php echo $unreadNotifications; ?></strong></div></div>
	<form method="GET" class="admin-card d-flex flex-wrap align-items-end gap-2 mb-3"><label class="form-label mb-0">Read status<select class="form-select" name="read"><option value="">All</option><option value="0" <?php echo $readFilter === '0' ? 'selected' : ''; ?>>Unread</option><option value="1" <?php echo $readFilter === '1' ? 'selected' : ''; ?>>Read</option></select></label><label class="form-label mb-0">Search<input class="form-control" type="search" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Title, message, or user"></label><button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?php echo appUrl('admin/notification/index.php'); ?>">Reset</a></form>
	<div class="admin-card admin-table-wrap"><table class="admin-table"><thead><tr><th>Notification</th><th>User</th><th>Type</th><th>Read</th><th>Created</th></tr></thead><tbody><?php foreach ($notifications as $notification): ?><tr><td><strong><?php echo htmlspecialchars($notification['title'], ENT_QUOTES, 'UTF-8'); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($notification['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small></td><td><?php echo htmlspecialchars($notification['full_name'] ?? 'Unknown user', ENT_QUOTES, 'UTF-8'); ?><br><small><?php echo htmlspecialchars($notification['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small></td><td><?php echo htmlspecialchars($notification['type'], ENT_QUOTES, 'UTF-8'); ?></td><td><span class="badge bg-<?php echo (int)$notification['is_read'] ? 'success' : 'warning'; ?>"><?php echo (int)$notification['is_read'] ? 'Read' : 'Unread'; ?></span></td><td><?php echo htmlspecialchars(date('M d, Y h:i A', strtotime($notification['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td></tr><?php endforeach; ?><?php if (!$notifications): ?><tr><td colspan="5" class="text-center text-muted py-4">No notifications found.</td></tr><?php endif; ?></tbody></table></div>
</main></div><?php include __DIR__ . '/../includes/footer.php'; ?>
