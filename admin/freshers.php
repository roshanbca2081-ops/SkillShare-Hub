<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Freshers';
$pdo = getDB();
$search = $_GET['search'] ?? '';

$sql = "SELECT u.id, u.full_name, u.email, u.phone, u.profile_picture, u.status, u.created_at, f.education_level, f.institution FROM users u LEFT JOIN freshers f ON u.id = f.user_id WHERE u.role = 'fresher'";
$params = [];

if ($search) {
    $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY u.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$freshers = $stmt->fetchAll();

$totalFreshers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'fresher'")->fetchColumn();
$activeFreshers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'fresher' AND status = 'active'")->fetchColumn();
$pendingFreshers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'fresher' AND status = 'pending'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Fresher / Student Management</h3>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-user-graduate"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalFreshers); ?></div>
                <div class="admin-stat-label">Total Freshers</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($activeFreshers); ?></div>
                <div class="admin-stat-label">Active</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-clock"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($pendingFreshers); ?></div>
                <div class="admin-stat-label">Pending</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search freshers..." value="<?php echo htmlspecialchars($search); ?>">
                <?php if ($search): ?>
                <a href="<?php echo ADMIN_URL; ?>freshers.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Education Level</th>
                        <th>Institution</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($freshers)): ?>
                    <tr><td colspan="7" class="admin-empty-state"><i class="fas fa-user-graduate"></i><p>No freshers found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($freshers as $fresher): ?>
                        <tr>
                            <td>
                                <div class="admin-user-cell">
                                    <img src="<?php echo BASE_URL; ?>frontend/assets/images/profile/<?php echo htmlspecialchars($fresher['profile_picture'] ?: 'default.png'); ?>" alt="">
                                    <strong><?php echo htmlspecialchars($fresher['full_name']); ?></strong>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($fresher['email']); ?></td>
                            <td><?php echo htmlspecialchars($fresher['education_level'] ?: 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($fresher['institution'] ?: 'N/A'); ?></td>
                            <td><span class="admin-badge <?php echo admin_status_badge($fresher['status']); ?>"><?php echo htmlspecialchars($fresher['status']); ?></span></td>
                            <td><?php echo admin_format_date($fresher['created_at']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="admin-action-btn view" title="View" onclick="viewFresher(<?php echo $fresher['id']; ?>)"><i class="fas fa-eye"></i></button>
                                    <button class="admin-action-btn edit" title="Toggle Status" onclick="adminToggleStatus(<?php echo $fresher['id']; ?>, '<?php echo ADMIN_URL; ?>actions/user-actions.php?action=status')"><i class="fas fa-pen"></i></button>
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
function viewFresher(id) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', AdminPanel.apiUrl + 'admin.php?action=show&id=' + id, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                var u = res.data;
                alert('Name: ' + u.full_name + '\nEmail: ' + u.email + '\nRole: Fresher\nStatus: ' + u.status + '\nEducation: ' + (u.education_level || 'N/A') + '\nInstitution: ' + (u.institution || 'N/A') + '\nJoined: ' + u.created_at);
            }
        }
    };
    xhr.send();
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

