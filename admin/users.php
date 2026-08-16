<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Users';
$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$pdo = getDB();
$sql = "SELECT u.id, u.full_name, u.email, u.phone, u.profile_picture, u.role, u.status, u.is_verified, u.created_at, af.name as field_name FROM users u LEFT JOIN academic_fields af ON u.academic_field_id = af.id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($roleFilter) {
    $sql .= " AND u.role = ?";
    $params[] = $roleFilter;
}
if ($statusFilter) {
    $sql .= " AND u.status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY u.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalMentors = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'mentor'")->fetchColumn();
$totalFreshers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'fresher'")->fetchColumn();
$totalAdmins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>User Management</h3>
        <button class="admin-btn admin-btn-primary admin-btn-sm" onclick="adminOpenModal('userModal')"><i class="fas fa-plus"></i> Add User</button>
    </div>

    <!-- User Stats -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa;"><i class="fas fa-users"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalUsers); ?></div>
                <div class="admin-stat-label">Total Users</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(139,92,246,0.15);color:#a78bfa;"><i class="fas fa-user-tie"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalMentors); ?></div>
                <div class="admin-stat-label">Mentors</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(34,197,94,0.15);color:#4ade80;"><i class="fas fa-user-graduate"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalFreshers); ?></div>
                <div class="admin-stat-label">Freshers</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon" style="background:rgba(239,68,68,0.15);color:#f87171;"><i class="fas fa-shield-halved"></i></div>
            <div class="admin-stat-info">
                <div class="admin-stat-value"><?php echo number_format($totalAdmins); ?></div>
                <div class="admin-stat-label">Admins</div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-toolbar">
            <form method="GET" class="admin-search-box" style="max-width:none;flex:1;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="role" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="mentor" <?php echo $roleFilter === 'mentor' ? 'selected' : ''; ?>>Mentor</option>
                    <option value="fresher" <?php echo $roleFilter === 'fresher' ? 'selected' : ''; ?>>Fresher</option>
                </select>
                <select name="status" class="admin-filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                </select>
                <?php if ($search || $roleFilter || $statusFilter): ?>
                <a href="<?php echo ADMIN_URL; ?>users.php" class="admin-btn admin-btn-secondary admin-btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Field</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="7" class="admin-empty-state"><i class="fas fa-users"></i><p>No users found</p></td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="admin-user-cell">
                                    <img src="<?php echo BASE_URL; ?>frontend/assets/images/profile/<?php echo htmlspecialchars($user['profile_picture'] ?: 'default.png'); ?>" alt="">
                                    <div>
                                        <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                        <?php if ($user['is_verified']): ?>
                                        <i class="fas fa-check-circle" style="color:#22c55e;font-size:0.75rem;" title="Verified"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="admin-badge <?php echo admin_role_badge($user['role']); ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td><span class="admin-badge <?php echo admin_status_badge($user['status']); ?>"><?php echo htmlspecialchars($user['status']); ?></span></td>
                            <td><?php echo htmlspecialchars($user['field_name'] ?: 'N/A'); ?></td>
                            <td><?php echo admin_format_date($user['created_at']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <button class="admin-action-btn view" title="View" onclick="viewUser(<?php echo $user['id']; ?>)"><i class="fas fa-eye"></i></button>
                                    <button class="admin-action-btn edit" title="Edit" onclick="editUser(<?php echo $user['id']; ?>)"><i class="fas fa-pen"></i></button>
                                    <button class="admin-action-btn delete" title="Delete" onclick="adminConfirmDelete(<?php echo $user['id']; ?>, '<?php echo ADMIN_URL; ?>actions/user-actions.php', 'Delete this user?')"><i class="fas fa-trash"></i></button>
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

<!-- Add/Edit User Modal -->
<div class="admin-modal-overlay" id="userModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h5 id="userModalTitle">Add User</h5>
            <button class="admin-modal-close" onclick="adminCloseModal('userModal')">&times;</button>
        </div>
        <div class="admin-modal-body">
            <form id="userForm">
                <?php echo admin_csrf_field(); ?>
                <input type="hidden" name="user_id" id="user_id">
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Full Name</label>
                        <input type="text" name="full_name" id="full_name" class="admin-form-control" required>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Email</label>
                        <input type="email" name="email" id="email" class="admin-form-control" required>
                    </div>
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Role</label>
                        <select name="role" id="role" class="admin-form-control">
                            <option value="fresher">Fresher</option>
                            <option value="mentor">Mentor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Status</label>
                        <select name="status" id="status" class="admin-form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Phone</label>
                    <input type="text" name="phone" id="phone" class="admin-form-control">
                </div>
                <div class="admin-form-group" id="passwordGroup">
                    <label class="admin-form-label">Password</label>
                    <input type="password" name="password" id="password" class="admin-form-control" placeholder="Leave blank to keep current">
                </div>
            </form>
        </div>
        <div class="admin-modal-footer">
            <button class="admin-btn admin-btn-secondary" onclick="adminCloseModal('userModal')">Cancel</button>
            <button class="admin-btn admin-btn-primary" onclick="saveUser()">Save User</button>
        </div>
    </div>
</div>

<script>
function viewUser(id) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', AdminPanel.apiUrl + 'admin.php?action=show&id=' + id, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                var u = res.data;
                alert('Name: ' + u.full_name + '\nEmail: ' + u.email + '\nRole: ' + u.role + '\nStatus: ' + u.status + '\nPhone: ' + (u.phone || 'N/A') + '\nField: ' + (u.field_name || 'N/A') + '\nJoined: ' + u.created_at);
            } else {
                AdminPanel.showToast('Error', res.message, 'error');
            }
        }
    };
    xhr.send();
}

function editUser(id) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', AdminPanel.apiUrl + 'admin.php?action=show&id=' + id, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                var u = res.data;
                document.getElementById('userModalTitle').textContent = 'Edit User';
                document.getElementById('user_id').value = u.id;
                document.getElementById('full_name').value = u.full_name || '';
                document.getElementById('email').value = u.email || '';
                document.getElementById('role').value = u.role || 'fresher';
                document.getElementById('status').value = u.status || 'active';
                document.getElementById('phone').value = u.phone || '';
                document.getElementById('passwordGroup').style.display = 'none';
                adminOpenModal('userModal');
            }
        }
    };
    xhr.send();
}

function saveUser() {
    var id = document.getElementById('user_id').value;
    var form = document.getElementById('userForm');
    var formData = new FormData(form);
    var data = {};
    formData.forEach(function(v, k) { data[k] = v; });
    
    var url = AdminPanel.baseUrl + 'actions/user-actions.php?action=save';
    if (id) url += '&id=' + id;
    
    AdminUtils.apiFetch(url, {method: 'POST', body: data}).then(function(res) {
        if (res.success) {
            AdminPanel.showToast('Success', res.message || 'User saved', 'success');
            adminCloseModal('userModal');
            location.reload();
        } else {
            AdminPanel.showToast('Error', res.message || 'Failed', 'error');
        }
    });
}

document.getElementById('userModal')?.addEventListener('click', function(e) {
    if (e.target === this) adminCloseModal('userModal');
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

