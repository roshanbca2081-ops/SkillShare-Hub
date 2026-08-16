<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Certificates';
$pdo = getDB();
$search = $_GET['search'] ?? '';

$sql = "SELECT c.*, u.full_name as user_name, co.name as course_name FROM certificates c JOIN users u ON c.user_id = u.id LEFT JOIN courses co ON c.course_id = co.id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (u.full_name LIKE ? OR c.certificate_number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY c.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$certificates = $stmt->fetchAll();

$totalCertificates = (int)$pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();
$issuedCertificates = (int)$pdo->query("SELECT COUNT(*) FROM certificates WHERE status = 'issued'")->fetchColumn();
$revokedCertificates = (int)$pdo->query("SELECT COUNT(*) FROM certificates WHERE status = 'revoked'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Certificate Management</h3>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fas fa-award"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalCertificates); ?></div>
                <div class="admin-stat-label">Total Certificates</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($issuedCertificates); ?></div>
                <div class="admin-stat-label">Issued</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i class="fas fa-ban"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($revokedCertificates); ?></div>
                <div class="admin-stat-label">Revoked</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search certificates..." value="<?php echo htmlspecialchars($search); ?>">
                <?php if ($search): ?>
                <a href="<?php echo ADMIN_URL; ?>certificates.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Certificate #</th>
                        <th>Recipient</th>
                        <th>Course</th>
                        <th>Issue Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($certificates)): ?>
                    <tr><td colspan="7" class="admin-empty-state"><i class="fas fa-award"></i><p>No certificates found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($certificates as $cert): ?>
                        <tr>
                            <td>#<?php echo $cert['id']; ?></td>
                            <td><code><?php echo htmlspecialchars($cert['certificate_number'] ?: 'N/A'); ?></code></td>
                            <td><?php echo htmlspecialchars($cert['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($cert['course_name'] ?: 'N/A'); ?></td>
                            <td><?php echo admin_format_date($cert['issue_date']); ?></td>
                            <td><span class="admin-badge <?php echo admin_status_badge($cert['status']); ?>"><?php echo htmlspecialchars($cert['status']); ?></span></td>
                            <td>
                                <div class="admin-actions">
                                    <?php if ($cert['status'] === 'issued'): ?>
                                    <button class="admin-action-btn reject" title="Revoke" onclick="updateCertStatus(<?php echo $cert['id']; ?>, 'revoked')"><i class="fas fa-ban"></i></button>
                                    <?php endif; ?>
                                    <?php if ($cert['status'] === 'revoked'): ?>
                                    <button class="admin-action-btn approve" title="Re-issue" onclick="updateCertStatus(<?php echo $cert['id']; ?>, 'issued')"><i class="fas fa-check"></i></button>
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
function updateCertStatus(id, status) {
    if (!AdminPanel.confirm('Update certificate to ' + status + '?')) return;
    AdminUtils.apiFetch(AdminPanel.baseUrl + 'actions/certificate-actions.php?action=update_status&id=' + id, {
        method: 'POST',
        body: {status: status}
    }).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', 'Certificate updated', 'success');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

