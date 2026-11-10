<?php
$page_title = 'Edit Academic Field';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM academic_fields WHERE id = ?');
$stmt->execute([$id]);
$field = $stmt->fetch();
if (!$field) {
	$_SESSION['alert'] = ['type' => 'warning', 'icon' => 'exclamation-circle', 'message' => 'Academic field not found.'];
	header('Location: index.php');
	exit;
}

$errors = [];
$name = trim((string)($_POST['name'] ?? $field['name']));
$description = trim((string)($_POST['description'] ?? $field['description'] ?? ''));
$icon = trim((string)($_POST['icon'] ?? $field['icon'] ?? 'fa-book'));
$color = trim((string)($_POST['color'] ?? $field['color'] ?? '#4f46e5'));
$active = $_SERVER['REQUEST_METHOD'] === 'POST' ? (isset($_POST['is_active']) ? 1 : 0) : (int)$field['is_active'];
$hasSlug = (bool)$pdo->query("SHOW COLUMNS FROM academic_fields LIKE 'slug'")->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
		$errors[] = 'Your session token expired. Refresh the page and try again.';
	}
	if ($name === '') {
		$errors[] = 'Field name is required.';
	} elseif (function_exists('mb_strlen') ? mb_strlen($name, 'UTF-8') > 100 : strlen($name) > 100) {
		$errors[] = 'Field name must be 100 characters or fewer.';
	}
	if (strlen($icon) > 50) {
		$errors[] = 'Icon value must be 50 characters or fewer.';
	}
	if (strlen($color) > 20) {
		$errors[] = 'Color value must be 20 characters or fewer.';
	}
	if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
		$errors[] = 'Choose a valid six-digit hex color.';
	}

	if (!$errors) {
		try {
			if ($hasSlug) {
				$slug = createUniqueAcademicFieldSlug($pdo, $name, $id);
				$stmt = $pdo->prepare('UPDATE academic_fields SET name = ?, description = ?, icon = ?, color = ?, slug = ?, is_active = ? WHERE id = ?');
				$stmt->execute([$name, $description, $icon, $color, $slug, $active, $id]);
			} else {
				$stmt = $pdo->prepare('UPDATE academic_fields SET name = ?, description = ?, icon = ?, color = ?, is_active = ? WHERE id = ?');
				$stmt->execute([$name, $description, $icon, $color, $active, $id]);
			}
			$_SESSION['alert'] = ['type' => 'success', 'icon' => 'check-circle', 'message' => 'Academic field updated successfully.'];
			header('Location: index.php');
			exit;
		} catch (PDOException $e) {
			error_log('Academic field update failed: ' . $e->getMessage());
			$errors[] = (string)$e->getCode() === '23000'
				? 'A field with this name or slug already exists. Please choose another name.'
				: 'The academic field could not be updated. Check the field values and try again.';
		}
	}
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell">
	<?php include __DIR__ . '/../includes/sidebar.php'; ?>
	<main class="admin-main">
		<div class="admin-topbar">
			<div><h1 class="admin-title">Edit academic field</h1><p class="admin-subtitle">Update the field label and discovery metadata.</p></div>
			<a class="btn btn-outline-secondary" href="index.php">Back</a>
		</div>
		<div class="admin-card">
			<form method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<div class="admin-form-grid">
					<div><label for="name">Name</label><input id="name" name="name" maxlength="100" required value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"></div>
					<div><label for="icon">Font Awesome icon</label><input id="icon" name="icon" maxlength="50" value="<?php echo htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'); ?>"></div>
					<div><label for="color">Accent color</label><input id="color" type="color" name="color" value="<?php echo htmlspecialchars($color, ENT_QUOTES, 'UTF-8'); ?>"></div>
					<div><label><input type="checkbox" name="is_active" <?php echo $active ? 'checked' : ''; ?>> Active</label></div>
					<div class="full"><label for="description">Description</label><textarea id="description" name="description"><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea></div>
				</div>
				<?php foreach ($errors as $error): ?><div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
				<button class="btn btn-primary mt-3" type="submit"><i class="fas fa-save"></i> Save changes</button>
			</form>
		</div>
	</main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
