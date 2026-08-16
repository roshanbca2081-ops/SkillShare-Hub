<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Bookings';
$pdo = getDB();
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT b.*, u.full_name as user_name, m.full_name as mentor_name, c.name as course_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN users m ON b.mentor_id = m.id LEFT JOIN courses c ON b.course_id = c.id WHERE 1=1";
$params = [];

if ($statusFilter) {
    $sql .= " AND b.status = ?";
    $params[] = $statusFilter;
}
if ($search) {
    $sql .= " AND (u.full_name LIKE ? OR m.full_name LIKE ? OR c.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY b.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$totalBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$pendingBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$acceptedBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'accepted'")->fetchColumn();
$completedBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'completed'")->fetchColumn();
$cancelledBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'cancelled'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Booking Management</h3>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-calendar-check"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalBookings); ?></div>
                <div class="admin-stat-label">Total Bookings</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-clock"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($pendingBookings); ?></div>
                <div class="admin-stat-label">Pending</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($acceptedBookings); ?></div>
                <div class="admin-stat-label">Accepted</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;"><i class="fas fa-circle-check"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($completedBookings); ?></div>
                <div class="admin-stat-label">Completed</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i class="fas fa-ban"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($cancelledBookings); ?></div>
                <div class="admin-stat-label">Cancelled/Rejected</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search bookings..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="status" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="accepted" <?php echo $statusFilter === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <?php if ($search || $statusFilter): ?>
                <a href="<?php echo ADMIN_URL; ?>bookings.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Mentor</th>
                        <th>Course/Topic</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                    <tr><td colspan="8" class="admin-empty-state"><i class="fas fa-calendar-check"></i><p>No bookings found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#<?php echo $booking['id']; ?></td>
                            <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['mentor_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['course_name'] ?: $booking['topic'] ?: 'Session'); ?></td>
                            <td><?php echo admin_format_date($booking['booking_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_time'] ?: 'N/A'); ?></td>
                            <td><span class="admin-badge <?php echo admin_status_badge($booking['status']); ?>"><?php echo htmlspecialchars($booking['status']); ?></span></td>
                            <td>
                                <div class="admin-actions">
                                    <?php if ($booking['status'] === 'pending'): ?>
                                    <button class="admin-action-btn approve" title="Accept" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'accepted')"><i class="fas fa-check"></i></button>
                                    <button class="admin-action-btn reject" title="Reject" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'rejected')"><i class="fas fa-times"></i></button>
                                    <?php endif; ?>
                                    <?php if ($booking['status'] === 'accepted'): ?>
                                    <button class="admin-action-btn approve" title="Complete" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'completed')"><i class="fas fa-check-double"></i></button>
                                    <?php endif; ?>
                                    <button class="admin-action-btn edit" title="Cancel" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'cancelled')"><i class="fas fa-ban"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function updateBookingStatus(id, status) {
    if (!AdminPanel.confirm('Update booking status to ' + status + '?')) return;
    AdminUtils.apiFetch(AdminPanel.baseUrl + 'actions/booking-actions.php?action=update_status&id=' + id, {
        method: 'POST',
        body: {status: status}
    }).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', 'Booking updated', 'success');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

