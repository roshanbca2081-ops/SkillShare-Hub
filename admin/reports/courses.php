<?php
$page_title = 'Course report';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();
$courses = $pdo->query("SELECT c.id, c.title, c.status, c.price, c.created_at, u.full_name AS mentor_name FROM courses c LEFT JOIN users u ON u.id = c.mentor_id ORDER BY c.created_at DESC LIMIT 100")->fetchAll();
$totalCourses = (int)$pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$activeCourses = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell"><?php include __DIR__ . '/../includes/sidebar.php'; ?><main class="admin-main">
	<div class="admin-topbar"><div><h1 class="admin-title">Course report</h1><p class="admin-subtitle">Latest courses, their publishing status, and instructors.</p></div><a class="btn btn-outline-secondary" href="index.php">All reports</a></div>
	<div class="admin-kpis"><div class="admin-card admin-kpi"><span>Total courses</span><strong><?php echo $totalCourses; ?></strong></div><div class="admin-card admin-kpi"><span>Active courses</span><strong><?php echo $activeCourses; ?></strong></div></div>
	<div class="admin-card admin-table-wrap"><table class="admin-table"><thead><tr><th>Course</th><th>Mentor</th><th>Status</th><th>Price</th><th>Created</th></tr></thead><tbody><?php foreach ($courses as $course): ?><tr><td><?php echo htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($course['mentor_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars(ucfirst($course['status']), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo number_format((float)$course['price'], 2); ?></td><td><?php echo htmlspecialchars(date('M d, Y', strtotime($course['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td></tr><?php endforeach; ?><?php if (!$courses): ?><tr><td colspan="5" class="text-center text-muted">No course records.</td></tr><?php endif; ?></tbody></table></div>
</main></div><?php include __DIR__ . '/../includes/footer.php'; ?>
