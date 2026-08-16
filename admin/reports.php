<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Reports';
$pdo = getDB();

$period = $_GET['period'] ?? 'month';
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

$dateCondition = "created_at BETWEEN '$from 00:00:00' AND '$to 23:59:59'";

$reportData = [
    'users' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE $dateCondition")->fetchColumn(),
    'mentors' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mentor' AND $dateCondition")->fetchColumn(),
    'freshers' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'fresher' AND $dateCondition")->fetchColumn(),
    'bookings' => (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE $dateCondition")->fetchColumn(),
    'sessions' => (int)$pdo->query("SELECT COUNT(*) FROM sessions WHERE $dateCondition")->fetchColumn(),
    'payments' => (int)$pdo->query("SELECT COUNT(*) FROM payments WHERE $dateCondition")->fetchColumn(),
    'revenue' => (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid' AND $dateCondition")->fetchColumn(),
    'certificates' => (int)$pdo->query("SELECT COUNT(*) FROM certificates WHERE $dateCondition")->fetchColumn(),
    'research' => (int)$pdo->query("SELECT COUNT(*) FROM research WHERE $dateCondition")->fetchColumn(),
    'feedback' => (int)$pdo->query("SELECT COUNT(*) FROM reviews WHERE $dateCondition")->fetchColumn(),
];

$topCourses = $pdo->query("
    SELECT c.name, COUNT(b.id) as bookings, AVG(r.rating) as avg_rating
    FROM courses c
    LEFT JOIN bookings b ON c.id = b.course_id AND b.$dateCondition
    LEFT JOIN reviews r ON c.id = r.mentor_id
    GROUP BY c.id
    ORDER BY bookings DESC
    LIMIT 10
")->fetchAll();

$topMentors = $pdo->query("
    SELECT u.full_name, COUNT(b.id) as bookings, AVG(r.rating) as avg_rating
    FROM users u
    LEFT JOIN bookings b ON u.id = b.mentor_id AND b.$dateCondition
    LEFT JOIN reviews r ON u.id = r.mentor_id
    WHERE u.role = 'mentor'
    GROUP BY u.id
    ORDER BY bookings DESC
    LIMIT 10
")->fetchAll();

$dailyBookings = $pdo->query("
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM bookings
    WHERE $dateCondition
    GROUP BY DATE(created_at)
    ORDER BY date ASC
    LIMIT 30
")->fetchAll();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Reports & Analytics</h3>
        <div>
            <button class="admin-btn admin-btn-secondary admin-btn-sm" onclick="exportCSV()"><i class="fas fa-download"></i> Export CSV</button>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                <select name="period" class="admin-filter-select" onchange="setDateRange(this.value)">
                    <option value="today" <?php echo $period === 'today' ? 'selected' : ''; ?>>Today</option>
                    <option value="week" <?php echo $period === 'week' ? 'selected' : ''; ?>>This Week</option>
                    <option value="month" <?php echo $period === 'month' ? 'selected' : ''; ?>>This Month</option>
                    <option value="year" <?php echo $period === 'year' ? 'selected' : ''; ?>>This Year</option>
                    <option value="custom" <?php echo $period === 'custom' ? 'selected' : ''; ?>>Custom</option>
                </select>
                <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>" class="admin-form-control" style="width:auto;">
                <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>" class="admin-form-control" style="width:auto;">
                <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">Apply</button>
            </form>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;"><i class="fas fa-users"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($reportData['users']); ?></div>
                <div class="admin-stat-label">New Users</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-calendar-check"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($reportData['bookings']); ?></div>
                <div class="admin-stat-label">Bookings</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-video"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($reportData['sessions']); ?></div>
                <div class="admin-stat-label">Sessions</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-credit-card"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value">$<?php echo number_format($reportData['revenue'], 2); ?></div>
                <div class="admin-stat-label">Revenue</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:16px;margin-bottom:24px;">
        <div class="admin-card">
            <div class="admin-card-header"><h5><i class="fas fa-chart-line" style="color:#3b82f6;"></i> Daily Bookings</h5></div>
            <div class="admin-card-body" style="padding:20px;">
                <canvas id="dailyBookingsChart" height="200"></canvas>
            </div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header"><h5><i class="fas fa-trophy" style="color:#f59e0b;"></i> Top Mentors</h5></div>
            <div class="admin-card-body">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead><tr><th>Mentor</th><th>Bookings</th><th>Rating</th></tr></thead>
                        <tbody>
                            <?php foreach ($topMentors as $mentor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mentor['full_name']); ?></td>
                                <td><?php echo number_format($mentor['bookings']); ?></td>
                                <td><?php echo $mentor['avg_rating'] ? number_format($mentor['avg_rating'], 1) : 'N/A'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('dailyBookingsChart');
    if (ctx && <?php echo count($dailyBookings); ?> > 0) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($dailyBookings, 'date')); ?>,
                datasets: [{
                    label: 'Bookings',
                    data: <?php echo json_encode(array_column($dailyBookings, 'count')); ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
});

function setDateRange(period) {
    var from, to;
    var today = new Date();
    to = today.toISOString().split('T')[0];
    if (period === 'today') {
        from = to;
    } else if (period === 'week') {
        var d = new Date(today);
        d.setDate(today.getDate() - 7);
        from = d.toISOString().split('T')[0];
    } else if (period === 'month') {
        from = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-01';
    } else if (period === 'year') {
        from = today.getFullYear() + '-01-01';
    } else {
        return;
    }
    document.querySelector('input[name="from"]').value = from;
    document.querySelector('input[name="to"]').value = to;
    document.querySelector('form').submit();
}

function exportCSV() {
    var csv = 'Metric,Value\n';
    csv += 'New Users,<?php echo $reportData['users']; ?>\n';
    csv += 'New Mentors,<?php echo $reportData['mentors']; ?>\n';
    csv += 'New Freshers,<?php echo $reportData['freshers']; ?>\n';
    csv += 'Bookings,<?php echo $reportData['bookings']; ?>\n';
    csv += 'Sessions,<?php echo $reportData['sessions']; ?>\n';
    csv += 'Payments,<?php echo $reportData['payments']; ?>\n';
    csv += 'Revenue,<?php echo $reportData['revenue']; ?>\n';
    csv += 'Certificates,<?php echo $reportData['certificates']; ?>\n';
    csv += 'Research Posts,<?php echo $reportData['research']; ?>\n';
    csv += 'Reviews,<?php echo $reportData['feedback']; ?>\n';
    
    var blob = new Blob([csv], { type: 'text/csv' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'report-' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

