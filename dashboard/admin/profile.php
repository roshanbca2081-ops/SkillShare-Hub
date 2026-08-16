<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Profile';
$sidebar_role = 'admin';
$sidebar_active = 'Profile';

startDashboardPage();
?>
<style>
.profile-grid { display: grid; grid-template-columns: 300px 1fr; gap: 20px; align-items: start; }
@media (max-width: 768px) { .profile-grid { grid-template-columns: 1fr; } }
.profile-avatar-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 24px; text-align: center; }
.profile-avatar-card img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-soft); margin-bottom: 12px; }
.profile-form-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 24px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 480px) { .form-grid { grid-template-columns: 1fr; } }
</style>

<div class="profile-grid">
    <div class="profile-avatar-card">
        <img id="profileAvatar" src="https://ui-avatars.com/40/AA/3b82f6/fff?text=Admin" alt="Profile">
        <h5 id="profileName">Admin</h5>
        <p style="color:var(--text-muted);font-size:0.85rem;">Administrator</p>
        <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;margin-top:12px;">
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('avatarInput').click()"><i class="fa-solid fa-camera"></i> Change Photo</button>
            <button class="btn btn-outline btn-sm" id="removeAvatarBtn"><i class="fa-solid fa-trash"></i> Remove</button>
        </div>
        <input type="file" id="avatarInput" accept="image/*" style="display:none;">
    </div>
    <div class="profile-form-card">
        <h5 style="margin-bottom:16px;"><i class="fa-solid fa-id-card" style="color:var(--primary);"></i> Personal Information</h5>
        <form id="profileForm">
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Full Name</label><input type="text" class="form-control" id="profName" value="Admin"></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" id="profEmail" value=""></div>
                <div class="form-group"><label class="form-label">Phone</label><input type="tel" class="form-control" id="profPhone" value=""></div>
                <div class="form-group"><label class="form-label">Role</label><input type="text" class="form-control" value="Administrator" disabled></div>
                <div class="form-group" style="grid-column:1/-1;"><label class="form-label">Bio</label><textarea class="form-control" id="profBio" rows="4"></textarea></div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:16px;"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
        </form>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';
var userId = <?php echo getUserId(); ?>;

SkillShare.apiFetch(BASE + 'api/profile.php').then(function(res) {
    if (res.success && res.data) {
        var u = res.data;
        document.getElementById('profName').value = u.full_name || '';
        document.getElementById('profEmail').value = u.email || '';
        document.getElementById('profPhone').value = u.phone || '';
        document.getElementById('profBio').value = u.bio || '';
        document.getElementById('profileName').textContent = u.full_name || 'Admin';
        if (u.profile_picture) {
            document.getElementById('profileAvatar').src = BASE + 'frontend/assets/images/profile/' + u.profile_picture;
        }
    }
});

document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    SkillShare.showToast('Profile Updated', 'Your profile has been saved successfully.', 'success');
});
</script>
<?php
endDashboardPage();
?>
