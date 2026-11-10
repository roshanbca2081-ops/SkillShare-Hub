<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../config/auth.php';
requireAdmin();

$stats = [];
foreach (['users', 'courses', 'academic_fields', 'sessions', 'bookings'] as $table) {
	$stats[$table] = (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
}
$pendingBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$recent = $pdo->query("SELECT s.title, s.scheduled_at, u.full_name AS mentor_name, s.status FROM sessions s JOIN users u ON u.id = s.mentor_id ORDER BY s.scheduled_at DESC LIMIT 6")->fetchAll();

$monthStart = new DateTimeImmutable('first day of this month 00:00:00');
$chartStart = $monthStart->modify('-5 months');
$growthLabels = [];
$monthKeys = [];
for ($offset = 0; $offset < 6; $offset++) {
	$month = $chartStart->modify('+' . $offset . ' months');
	$monthKeys[] = $month->format('Y-m');
	$growthLabels[] = $month->format('M Y');
}
$growthDatasets = [];
foreach ([
	'users' => ['label' => 'New users', 'color' => '#4f46e5'],
	'courses' => ['label' => 'New courses', 'color' => '#06b6d4'],
	'enrollments' => ['label' => 'Enrollments', 'color' => '#16a34a'],
] as $table => $series) {
	$counts = array_fill_keys($monthKeys, 0);
	$stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_key, COUNT(*) AS total FROM `$table` WHERE created_at >= ? AND created_at < ? GROUP BY month_key");
	$stmt->execute([$chartStart->format('Y-m-d H:i:s'), $monthStart->modify('+1 month')->format('Y-m-d H:i:s')]);
	foreach ($stmt->fetchAll() as $row) {
		if (array_key_exists($row['month_key'], $counts)) {
			$counts[$row['month_key']] = (int)$row['total'];
		}
	}
	$growthDatasets[] = [
		'label' => $series['label'],
		'data' => array_values($counts),
		'borderColor' => $series['color'],
		'backgroundColor' => $series['color'],
		'borderWidth' => 2,
		'pointRadius' => 3,
		'pointHoverRadius' => 5,
		'tension' => 0.35,
	];
}
$bookingStatuses = $pdo->query('SELECT status, COUNT(*) AS total FROM bookings GROUP BY status ORDER BY status')->fetchAll();
$bookingLabels = array_map(static function ($row) { return ucfirst($row['status']); }, $bookingStatuses);
$bookingValues = array_map(static function ($row) { return (int)$row['total']; }, $bookingStatuses);
$bookingColors = ['#f59e0b', '#16a34a', '#ef4444', '#64748b', '#4f46e5'];
$chartJsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="admin-shell">
	<?php include __DIR__ . '/includes/sidebar.php'; ?>
	<main class="admin-main">
		<div class="admin-topbar">
			<div><h1 class="admin-title">Admin dashboard</h1><p class="admin-subtitle">A live overview of the SkillShare Hub platform.</p></div>
			<span class="badge bg-success">System online</span>
		</div>

		<div class="admin-kpis">
			<div class="admin-card admin-kpi"><span>Users</span><strong><?php echo $stats['users']; ?></strong></div>
			<div class="admin-card admin-kpi"><span>Courses</span><strong><?php echo $stats['courses']; ?></strong></div>
			<div class="admin-card admin-kpi"><span>Academic fields</span><strong><?php echo $stats['academic_fields']; ?></strong></div>
			<div class="admin-card admin-kpi"><span>Pending bookings</span><strong><?php echo $pendingBookings; ?></strong></div>
		</div>

		<section class="admin-chart-grid" aria-label="Platform charts">
			<article class="admin-card admin-chart-card">
				<div class="admin-chart-heading"><div><h2>Platform growth</h2><p>New users, courses, and enrollments over the last six months</p></div></div>
				<div class="admin-chart-canvas"><canvas id="platformGrowthChart" role="img" aria-label="Line chart showing monthly new users, courses, and enrollments"></canvas></div>
			</article>
			<article class="admin-card admin-chart-card">
				<div class="admin-chart-heading"><div><h2>Booking status</h2><p><?php echo $stats['bookings']; ?> total bookings</p></div></div>
				<div class="admin-chart-canvas admin-chart-canvas-doughnut"><canvas id="bookingStatusChart" role="img" aria-label="Doughnut chart showing bookings by status"></canvas></div>
			</article>
		</section>

		<div class="admin-card admin-table-wrap">
			<div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h5 mb-0">Recent sessions</h2><a href="session/index.php" class="btn btn-sm btn-outline-primary">Manage sessions</a></div>
			<table class="admin-table"><thead><tr><th>Session</th><th>Mentor</th><th>Scheduled</th><th>Status</th></tr></thead><tbody>
				<?php foreach ($recent as $item): ?><tr><td><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($item['mentor_name'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars(date('M d, Y h:i A', strtotime($item['scheduled_at'])), ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars(ucfirst($item['status']), ENT_QUOTES, 'UTF-8'); ?></td></tr><?php endforeach; ?>
				<?php if (!$recent): ?><tr><td colspan="4" class="text-muted text-center">No sessions yet.</td></tr><?php endif; ?>
			</tbody></table>
		</div>
	</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
	if (typeof Chart === 'undefined') return;
	const getChartTheme = () => {
		const styles = getComputedStyle(document.documentElement);
		return {
			text: styles.getPropertyValue('--app-muted').trim(),
			border: styles.getPropertyValue('--app-border').trim()
		};
	};
	const chartTheme = getChartTheme();
	const charts = [];
	const growthCanvas = document.getElementById('platformGrowthChart');
	if (growthCanvas) {
		charts.push(new Chart(growthCanvas, {
			type: 'line',
			data: {
				labels: <?php echo json_encode($growthLabels, $chartJsonFlags); ?>,
				datasets: <?php echo json_encode($growthDatasets, $chartJsonFlags); ?>
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				interaction: { intersect: false, mode: 'index' },
				plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 18, color: chartTheme.text } } },
				scales: { y: { beginAtZero: true, ticks: { precision: 0, color: chartTheme.text }, grid: { color: chartTheme.border } }, x: { ticks: { color: chartTheme.text }, grid: { display: false } } }
			}
		}));
	}
	const bookingCanvas = document.getElementById('bookingStatusChart');
	if (bookingCanvas) {
		charts.push(new Chart(bookingCanvas, {
			type: 'doughnut',
			data: {
				labels: <?php echo json_encode($bookingLabels, $chartJsonFlags); ?>,
				datasets: [{ data: <?php echo json_encode($bookingValues, $chartJsonFlags); ?>, backgroundColor: <?php echo json_encode($bookingColors, $chartJsonFlags); ?>, borderWidth: 0, hoverOffset: 5 }]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				cutout: '66%',
				plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16, color: chartTheme.text } } }
			}
		}));
	}
	document.addEventListener('themechange', function () {
		const theme = getChartTheme();
		charts.forEach(chart => {
			if (chart.options.plugins?.legend?.labels) chart.options.plugins.legend.labels.color = theme.text;
			Object.values(chart.options.scales || {}).forEach(scale => {
				if (scale.ticks) scale.ticks.color = theme.text;
				if (scale.grid && scale.grid.display !== false) scale.grid.color = theme.border;
			});
			chart.update();
		});
	});
});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
