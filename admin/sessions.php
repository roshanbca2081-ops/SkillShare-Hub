<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Sessions';
$pdo = getDB();
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT s.*, u.full_name as user_name, m.full_name as mentor_name, c.name as course_name FROM sessions s JOIN users u ON s.user_id = u.id JOIN users m ON s.mentor_id = m.id LEFT JOIN courses c ON s.course_id = c.id WHERE 1=1";
$params = [];

if ($statusFilter) {
    $sql .= " AND s.status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY s.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sessions = $stmt->fetchAll();

$totalSessions = (int)$pdo->query("SELECT COUNT(*) FROM sessions")->fetchColumn();
$scheduledSessions = (int)$pdo->query("SELECT COUNT(*) FROM sessions WHERE status = 'scheduled'")->fetchColumn();
$completedSessions = (int)$pdo->query("SELECT COUNT(*) FROM sessions WHERE status = 'completed'")->fetchColumn();
$cancelledSessions = (int)$pdo->query("SELECT COUNT(*) FROM sessions WHERE status = 'cancelled'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Session Management</h3>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(139,92,246,0.15);color:#a78bfa;"><i class="fas fa-video"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalSessions); ?></div>
                <div class="admin-stat-label">Total Sessions</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-clock"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($scheduledSessions); ?></div>
                <div class="admin-stat-label">Scheduled</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($completedSessions); ?></div>
                <div class="admin-stat-label">Completed</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i class="fas fa-ban"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($cancelledSessions); ?></div>
                <div class="admin-stat-label">Cancelled</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <select name="status" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="scheduled" <?php echo $statusFilter === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <?php if ($statusFilter): ?>
                <a href="<?php echo ADMIN_URL; ?>sessions.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mentor</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Meeting Link</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sessions)): ?>
                    <tr><td colspan="9" class="admin-empty-state"><i class="fas fa-video"></i><p>No sessions found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($sessions as $session): ?>
                        <tr>
                            <td>#<?php echo $session['id']; ?></td>
                            <td><?php echo htmlspecialchars($session['mentor_name']); ?></td>
                            <td><?php echo htmlspecialchars($session['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($session['course_name'] ?: 'N/A'); ?></td>
                            <td><?php echo admin_format_datetime($session['start_at']); ?></td>
                            <td><?php echo admin_format_datetime($session['end_at']); ?></td>
                            <td>
                                <?php if ($session['meeting_link']): ?>
                                <a href="<?php echo htmlspecialchars($session['meeting_link']); ?>" target="_blank" class="admin-btn admin-btn-sm admin-btn-secondary">Join</a>
                                <?php else: ?>
                                N/A
                                <?php endif; ?>
                            </td>
                            <td><span class="admin-badge <?php echo admin_status_badge($session['status']); ?>"><?php echo htmlspecialchars($session['status']); ?></span></td>
                            <td>
                                <div class="admin-actions">
                                    <?php if ($session['status'] === 'scheduled'): ?>
                                    <button class="admin-action-btn approve" title="Mark Complete" onclick="updateSessionStatus(<?php echo $session['id']; ?>, 'completed')"><i class="fas fa-check"></i></button>
                                    <button class="admin-action-btn reject" title="Cancel" onclick="updateSessionStatus(<?php echo $session['id']; ?>, 'cancelled')"><i class="fas fa-ban"></i></button>
                                    <?php endif; ?>
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
function updateSessionStatus(id, status) {
    if (!AdminPanel.confirm('Update session to ' + status + '?')) return;
    AdminUtils.apiFetch(AdminPanel.baseUrl + 'actions/session-actions.php?action=update_status&id=' + id, {
        method: 'POST',
        body: {status: status}
    }).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', 'Session updated', 'success');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

