<?php
$page_title = 'Settings';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

$message = '';
$error = '';
$settings = [];
$keyColumn = '';
$valueColumn = '';
$legacySettings = false;
try {
	$columns = $pdo->query('SHOW COLUMNS FROM settings')->fetchAll(PDO::FETCH_COLUMN, 0);
	if (in_array('key', $columns, true) && in_array('value', $columns, true)) {
		$keyColumn = '`key`';
		$valueColumn = '`value`';
		$settings = $pdo->query('SELECT `key` AS setting_key, `value` AS setting_value, type AS setting_group, description FROM settings ORDER BY `key`')->fetchAll();
	} elseif (in_array('setting_key', $columns, true) && in_array('setting_value', $columns, true)) {
		$legacySettings = true;
		$keyColumn = 'setting_key';
		$valueColumn = 'setting_value';
		$settings = $pdo->query("SELECT setting_key, setting_value, setting_group, '' AS description FROM settings ORDER BY setting_group, setting_key")->fetchAll();
	} else {
		$error = 'The settings table uses an unsupported schema. Review the database migrations before editing settings.';
	}
} catch (Throwable $exception) {
	error_log('Admin settings load failed: ' . $exception->getMessage());
	$error = 'Settings are not available. Confirm that the settings table exists in the configured database.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $keyColumn !== '') {
	if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
		http_response_code(400);
		exit('Invalid request token. Please reload the page and try again.');
	}
	$key = trim((string)($_POST['setting_key'] ?? ''));
	$newValue = (string)($_POST['setting_value'] ?? '');
	if ($key === '' || preg_match('/secret|password|token|private|api[_-]?key/i', $key)) {
		$error = 'This setting cannot be edited from this screen.';
	} else {
		try {
			$update = $pdo->prepare("UPDATE settings SET $valueColumn = ? WHERE $keyColumn = ?");
			$update->execute([$newValue, $key]);
			$message = $update->rowCount() > 0 ? 'Setting saved.' : 'No setting was changed.';
			if ($legacySettings) {
				$settings = $pdo->query("SELECT setting_key, setting_value, setting_group, '' AS description FROM settings ORDER BY setting_group, setting_key")->fetchAll();
			} else {
				$settings = $pdo->query('SELECT `key` AS setting_key, `value` AS setting_value, type AS setting_group, description FROM settings ORDER BY `key`')->fetchAll();
			}
		} catch (Throwable $exception) {
			error_log('Admin settings update failed: ' . $exception->getMessage());
			$error = 'The setting could not be saved.';
		}
	}
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell"><?php include __DIR__ . '/../includes/sidebar.php'; ?><main class="admin-main">
	<div class="admin-topbar"><div><h1 class="admin-title">Settings</h1><p class="admin-subtitle">Edit existing non-secret application settings. Secrets remain hidden.</p></div></div>
	<?php if ($message): ?><div class="alert alert-success" role="status"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
	<?php if ($error): ?><div class="alert alert-warning" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
	<?php if (!$error || $settings): ?><div class="admin-card admin-table-wrap"><table class="admin-table"><thead><tr><th>Setting</th><th>Group / Type</th><th>Description</th><th>Value</th><th>Action</th></tr></thead><tbody><?php foreach ($settings as $setting): $sensitive = (bool)preg_match('/secret|password|token|private|api[_-]?key/i', $setting['setting_key']); ?><tr><td><strong><?php echo htmlspecialchars($setting['setting_key'], ENT_QUOTES, 'UTF-8'); ?></strong></td><td><?php echo htmlspecialchars($setting['setting_group'] ?? 'general', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($setting['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td><td><?php if ($sensitive): ?><span class="text-muted">Hidden sensitive value</span><?php else: ?><form method="POST" class="d-flex gap-2 align-items-start"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>"><input type="hidden" name="setting_key" value="<?php echo htmlspecialchars($setting['setting_key'], ENT_QUOTES, 'UTF-8'); ?>"><textarea class="form-control" name="setting_value" rows="2" aria-label="Value for <?php echo htmlspecialchars($setting['setting_key'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string)$setting['setting_value'], ENT_QUOTES, 'UTF-8'); ?></textarea><button class="btn btn-sm btn-primary" type="submit">Save</button></form><?php endif; ?></td><td><?php echo $sensitive ? 'Protected' : 'Editable'; ?></td></tr><?php endforeach; ?><?php if (!$settings): ?><tr><td colspan="5" class="text-center text-muted py-4">No settings are currently configured.</td></tr><?php endif; ?></tbody></table></div><?php endif; ?>
	<p class="text-muted small mt-3">Settings are saved only if the active database contains a compatible settings table. Database credentials and payment-provider secrets should be managed outside this page.</p>
</main></div><?php include __DIR__ . '/../includes/footer.php'; ?>
