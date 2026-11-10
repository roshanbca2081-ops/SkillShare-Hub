<?php
$page_title = 'Booking report';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();
$bookingCounts = $pdo->query('SELECT status, COUNT(*) AS total FROM bookings GROUP BY status ORDER BY status')->fetchAll();
$bookings = $pdo->query("SELECT b.id, b.status, b.created_at, f.full_name AS fresher_name, s.title AS session_title, m.full_name AS mentor_name FROM bookings b LEFT JOIN users f ON f.id = b.fresher_id LEFT JOIN sessions s ON s.id = b.session_id LEFT JOIN users m ON m.id = s.mentor_id ORDER BY b.created_at DESC LIMIT 100")->fetchAll();
$totalBookings = (int)$pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell"><?php include __DIR__ . '/../includes/sidebar.php'; ?><main class="admin-main">
	<div class="admin-topbar"><div><h1 class="admin-title">Booking report</h1><p class="admin-subtitle">Booking volume by status and the latest requests.</p></div><a class="btn btn-outline-secondary" href="index.php">All reports</a></div>
	<div class="admin-kpis"><div class="admin-card admin-kpi"><span>Total bookings</span><strong><?php echo $totalBookings; ?></strong></div><?php foreach ($bookingCounts as $row): ?><div class="admin-card admin-kpi"><span><?php echo htmlspecialchars(ucfirst($row['status']), ENT_QUOTES, 'UTF-8'); ?></span><strong><?php echo (int)$row['total']; ?></strong></div><?php endforeach; ?></div>
	<div class="admin-card admin-table-wrap"><table class="admin-table"><thead><tr><th>Learner</th><th>Session</th><th>Mentor</th><th>Status</th><th>Created</th></tr></thead><tbody><?php foreach ($bookings as $booking): ?><tr><td><?php echo htmlspecialchars($booking['fresher_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($booking['session_title'] ?? 'Unavailable', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($booking['mentor_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars(ucfirst($booking['status']), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars(date('M d, Y', strtotime($booking['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td></tr><?php endforeach; ?><?php if (!$bookings): ?><tr><td colspan="5" class="text-center text-muted">No booking records.</td></tr><?php endif; ?></tbody></table></div>
</main></div><?php include __DIR__ . '/../includes/footer.php'; ?>
