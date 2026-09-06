<?php
$page_title = 'Courses';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

$status = $_GET['status'] ?? '';
$level = $_GET['level'] ?? '';
$search = trim($_GET['search'] ?? '');
$where = [];
$params = [];
if (in_array($status, ['active', 'pending', 'draft', 'inactive', 'archived'], true)) {
    $where[] = 'c.status = ?';
    $params[] = $status;
}
if (in_array($level, ['beginner', 'intermediate', 'advanced'], true)) {
    $where[] = 'c.level = ?';
    $params[] = $level;
}
if ($search !== '') {
    $where[] = '(c.title LIKE ? OR u.full_name LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
$query = "SELECT c.*, u.full_name AS mentor_name, f.name AS field_name
          FROM courses c
          JOIN users u ON u.id = c.mentor_id
          LEFT JOIN academic_fields f ON f.id = c.field_id";
if ($where) {
    $query .= ' WHERE ' . implode(' AND ', $where);
}
$query .= ' ORDER BY c.created_at DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();
$totalCourses = (int) $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$activeCourses = (int) $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn();
$pendingCourses = (int) $pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'pending'")->fetchColumn();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="admin-main courses-admin-main">
        <header class="courses-topbar">
            <div class="courses-topbar-left">
                <button class="courses-mobile-toggle" type="button" aria-label="Toggle admin navigation"><i class="fas fa-bars"></i></button>
                <div>
                    <h1 class="admin-title">Course Management</h1>
                    <nav class="courses-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo appUrl('admin/dashboard.php'); ?>">Home</a><span>/</span><strong>Courses</strong></nav>
                </div>
            </div>
            <div class="courses-topbar-right"><span class="courses-admin-avatar"><i class="fas fa-user-shield"></i></span><span><strong>Administrator</strong><small>Admin workspace</small></span></div>
        </header>
        <div class="courses-page-header"><div><p class="courses-eyebrow"><i class="fas fa-book-open"></i> Catalogue</p><h2>All Courses</h2><p>Review, publish, and maintain the learning catalogue.</p></div><div class="courses-actions"><a class="btn btn-primary" href="create.php"><i class="fas fa-plus"></i> Add Course</a></div></div>
        <div class="courses-stats"><div class="courses-stat-card"><span class="courses-stat-icon purple"><i class="fas fa-book"></i></span><div><strong><?php echo $totalCourses; ?></strong><small>Total Courses</small></div></div><div class="courses-stat-card"><span class="courses-stat-icon green"><i class="fas fa-circle-check"></i></span><div><strong><?php echo $activeCourses; ?></strong><small>Active Courses</small></div></div><div class="courses-stat-card"><span class="courses-stat-icon yellow"><i class="fas fa-clock"></i></span><div><strong><?php echo $pendingCourses; ?></strong><small>Pending Review</small></div></div></div>
        <form class="courses-filter-bar" method="GET"><select name="status" aria-label="Filter by status"><option value="">All Status</option><?php foreach (['active', 'pending', 'draft', 'inactive', 'archived'] as $option): ?><option value="<?php echo $option; ?>" <?php echo $status === $option ? 'selected' : ''; ?>><?php echo ucfirst($option); ?></option><?php endforeach; ?></select><select name="level" aria-label="Filter by level"><option value="">All Levels</option><?php foreach (['beginner', 'intermediate', 'advanced'] as $option): ?><option value="<?php echo $option; ?>" <?php echo $level === $option ? 'selected' : ''; ?>><?php echo ucfirst($option); ?></option><?php endforeach; ?></select><div class="courses-search"><i class="fas fa-search"></i><input name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search courses or mentors..."></div><button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button><a class="btn btn-outline-secondary" href="index.php"><i class="fas fa-rotate-left"></i> Reset</a></form>
        <section class="courses-table-card"><div class="courses-table-heading"><div><h3>Course catalogue</h3><p><?php echo count($courses); ?> result<?php echo count($courses) === 1 ? '' : 's'; ?> shown</p></div><span class="courses-live-label"><i class="fas fa-database"></i> Live database</span></div><div class="table-responsive"><table class="courses-table"><thead><tr><th>Course</th><th>Mentor</th><th>Field</th><th>Level</th><th>Students</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($courses as $course): ?>
<?php $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $course['title']), 0, 2)); ?>
<tr><td><div class="course-name-cell"><span class="course-thumb-fallback"><?php echo htmlspecialchars($initials ?: 'CR'); ?></span><div><strong><?php echo htmlspecialchars($course['title']); ?></strong><small><?php echo (int) $course['duration']; ?> hours · <?php echo htmlspecialchars(ucfirst($course['level'])); ?></small></div></div></td><td><?php echo htmlspecialchars($course['mentor_name']); ?></td><td><?php echo htmlspecialchars($course['field_name'] ?? 'General'); ?></td><td><span class="level-pill level-<?php echo htmlspecialchars($course['level']); ?>"><?php echo ucfirst($course['level']); ?></span></td><td><?php echo number_format((int) $course['total_students']); ?></td><td><strong>$<?php echo number_format((float) $course['price'], 2); ?></strong></td><td><span class="status-pill status-<?php echo htmlspecialchars($course['status']); ?>"><?php echo ucfirst($course['status']); ?></span></td><td><div class="course-action-buttons"><a href="<?php echo appUrl('public/course-detail.php?id=' . (int) $course['id']); ?>" class="course-action view" title="View course"><i class="fas fa-eye"></i></a><a href="edit.php?id=<?php echo (int) $course['id']; ?>" class="course-action edit" title="Edit course"><i class="fas fa-pen"></i></a><form method="POST" action="delete.php" onsubmit="return confirm('Delete or archive this course?');"><input type="hidden" name="id" value="<?php echo (int) $course['id']; ?>"><button class="course-action delete" title="Delete course" type="submit"><i class="fas fa-trash"></i></button></form></div></td></tr>
<?php endforeach; ?>
<?php if (!$courses): ?><tr><td colspan="8" class="courses-empty"><i class="fas fa-book-open"></i><strong>No courses found</strong><span>Try changing the filters or add a new course.</span></td></tr><?php endif; ?>
</tbody></table></div></section>
    </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
