<?php
$page_title = 'Users';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

$roleFilter = $userRoleFilter ?? ($_GET['role'] ?? '');
$allowedRoles = ['admin', 'mentor', 'fresher'];
if (!in_array($roleFilter, $allowedRoles, true)) {
	$roleFilter = '';
}
$search = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$where = [];
$params = [];
if ($roleFilter !== '') {
	$where[] = 'role = ?';
	$params[] = $roleFilter;
}
if ($search !== '') {
	$where[] = '(full_name LIKE ? OR email LIKE ?)';
	$params[] = '%' . $search . '%';
	$params[] = '%' . $search . '%';
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
$countStmt = $pdo->prepare('SELECT COUNT(*) FROM users' . $whereSql);
$countStmt->execute($params);
$totalUsers = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalUsers / $perPage));
$page = min($page, $totalPages);
$userStmt = $pdo->prepare('SELECT id, full_name, email, role, is_active, created_at FROM users' . $whereSql . ' ORDER BY created_at DESC LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage));
$userStmt->execute($params);
$users = $userStmt->fetchAll();
$flash = $_SESSION['admin_user_flash'] ?? null;
unset($_SESSION['admin_user_flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
		http_response_code(400);
		exit('Invalid request token. Please reload the page and try again.');
	}
	$id = (int)($_POST['id'] ?? 0);
	$active = (int)($_POST['is_active'] ?? -1);
	$message = 'The requested account could not be updated.';
	if ($id > 0 && in_array($active, [0, 1], true) && $id !== (int)getUserId()) {
		$targetStmt = $pdo->prepare('SELECT role, is_active FROM users WHERE id = ? LIMIT 1');
		$targetStmt->execute([$id]);
		$target = $targetStmt->fetch();
		if ($target) {
			$canUpdate = true;
			if ($target['role'] === 'admin' && (int)$target['is_active'] === 1 && $active === 0) {
				$activeAdmins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1")->fetchColumn();
				$canUpdate = $activeAdmins > 1;
				if (!$canUpdate) {
					$message = 'The last active administrator cannot be disabled.';
				}
			}
			if ($canUpdate) {
				$updateStmt = $pdo->prepare('UPDATE users SET is_active = ? WHERE id = ?');
				$updateStmt->execute([$active, $id]);
				$message = 'Account access updated.';
			}
		}
	} elseif ($id === (int)getUserId()) {
		$message = 'You cannot change access for your current account.';
	}
	$_SESSION['admin_user_flash'] = $message;
	$redirectQuery = http_build_query(array_filter(['role' => $roleFilter, 'q' => $search]));
	header('Location: ' . appUrl('admin/users/index.php') . ($redirectQuery ? '?' . $redirectQuery : ''));
	exit;
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell">
	<?php include __DIR__ . '/../includes/sidebar.php'; ?>
	<main class="admin-main">
		<div class="admin-topbar"><div><h1 class="admin-title"><?php echo $roleFilter ? ucfirst($roleFilter) . 's' : 'Users'; ?></h1><p class="admin-subtitle">Find accounts and manage platform access. Your own account and the last active administrator are protected.</p></div></div>
		<?php if ($flash): ?><div class="alert alert-info" role="status"><?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
		<div class="admin-card mb-3">
			<form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
				<?php if (isset($userRoleFilter)): ?><input type="hidden" name="role" value="<?php echo htmlspecialchars($roleFilter, ENT_QUOTES, 'UTF-8'); ?>"><?php else: ?><label class="form-label mb-0">Role<select class="form-select" name="role"><option value="">All roles</option><?php foreach ($allowedRoles as $role): ?><option value="<?php echo $role; ?>" <?php echo $roleFilter === $role ? 'selected' : ''; ?>><?php echo ucfirst($role); ?></option><?php endforeach; ?></select></label><?php endif; ?>
				<label class="form-label mb-0">Search<input class="form-control" type="search" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Name or email"></label>
				<button class="btn btn-primary" type="submit">Filter</button><a class="btn btn-outline-secondary" href="<?php echo appUrl('admin/users/index.php'); ?>">Reset</a>
			</form>
		</div>
		<div class="admin-card admin-table-wrap"><table class="admin-table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th>Access</th><th>Actions</th></tr></thead><tbody>
		<?php foreach ($users as $user): ?><tr>
			<td><strong><?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?></strong></td><td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars(ucfirst($user['role']), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars(date('M d, Y', strtotime($user['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
			<td><span class="badge bg-<?php echo (int)$user['is_active'] ? 'success' : 'secondary'; ?>"><?php echo (int)$user['is_active'] ? 'Active' : 'Disabled'; ?></span></td>
			<td><div class="admin-actions"><a class="btn btn-sm btn-outline-secondary" href="<?php echo appUrl('admin/users/view.php?id=' . (int)$user['id']); ?>">View</a><?php if ((int)$user['id'] !== (int)getUserId()): ?><form method="POST"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>"><input type="hidden" name="is_active" value="<?php echo (int)$user['is_active'] ? 0 : 1; ?>"><button class="btn btn-sm btn-outline-primary" type="submit"><?php echo (int)$user['is_active'] ? 'Disable' : 'Enable'; ?></button></form><?php else: ?><small class="text-muted">Current account</small><?php endif; ?></div></td>
		</tr><?php endforeach; ?>
		<?php if (!$users): ?><tr><td colspan="6" class="text-center text-muted py-4">No matching users found.</td></tr><?php endif; ?>
		</tbody></table></div>
		<?php if ($totalPages > 1): ?><nav class="d-flex justify-content-between align-items-center mt-3" aria-label="User pages"><span class="text-muted">Page <?php echo $page; ?> of <?php echo $totalPages; ?> · <?php echo $totalUsers; ?> accounts</span><div class="d-flex gap-2"><?php if ($page > 1): ?><a class="btn btn-sm btn-outline-primary" href="?<?php echo htmlspecialchars(http_build_query(array_filter(['role' => $roleFilter, 'q' => $search, 'page' => $page - 1])), ENT_QUOTES, 'UTF-8'); ?>">Previous</a><?php endif; ?><?php if ($page < $totalPages): ?><a class="btn btn-sm btn-outline-primary" href="?<?php echo htmlspecialchars(http_build_query(array_filter(['role' => $roleFilter, 'q' => $search, 'page' => $page + 1])), ENT_QUOTES, 'UTF-8'); ?>">Next</a><?php endif; ?></div></nav><?php endif; ?>
	</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
