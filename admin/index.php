<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard';
$stats = getAdminStats();
$activities = getRecentActivity(15);
$usersByRole = getUsersByRoleChart();
$bookingStats = getBookingStatsChart();
$monthlyReg = getMonthlyRegistrations();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <div>
            <h3>Dashboard Overview</h3>
            <p class="admin-breadcrumb">Welcome back, <?php echo htmlspecialchars($adminName); ?></p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;"><i class="fas fa-users"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['users']); ?></div>
                <div class="admin-stat-label">Total Users</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(139,92,246,0.15);color:#a78bfa;"><i class="fas fa-user-tie"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['mentors']); ?></div>
                <div class="admin-stat-label">Active Mentors</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-user-graduate"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['freshers']); ?></div>
                <div class="admin-stat-label">Active Freshers</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(6,182,212,0.15);color:#22d3ee;"><i class="fas fa-layer-group"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['fields']); ?></div>
                <div class="admin-stat-label">Academic Fields</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-book-open"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['courses']); ?></div>
                <div class="admin-stat-label">Active Courses</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i class="fas fa-calendar-check"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['bookings']); ?></div>
                <div class="admin-stat-label">Total Bookings</div>
                <?php if ($stats['pending_bookings'] > 0): ?>
                <div class="admin-stat-change negative"><?php echo $stats['pending_bookings']; ?> pending</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(139,92,246,0.15);color:#a78bfa;"><i class="fas fa-video"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['sessions']); ?></div>
                <div class="admin-stat-label">Sessions</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-credit-card"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value">$<?php echo number_format($stats['revenue'], 2); ?></div>
                <div class="admin-stat-label">Total Revenue</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-file-pen"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['assignments']); ?></div>
                <div class="admin-stat-label">Assignments</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-award"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['certificates']); ?></div>
                <div class="admin-stat-label">Certificates</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(139,92,246,0.15);color:#a78bfa;"><i class="fas fa-flask"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['research']); ?></div>
                <div class="admin-stat-label">Research Posts</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-star"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($stats['feedback']); ?></div>
                <div class="admin-stat-label">Reviews</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:16px;margin-bottom:24px;">
        <div class="admin-card">
            <div class="admin-card-header"><h5><i class="fas fa-chart-pie" style="color:#3b82f6;"></i> Users by Role</h5></div>
            <div class="admin-card-body" style="padding:20px;">
                <canvas id="usersRoleChart" height="220"></canvas>
            </div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header"><h5><i class="fas fa-chart-bar" style="color:#8b5cf6;"></i> Booking Statistics</h5></div>
            <div class="admin-card-body" style="padding:20px;">
                <canvas id="bookingChart" height="220"></canvas>
            </div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header"><h5><i class="fas fa-chart-line" style="color:#22c55e;"></i> Monthly Registrations</h5></div>
            <div class="admin-card-body" style="padding:20px;">
                <canvas id="monthlyRegChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h5><i class="fas fa-clock-rotate-left" style="color:#f59e0b;"></i> Recent Activity</h5>
        </div>
        <div class="admin-card-body">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Type</th><th>Activity</th><th>Details</th><th>Time</th></tr></thead>
                    <tbody>
                        <?php if (empty($activities)): ?>
                        <tr><td colspan="4" class="admin-empty-state">No recent activity</td></tr>
                        <?php else: ?>
                            <?php foreach (array_slice($activities, 0, 12) as $act): ?>
                            <tr>
                                <td><i class="fas <?php echo $act['icon']; ?>" style="color:<?php echo $act['color']; ?>;"></i> <?php echo ucfirst($act['type']); ?></td>
                                <td><strong><?php echo htmlspecialchars($act['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($act['subtitle']); ?></td>
                                <td><?php echo admin_time_ago($act['time']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Users by Role Chart
    var roleCtx = document.getElementById('usersRoleChart');
    if (roleCtx) {
        new Chart(roleCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($usersByRole['labels']); ?>,
                datasets: [{
                    data: <?php echo json_encode($usersByRole['data']); ?>,
                    backgroundColor: ['#ef4444', '#8b5cf6', '#3b82f6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 } } }
            }
        });
    }

    // Booking Stats Chart
    var bookingCtx = document.getElementById('bookingChart');
    if (bookingCtx) {
        new Chart(bookingCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($bookingStats['labels']); ?>,
                datasets: [{
                    label: 'Bookings',
                    data: <?php echo json_encode($bookingStats['data']); ?>,
                    backgroundColor: ['#f59e0b', '#22c55e', '#3b82f6', '#ef4444', '#94a3b8'],
                    borderRadius: 6,
                    barThickness: 32
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

    // Monthly Registrations Chart
    var monthlyCtx = document.getElementById('monthlyRegChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($monthlyReg['labels']); ?>,
                datasets: [{
                    label: 'Registrations',
                    data: <?php echo json_encode($monthlyReg['data']); ?>,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#22c55e'
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
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

