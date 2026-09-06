<?php
$page_title = 'Add Academic Field';
require_once __DIR__ . '/../../config/database.php'; require_once __DIR__ . '/../../config/session.php'; require_once __DIR__ . '/../../config/functions.php'; require_once __DIR__ . '/../../config/auth.php'; requireAdmin();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = trim($_POST['name'] ?? ''); $slug = createSlug($name); $description = trim($_POST['description'] ?? ''); $icon = trim($_POST['icon'] ?? 'fa-book'); $color = trim($_POST['color'] ?? '#4f46e5'); $active = isset($_POST['is_active']) ? 1 : 0;
	if ($name === '') $errors[] = 'Field name is required.';
	if (!$errors) { $stmt = $pdo->prepare('INSERT INTO academic_fields (name, description, icon, color, slug, is_active) VALUES (?, ?, ?, ?, ?, ?)'); $stmt->execute([$name, $description, $icon, $color, $slug, $active]); header('Location: index.php'); exit; }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?><div class="admin-shell"><?php include __DIR__ . '/../includes/sidebar.php'; ?><main class="admin-main">
<div class="admin-topbar"><div><h1 class="admin-title">Add academic field</h1><p class="admin-subtitle">Create a category for courses and learner discovery.</p></div><a class="btn btn-outline-secondary" href="index.php">Back</a></div>
<div class="admin-card"><form method="POST"><div class="admin-form-grid"><div><label for="name">Name</label><input id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"></div><div><label for="icon">Font Awesome icon</label><input id="icon" name="icon" value="<?php echo htmlspecialchars($_POST['icon'] ?? 'fa-book'); ?>"></div><div><label for="color">Accent color</label><input id="color" type="color" name="color" value="<?php echo htmlspecialchars($_POST['color'] ?? '#4f46e5'); ?>"></div><div class="full"><label for="description">Description</label><textarea id="description" name="description"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea></div></div><?php foreach($errors as $error): ?><div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div><?php endforeach; ?><button class="btn btn-primary mt-3" type="submit"><i class="fas fa-save"></i> Create field</button></form></div>
</main></div><?php include __DIR__ . '/../includes/footer.php'; ?>
