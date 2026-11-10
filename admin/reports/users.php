<?php
$page_title = 'User report';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();
$users = $pdo->query("SELECT role, is_active, COUNT(*) AS total FROM users GROUP BY role, is_active ORDER BY role, is_active DESC")->fetchAll();
$totalUsers = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$activeUsers = (int)$pdo->query('SELECT COUNT(*) FROM users WHERE is_active = 1')->fetchColumn();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell"><?php include __DIR__ . '/../includes/sidebar.php'; ?><main class="admin-main">
	<div class="admin-topbar"><div><h1 class="admin-title">User report</h1><p class="admin-subtitle">Accounts grouped by role and access status.</p></div><a class="btn btn-outline-secondary" href="index.php">All reports</a></div>
	<div class="admin-kpis"><div class="admin-card admin-kpi"><span>Total accounts</span><strong><?php echo $totalUsers; ?></strong></div><div class="admin-card admin-kpi"><span>Active accounts</span><strong><?php echo $activeUsers; ?></strong></div></div>
	<div class="admin-card admin-table-wrap"><table class="admin-table"><thead><tr><th>Role</th><th>Access</th><th>Accounts</th></tr></thead><tbody><?php foreach ($users as $row): ?><tr><td><?php echo htmlspecialchars(ucfirst($row['role']), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo (int)$row['is_active'] ? 'Active' : 'Disabled'; ?></td><td><?php echo (int)$row['total']; ?></td></tr><?php endforeach; ?><?php if (!$users): ?><tr><td colspan="3" class="text-center text-muted">No user records.</td></tr><?php endif; ?></tbody></table></div>
</main></div><?php include __DIR__ . '/../includes/footer.php'; ?>
