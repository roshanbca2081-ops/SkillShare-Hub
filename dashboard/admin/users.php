<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Users';
$sidebar_role = 'admin';
$sidebar_active = 'Users';

startDashboardPage();
?>
<style>
.user-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
</style>

<div class="user-stats">
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="uTotal">0</div><div class="stat-label">Total Users</div></div><div class="stat-icon"><i class="fas fa-users"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="uFreshers">0</div><div class="stat-label">Freshers</div></div><div class="stat-icon"><i class="fas fa-user-graduate"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="uMentors">0</div><div class="stat-label">Mentors</div></div><div class="stat-icon"><i class="fas fa-user-tie"></i></div></div></div>
    <div class="stat-card"><div class="stat-top"><div><div class="stat-number" id="uAdmins">0</div><div class="stat-label">Admins</div></div><div class="stat-icon"><i class="fas fa-shield-halved"></i></div></div></div>
</div>

<div class="panel reveal">
    <div class="panel-header">
        <h5><i class="fa-solid fa-users" style="color:var(--primary);"></i> All Users</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fa-solid fa-plus"></i> Add User</button>
    </div>
    <div class="table-responsive-wrap">
        <table class="data-table dash-table">
            <thead>
                <tr><th><input type="checkbox" id="checkAll"></th><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
            </thead>
            <tbody id="usersTable"><tr><td colspan="7" style="text-align:center;color:var(--text-muted);">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content" style="background:rgba(20,20,40,0.95);color:#fff;border:1px solid var(--glass-border);">
        <div class="modal-header" style="border-bottom:1px solid var(--glass-border);"><h5 class="modal-title">Add User</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <form id="addUserForm">
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Full Name</label><input type="text" class="form-control" id="auName" required></div>
                    <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" id="auEmail" required></div>
                    <div class="form-group"><label class="form-label">Role</label><select class="form-control" id="auRole"><option>fresher</option><option>mentor</option><option>admin</option></select></div>
                    <div class="form-group"><label class="form-label">Password</label><input type="password" class="form-control" id="auPass" required></div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:12px;"><i class="fa-solid fa-plus"></i> Create User</button>
            </form>
        </div>
    </div></div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function getStatusClass(s) { return s==='active'?'approved':(s==='pending'?'pending':'rejected'); }
function getRoleClass(r) { return r==='mentor'?'secondary':(r==='admin'?'danger':'primary'); }

SkillShare.apiFetch(BASE + 'api/dashboard/index.php').then(function(res) {
    if (res.success && res.data) {
        var d = res.data;
        document.getElementById('uTotal').textContent = d.users || 0;
        document.getElementById('uFreshers').textContent = d.freshers || 0;
        document.getElementById('uMentors').textContent = d.mentors || 0;
        document.getElementById('uAdmins').textContent = d.admins || 0;
    }
});

function loadUsers() {
    SkillShare.apiFetch(BASE + 'api/admin.php?action=list').then(function(res) {
        if (res.success && res.data) {
            var html = '';
            res.data.forEach(function(u) {
                html += '<tr>' +
                    '<td><input type="checkbox" class="row-check" value="' + u.id + '"></td>' +
                    '<td><div class="user-cell"><img src="../../assets/images/profile/' + (u.profile_picture||'default.png') + '" alt=""><strong>' + SkillShare.escapeHtml(u.full_name) + '</strong></div></td>' +
                    '<td>' + SkillShare.escapeHtml(u.email) + '</td>' +
                    '<td><span class="badge badge-' + getRoleClass(u.role) + '">' + u.role + '</span></td>' +
                    '<td><span class="status-pill ' + getStatusClass(u.status) + '">' + u.status + '</span></td>' +
                    '<td>' + SkillShare.escapeHtml(u.created_at) + '</td>' +
                    '<td><div class="table-actions">' +
                        '<button class="view-btn" onclick="viewUser(' + u.id + ')" aria-label="View"><i class="fa-solid fa-eye"></i></button>' +
                        '<button class="edit-btn" onclick="editUserRole(' + u.id + ')" aria-label="Edit role"><i class="fa-solid fa-pen"></i></button>' +
                        '<button class="delete-btn" onclick="toggleStatus(' + u.id + ')" aria-label="Toggle status"><i class="fa-solid fa-ban"></i></button>' +
                    '</div></td></tr>';
            });
            document.getElementById('usersTable').innerHTML = html || '<tr><td colspan="7" style="text-align:center;">No users found</td></tr>';
        }
    });
}
loadUsers();

function viewUser(id) {
    SkillShare.apiFetch(BASE + 'api/admin.php?action=show&id=' + id).then(function(res) {
        if (res.success) {
            var u = res.data;
            var msg = 'Name: ' + u.full_name + '\nEmail: ' + u.email + '\nRole: ' + u.role + '\nStatus: ' + u.status + '\nPhone: ' + (u.phone||'N/A');
            alert(msg);
        }
    });
}

function editUserRole(id) {
    var role = prompt('Enter new role (fresher, mentor, admin):');
    if (role && ['fresher','mentor','admin'].includes(role)) {
        fetch(BASE + 'api/admin.php?action=update_role&id=' + id, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'role=' + encodeURIComponent(role)})
            .then(function(r){ return r.json(); }).then(function(res) {
                if (res.success) { SkillShare.showToast('Success', 'Role updated', 'success'); loadUsers(); }
                else SkillShare.showToast('Error', res.message, 'error');
            });
    }
}

function toggleStatus(id) {
    var status = prompt('Enter status (active, pending, suspended):');
    if (status && ['active','pending','suspended'].includes(status)) {
        fetch(BASE + 'api/admin.php?action=update_status&id=' + id, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'status=' + encodeURIComponent(status)})
            .then(function(r){ return r.json(); }).then(function(res) {
                if (res.success) { SkillShare.showToast('Success', 'Status updated', 'success'); loadUsers(); }
                else SkillShare.showToast('Error', res.message, 'error');
            });
    }
}

document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    SkillShare.showToast('Info', 'User creation handled server-side. Connect /api/users.php create endpoint.', 'info');
    var modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
    if (modal) modal.hide();
});
</script>
<?php
endDashboardPage();
?>
