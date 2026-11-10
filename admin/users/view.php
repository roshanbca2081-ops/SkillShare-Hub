<?php
$page_title = 'User details';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$stmt = $pdo->prepare('SELECT id, full_name, email, role, is_active, bio, title, location, created_at FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
	http_response_code(404);
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell">
	<?php include __DIR__ . '/../includes/sidebar.php'; ?>
	<main class="admin-main">
		<div class="admin-topbar"><div><h1 class="admin-title">User details</h1><p class="admin-subtitle">Account information only; credentials and private tokens are never displayed.</p></div><a class="btn btn-outline-secondary" href="<?php echo appUrl('admin/users/index.php'); ?>">Back to users</a></div>
		<?php if ($user): ?>
			<section class="admin-card" aria-label="User account details">
				<h2 class="h4 mb-3"><?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
				<dl class="row mb-0">
					<dt class="col-sm-3">Email</dt><dd class="col-sm-9"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></dd>
					<dt class="col-sm-3">Role</dt><dd class="col-sm-9"><?php echo htmlspecialchars(ucfirst($user['role']), ENT_QUOTES, 'UTF-8'); ?></dd>
					<dt class="col-sm-3">Access</dt><dd class="col-sm-9"><span class="badge bg-<?php echo (int)$user['is_active'] ? 'success' : 'secondary'; ?>"><?php echo (int)$user['is_active'] ? 'Active' : 'Disabled'; ?></span></dd>
					<dt class="col-sm-3">Title</dt><dd class="col-sm-9"><?php echo htmlspecialchars($user['title'] ?: 'Not provided', ENT_QUOTES, 'UTF-8'); ?></dd>
					<dt class="col-sm-3">Location</dt><dd class="col-sm-9"><?php echo htmlspecialchars($user['location'] ?: 'Not provided', ENT_QUOTES, 'UTF-8'); ?></dd>
					<dt class="col-sm-3">Joined</dt><dd class="col-sm-9"><?php echo htmlspecialchars(date('M d, Y', strtotime($user['created_at'])), ENT_QUOTES, 'UTF-8'); ?></dd>
					<dt class="col-sm-3">Bio</dt><dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($user['bio'] ?: 'Not provided', ENT_QUOTES, 'UTF-8')); ?></dd>
				</dl>
			</section>
		<?php else: ?><div class="alert alert-warning">User not found.</div><?php endif; ?>
	</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
