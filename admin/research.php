<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Research Management';
$pdo = getDB();
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT r.*, u.full_name as author_name FROM research r JOIN users u ON r.user_id = u.id WHERE 1=1";
$params = [];

if ($statusFilter) {
    $sql .= " AND r.status = ?";
    $params[] = $statusFilter;
}
if ($search) {
    $sql .= " AND (r.title LIKE ? OR u.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY r.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$research = $stmt->fetchAll();

$totalResearch = (int)$pdo->query("SELECT COUNT(*) FROM research")->fetchColumn();
$pendingResearch = (int)$pdo->query("SELECT COUNT(*) FROM research WHERE status = 'pending'")->fetchColumn();
$approvedResearch = (int)$pdo->query("SELECT COUNT(*) FROM research WHERE status = 'approved'")->fetchColumn();
$rejectedResearch = (int)$pdo->query("SELECT COUNT(*) FROM research WHERE status = 'rejected'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Research Moderation</h3>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(139,92,246,0.15);color:#a78bfa;"><i class="fas fa-flask"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalResearch); ?></div>
                <div class="admin-stat-label">Total Research</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-clock"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($pendingResearch); ?></div>
                <div class="admin-stat-label">Pending Review</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($approvedResearch); ?></div>
                <div class="admin-stat-label">Approved</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i class="fas fa-times-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($rejectedResearch); ?></div>
                <div class="admin-stat-label">Rejected</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search research..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="status" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <?php if ($search || $statusFilter): ?>
                <a href="<?php echo ADMIN_URL; ?>research.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($research)): ?>
                    <tr><td colspan="8" class="admin-empty-state"><i class="fas fa-flask"></i><p>No research found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($research as $item): ?>
                        <tr>
                            <td>#<?php echo $item['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($item['author_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['category'] ?: 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($item['type'] ?: 'N/A'); ?></td>
                            <td><span class="admin-badge <?php echo admin_status_badge($item['status']); ?>"><?php echo htmlspecialchars($item['status']); ?></span></td>
                            <td><?php echo admin_format_date($item['created_at']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <?php if ($item['status'] === 'pending'): ?>
                                    <button class="admin-action-btn approve" title="Approve" onclick="updateResearchStatus(<?php echo $item['id']; ?>, 'approved')"><i class="fas fa-check"></i></button>
                                    <button class="admin-action-btn reject" title="Reject" onclick="updateResearchStatus(<?php echo $item['id']; ?>, 'rejected')"><i class="fas fa-times"></i></button>
                                    <?php endif; ?>
                                    <button class="admin-action-btn delete" title="Delete" onclick="adminConfirmDelete(<?php echo $item['id']; ?>, '<?php echo ADMIN_URL; ?>actions/user-actions.php?action=delete_research', 'Delete this research?')"><i class="fas fa-trash"></i></button>
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
function updateResearchStatus(id, status) {
    if (!AdminPanel.confirm('Update research status to ' + status + '?')) return;
    AdminUtils.apiFetch(AdminPanel.baseUrl + 'actions/research-actions.php?action=update_status&id=' + id, {
        method: 'POST',
        body: {status: status}
    }).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', 'Research status updated', 'success');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

