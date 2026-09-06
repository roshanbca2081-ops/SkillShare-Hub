<?php
$page_title = 'Academic Fields';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();
$fields = $pdo->query("SELECT f.*, (SELECT COUNT(*) FROM courses c WHERE c.field_id = f.id) AS course_count FROM academic_fields f ORDER BY f.name")->fetchAll();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell"><?php include __DIR__ . '/../includes/sidebar.php'; ?><main class="admin-main">
<div class="admin-topbar"><div><h1 class="admin-title">Academic Fields</h1><p class="admin-subtitle">Manage the categories used across courses and discovery.</p></div><a class="btn btn-primary" href="create.php"><i class="fas fa-plus"></i> Add field</a></div>
<div class="admin-card admin-table-wrap"><table class="admin-table"><thead><tr><th>Name</th><th>Slug</th><th>Courses</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($fields as $field): ?><tr><td><strong><?php echo htmlspecialchars($field['name']); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($field['description'] ?? ''); ?></small></td><td><?php echo htmlspecialchars($field['slug']); ?></td><td><?php echo (int)$field['course_count']; ?></td><td><span class="badge <?php echo $field['is_active'] ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $field['is_active'] ? 'Active' : 'Inactive'; ?></span></td><td><div class="admin-actions"><a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?php echo (int)$field['id']; ?>"><i class="fas fa-pen"></i></a><form method="POST" action="delete.php" onsubmit="return confirm('Delete this academic field?');"><input type="hidden" name="id" value="<?php echo (int)$field['id']; ?>"><button class="btn btn-sm btn-outline-danger" type="submit"><i class="fas fa-trash"></i></button></form></div></td></tr><?php endforeach; ?>
<?php if (!$fields): ?><tr><td colspan="5" class="text-center text-muted py-4">No academic fields found.</td></tr><?php endif; ?></tbody></table></div>
</main></div><?php include __DIR__ . '/../includes/footer.php'; ?>
