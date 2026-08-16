<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Mentors';
$pdo = getDB();
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT u.id, u.full_name, u.email, u.phone, u.profile_picture, u.status, u.is_verified, u.created_at, m.specialization, m.experience_years, m.rating, m.is_verified as mentor_verified FROM users u JOIN mentors m ON u.id = m.user_id WHERE u.role = 'mentor'";
$params = [];

if ($search) {
    $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR m.specialization LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($statusFilter) {
    $sql .= " AND u.status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY u.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mentors = $stmt->fetchAll();

$totalMentors = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mentor'")->fetchColumn();
$approvedMentors = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mentor' AND status = 'active'")->fetchColumn();
$pendingMentors = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mentor' AND status = 'pending'")->fetchColumn();
$suspendedMentors = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mentor' AND status = 'inactive'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>Mentor Management</h3>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(139,92,246,0.15);color:#a78bfa;"><i class="fas fa-user-tie"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalMentors); ?></div>
                <div class="admin-stat-label">Total Mentors</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-check-circle"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($approvedMentors); ?></div>
                <div class="admin-stat-label">Approved</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24;"><i class="fas fa-clock"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($pendingMentors); ?></div>
                <div class="admin-stat-label">Pending</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i class="fas fa-ban"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($suspendedMentors); ?></div>
                <div class="admin-stat-label">Inactive/Suspended</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search mentors..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="status" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                </select>
                <?php if ($search || $statusFilter): ?>
                <a href="<?php echo ADMIN_URL; ?>mentors.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mentor</th>
                        <th>Email</th>
                        <th>Specialization</th>
                        <th>Experience</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mentors)): ?>
                    <tr><td colspan="8" class="admin-empty-state"><i class="fas fa-user-tie"></i><p>No mentors found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($mentors as $mentor): ?>
                        <tr>
                            <td>
                                <div class="admin-user-cell">
                                    <img src="<?php echo BASE_URL; ?>frontend/assets/images/profile/<?php echo htmlspecialchars($mentor['profile_picture'] ?: 'default.png'); ?>" alt="">
                                    <div>
                                        <strong><?php echo htmlspecialchars($mentor['full_name']); ?></strong>
                                        <?php if ($mentor['is_verified']): ?>
                                        <i class="fas fa-check-circle" style="color:#22c55e;font-size:0.75rem;" title="Verified"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($mentor['email']); ?></td>
                            <td><?php echo htmlspecialchars($mentor['specialization'] ?: 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($mentor['experience_years'] ? $mentor['experience_years'] . ' yrs' : 'N/A'); ?></td>
                            <td>
                                <?php if ($mentor['rating']): ?>
                                <i class="fas fa-star" style="color:#f59e0b;"></i> <?php echo number_format($mentor['rating'], 1); ?>
                                <?php else: ?>
                                N/A
                                <?php endif; ?>
                            </td>
                            <td><span class="admin-badge <?php echo admin_status_badge($mentor['status']); ?>"><?php echo htmlspecialchars($mentor['status']); ?></span></td>
                            <td><?php echo admin_format_date($mentor['created_at']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="admin-action-btn view" title="View" onclick="viewMentor(<?php echo $mentor['id']; ?>)"><i class="fas fa-eye"></i></button>
                                    <?php if ($mentor['status'] === 'pending'): ?>
                                    <button class="admin-action-btn approve" title="Approve" onclick="updateMentorStatus(<?php echo $mentor['id']; ?>, 'active')"><i class="fas fa-check"></i></button>
                                    <button class="admin-action-btn reject" title="Reject" onclick="updateMentorStatus(<?php echo $mentor['id']; ?>, 'inactive')"><i class="fas fa-times"></i></button>
                                    <?php endif; ?>
                                    <?php if ($mentor['status'] === 'active'): ?>
                                    <button class="admin-action-btn reject" title="Suspend" onclick="updateMentorStatus(<?php echo $mentor['id']; ?>, 'inactive')"><i class="fas fa-ban"></i></button>
                                    <?php endif; ?>
                                    <?php if ($mentor['status'] === 'inactive'): ?>
                                    <button class="admin-action-btn approve" title="Activate" onclick="updateMentorStatus(<?php echo $mentor['id']; ?>, 'active')"><i class="fas fa-check"></i></button>
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
function viewMentor(id) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', AdminPanel.apiUrl + 'admin.php?action=show&id=' + id, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                var u = res.data;
                var details = 'Name: ' + u.full_name + '\nEmail: ' + u.email + '\nRole: Mentor\nStatus: ' + u.status + '\nSpecialization: ' + (u.specialization || 'N/A') + '\nExperience: ' + (u.experience_years || 'N/A') + ' years\nRating: ' + (u.rating || 'N/A') + '\nJoined: ' + u.created_at;
                alert(details);
            }
        }
    };
    xhr.send();
}

function updateMentorStatus(id, status) {
    if (!AdminPanel.confirm('Update mentor status to ' + status + '?')) return;
    AdminUtils.apiFetch(AdminPanel.apiUrl + 'admin.php?action=update_status&id=' + id, {
        method: 'POST',
        body: {status: status}
    }).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', 'Mentor status updated', 'success');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

