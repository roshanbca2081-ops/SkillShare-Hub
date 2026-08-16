<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'mentor') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Settings';
$sidebar_role = 'mentor';
$sidebar_active = 'Settings';

startDashboardPage();
?>

<style>
.settings-layout { display: grid; grid-template-columns: 240px 1fr; gap: 20px; }
@media (max-width: 768px) { .settings-layout { grid-template-columns: 1fr; } }
.settings-nav { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 12px; height: fit-content; }
.settings-nav button { display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 14px; border-radius: var(--radius-md); border: none; background: transparent; color: var(--text-secondary); font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; text-align: left; }
.settings-nav button:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
.settings-nav button.active { background: rgba(59,130,246,0.12); color: var(--primary-400); }
.settings-nav button i { width: 18px; text-align: center; }
.panel { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 20px; }
.panel h4 { font-size: 1rem; margin-bottom: 16px; }
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
.form-group label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px; font-weight: 500; }
.form-group input, .form-group textarea { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: var(--radius-md); color: var(--text-primary); font-size: 0.9rem; outline: none; transition: all 0.3s ease; }
.form-group input:focus, .form-group textarea:focus { border-color: var(--primary-500); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.btn-primary { padding: 10px 20px; border-radius: var(--radius-full); background: var(--gradient-primary); border: none; color: #fff; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.85rem; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(59,130,246,0.3); }
.toggle-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--glass-border); }
.toggle-row:last-child { border-bottom: none; }
.toggle-row span { font-size: 0.85rem; }
.toggle { width: 44px; height: 24px; background: rgba(255,255,255,0.1); border-radius: 12px; position: relative; cursor: pointer; transition: all 0.3s ease; }
.toggle.on { background: var(--primary-500); }
.toggle::after { content: ''; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: all 0.3s ease; }
.toggle.on::after { left: 23px; }
</style>

<div class="settings-layout">
    <div class="settings-nav">
        <button class="active" onclick="showTab('profile')"><i class="fas fa-user"></i> Profile</button>
        <button onclick="showTab('security')"><i class="fas fa-shield-halved"></i> Security</button>
        <button onclick="showTab('notifications')"><i class="fas fa-bell"></i> Notifications</button>
        <button onclick="showTab('preferences')"><i class="fas fa-sliders"></i> Preferences</button>
    </div>
    <div>
        <div class="panel" id="tab-profile">
            <h4><i class="fas fa-user" style="color:var(--primary-400);"></i> Profile Settings</h4>
            <form id="profileForm">
                <div class="form-grid">
                    <div class="form-group"><label>Display Name</label><input type="text" name="full_name" id="setFullName"></div>
                    <div class="form-group"><label>Email</label><input type="email" id="setEmail" disabled></div>
                    <div class="form-group"><label>Phone</label><input type="text" name="phone" id="setPhone"></div>
                    <div class="form-group"><label>Website</label><input type="url" name="website_url" id="setWebsite"></div>
                    <div class="form-group" style="grid-column:1/-1;"><label>Bio</label><textarea name="bio" id="setBio" rows="3"></textarea></div>
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Profile</button>
            </form>
        </div>

        <div class="panel" id="tab-security" style="display:none;">
            <h4><i class="fas fa-shield-halved" style="color:var(--warning);"></i> Change Password</h4>
            <form id="passwordForm">
                <div class="form-grid">
                    <div class="form-group"><label>Current Password</label><input type="password" name="current_password" required></div>
                    <div class="form-group"><label>New Password</label><input type="password" name="new_password" required minlength="6"></div>
                    <div class="form-group"><label>Confirm New Password</label><input type="password" name="confirm_password" required></div>
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-key"></i> Update Password</button>
            </form>
        </div>

        <div class="panel" id="tab-notifications" style="display:none;">
            <h4><i class="fas fa-bell" style="color:var(--secondary-400);"></i> Notification Preferences</h4>
            <div id="notifToggles">
                <div class="toggle-row"><span>Email notifications</span><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
                <div class="toggle-row"><span>New booking alerts</span><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
                <div class="toggle-row"><span>New message alerts</span><div class="toggle" onclick="this.classList.toggle('on')"></div></div>
                <div class="toggle-row"><span>Assignment submissions</span><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
                <div class="toggle-row"><span>Payment notifications</span><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
            </div>
            <button class="btn-primary" style="margin-top:14px;" onclick="SkillShare.showToast('Saved','Notification preferences updated','success')"><i class="fas fa-save"></i> Save Preferences</button>
        </div>

        <div class="panel" id="tab-preferences" style="display:none;">
            <h4><i class="fas fa-sliders" style="color:var(--success);"></i> Preferences</h4>
            <div class="form-grid">
                <div class="form-group"><label>Language</label><select><option>English</option><option>Spanish</option><option>French</option></select></div>
                <div class="form-group"><label>Timezone</label><select><option>UTC</option><option>EST</option><option>PST</option><option>IST</option></select></div>
                <div class="form-group"><label>Currency</label><select><option>USD</option><option>EUR</option><option>GBP</option><option>INR</option></select></div>
            </div>
            <button class="btn-primary" style="margin-top:14px;" onclick="SkillShare.showToast('Saved','Preferences updated','success')"><i class="fas fa-save"></i> Save Preferences</button>
        </div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

function showTab(name) {
    ['profile','security','notifications','preferences'].forEach(function(t) {
        document.getElementById('tab-' + t).style.display = t === name ? '' : 'none';
    });
    document.querySelectorAll('.settings-nav button').forEach(function(b) { b.classList.toggle('active', b.getAttribute('onclick').includes(name)); });
}

SkillShare.apiFetch(BASE + 'api/profile.php').then(function(res) {
    if (res.success && res.data) {
        var d = res.data;
        document.getElementById('setFullName').value = d.full_name || '';
        document.getElementById('setEmail').value = d.email || '';
        document.getElementById('setPhone').value = d.phone || '';
        document.getElementById('setWebsite').value = d.website_url || d.portfolio_url || '';
        document.getElementById('setBio').value = d.bio || '';
    }
});

document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    SkillShare.apiFetch(BASE + 'api/profile.php', { method: 'POST', body: formData }).then(function(res) {
        if (res.success) SkillShare.showToast('Success', 'Profile updated', 'success');
        else SkillShare.showToast('Error', res.message || 'Failed', 'error');
    });
});

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    SkillShare.apiFetch(BASE + 'api/settings.php?action=change_password', { method: 'POST', body: formData }).then(function(res) {
        if (res.success) { SkillShare.showToast('Success', 'Password updated', 'success'); e.target.reset(); }
        else SkillShare.showToast('Error', res.message || 'Failed', 'error');
    });
});
</script>

<?php
endDashboardPage();
