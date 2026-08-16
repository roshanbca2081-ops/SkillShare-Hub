<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'fresher') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Settings';
$sidebar_role = 'fresher';
$sidebar_active = 'Settings';

startDashboardPage();
?>

<style>
.settings-layout { display: grid; grid-template-columns: 220px 1fr; gap: 20px; }
.settings-nav { display: flex; flex-direction: column; gap: 4px; }
.set-tab {
    display: flex; align-items: center; gap: 10px; padding: 10px 14px;
    border-radius: var(--radius-md); color: var(--text-secondary); text-decoration: none;
    font-size: 0.85rem; transition: all 0.3s ease; cursor: pointer; border: none; background: none; text-align: left;
}
.set-tab:hover, .set-tab.active { background: rgba(59,130,246,0.1); color: var(--primary-400); }
.set-tab i { width: 18px; text-align: center; }
.settings-content { display: flex; flex-direction: column; gap: 20px; }
.set-pane { display: none; }
.set-pane.active { display: block; }
.set-pane h5 { margin: 0 0 16px; color: var(--text-primary); }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group-full { grid-column: 1 / -1; }
.switch-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
.switch { position: relative; display: inline-block; width: 44px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; inset: 0; background: rgba(255,255,255,0.1); border-radius: var(--radius-full); transition: 0.3s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: 0.3s; }
input:checked + .slider { background: var(--gradient-primary); }
input:checked + .slider:before { transform: translateX(20px); }
@media (max-width: 768px) { .settings-layout { grid-template-columns: 1fr; } .form-grid { grid-template-columns: 1fr; } }
</style>

<div class="settings-layout">
    <div class="settings-nav">
        <button class="set-tab active" onclick="switchTab('profile', this)"><i class="fas fa-user"></i> Profile</button>
        <button class="set-tab" onclick="switchTab('security', this)"><i class="fas fa-shield-halved"></i> Security</button>
        <button class="set-tab" onclick="switchTab('preferences', this)"><i class="fas fa-bell"></i> Preferences</button>
    </div>
    <div class="settings-content">
        <div class="set-pane active" id="profile">
            <div class="card" style="padding:24px;">
                <h5 style="margin:0 0 16px;"><i class="fas fa-user" style="color:var(--primary-400);margin-right:8px;"></i> Profile Settings</h5>
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Full Name</label><input type="text" class="form-control" name="full_name" id="setFullName"></div>
                    <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" id="setEmail" disabled></div>
                    <div class="form-group"><label class="form-label">Phone</label><input type="text" class="form-control" name="phone" id="setPhone"></div>
                    <div class="form-group"><label class="form-label">Institution</label><input type="text" class="form-control" name="institution" id="setInstitution"></div>
                    <div class="form-group"><label class="form-label">Education Level</label><input type="text" class="form-control" name="education_level" id="setEduLevel"></div>
                    <div class="form-group"><label class="form-label">Graduation Year</label><input type="number" class="form-control" name="graduation_year" id="setGradYear"></div>
                    <div class="form-group form-group-full"><label class="form-label">Bio</label><textarea class="form-control" name="bio" id="setBio" rows="3"></textarea></div>
                </div>
                <button class="btn btn-primary" style="margin-top:16px;" onclick="saveProfile()"><i class="fas fa-save"></i> Save Profile</button>
            </div>
        </div>
        <div class="set-pane" id="security">
            <div class="card" style="padding:24px;">
                <h5 style="margin:0 0 16px;"><i class="fas fa-shield-halved" style="color:var(--primary-400);margin-right:8px;"></i> Change Password</h5>
                <form id="passwordForm">
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label">Current Password</label><input type="password" class="form-control" name="current_password" required></div>
                        <div class="form-group"></div>
                        <div class="form-group"><label class="form-label">New Password</label><input type="password" class="form-control" name="new_password" required></div>
                        <div class="form-group"><label class="form-label">Confirm New Password</label><input type="password" class="form-control" name="confirm_password" required></div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top:16px;"><i class="fas fa-lock"></i> Update Password</button>
                </form>
            </div>
        </div>
        <div class="set-pane" id="preferences">
            <div class="card" style="padding:24px;">
                <h5 style="margin:0 0 16px;"><i class="fas fa-bell" style="color:var(--primary-400);margin-right:8px;"></i> Preferences</h5>
                <label class="switch-row"><span>Email notifications</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                <label class="switch-row"><span>New message alerts</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                <label class="switch-row"><span>Assignment reminders</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                <label class="switch-row"><span>Promotional emails</span><span class="switch"><input type="checkbox"><span class="slider"></span></span></label>
                <button class="btn btn-primary" style="margin-top:16px;" onclick="SkillShare.showToast('Success', 'Preferences saved', 'success')"><i class="fas fa-save"></i> Save Preferences</button>
            </div>
        </div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function switchTab(id, btn) {
    document.querySelectorAll('.set-pane').forEach(function(p) { p.classList.remove('active'); });
    document.querySelectorAll('.set-tab').forEach(function(b) { b.classList.remove('active'); });
    document.getElementById(id).classList.add('active');
    btn.classList.add('active');
}

SkillShare.apiFetch(BASE + 'api/me.php').then(function(res) {
    if (res.success && res.data) {
        var u = res.data;
        document.getElementById('setFullName').value = u.full_name || '';
        document.getElementById('setEmail').value = u.email || '';
        document.getElementById('setPhone').value = u.phone || '';
        document.getElementById('setInstitution').value = u.institution || '';
        document.getElementById('setEduLevel').value = u.education_level || '';
        document.getElementById('setGradYear').value = u.graduation_year || '';
        document.getElementById('setBio').value = u.bio || '';
    }
});

function saveProfile() {
    var fd = new FormData();
    fd.append('full_name', document.getElementById('setFullName').value);
    fd.append('phone', document.getElementById('setPhone').value);
    fd.append('institution', document.getElementById('setInstitution').value);
    fd.append('education_level', document.getElementById('setEduLevel').value);
    fd.append('graduation_year', document.getElementById('setGradYear').value);
    fd.append('bio', document.getElementById('setBio').value);
    fetch(BASE + 'api/profile.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) { SkillShare.showToast('Success', 'Profile updated', 'success'); }
            else { SkillShare.showToast('Error', res.message, 'error'); }
        });
}

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var fd = new FormData(this);
    fd.append('action', 'change_password');
    fetch(BASE + 'api/settings.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) { SkillShare.showToast('Success', 'Password updated', 'success'); this.reset(); }
            else { SkillShare.showToast('Error', res.message || 'Failed', 'error'); }
        }.bind(this));
});
</script>

<?php
endDashboardPage();
