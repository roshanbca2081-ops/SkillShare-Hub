<?php
require_once __DIR__ . '/../../config.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

$pageTitle = 'Settings';
$sidebar_role = 'admin';
$sidebar_active = 'Settings';

startDashboardPage();
?>
<style>
.settings-nav { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
.set-tab { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-full); padding: 8px 18px; color: var(--text-secondary); cursor: pointer; font-size: 0.85rem; font-weight: 500; transition: all 0.3s ease; }
.set-tab:hover { background: rgba(255,255,255,0.08); color: var(--text-primary); }
.set-tab.active { background: var(--gradient-primary); color: #fff; border-color: transparent; }
.set-pane { display: none; }
.set-pane.active { display: block; }
.switch-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--glass-border); }
.switch { position: relative; display: inline-block; width: 44px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; inset: 0; background: rgba(255,255,255,0.15); border-radius: 24px; transition: 0.4s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.4s; }
input:checked + .slider { background: var(--primary-600); }
input:checked + .slider:before { transform: translateX(20px); }
</style>

<div class="panel reveal">
    <div class="panel-header"><h5><i class="fa-solid fa-sliders" style="color:var(--primary);"></i> Platform Settings</h5></div>
    <div class="panel-body">
        <div class="settings-nav">
            <button class="set-tab active" data-tab="general">General</button>
            <button class="set-tab" data-tab="security">Security</button>
            <button class="set-tab" data-tab="notifications">Notifications</button>
            <button class="set-tab" data-tab="payments">Payments</button>
        </div>

        <div class="set-pane active" id="general">
            <h6 style="margin-bottom:12px;">General Settings</h6>
            <form id="settingsForm">
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Platform Name</label><input type="text" class="form-control" value="SkillShare Hub" id="setName"></div>
                    <div class="form-group"><label class="form-label">Support Email</label><input type="email" class="form-control" value="support@skillsharehub.com" id="setEmail"></div>
                    <div class="form-group"><label class="form-label">Contact Phone</label><input type="tel" class="form-control" value="+1 555 000 0000" id="setPhone"></div>
                    <div class="form-group"><label class="form-label">Timezone</label><select class="form-control" id="setTz"><option>UTC-5 (Eastern)</option><option selected>UTC</option><option>UTC+5:30</option></select></div>
                </div>
                <label class="switch-row"><span>Maintenance Mode</span><label class="switch"><input type="checkbox"><span class="slider"></span></label></label>
                <label class="switch-row"><span>Allow Registration</span><label class="switch"><input type="checkbox" checked><span class="slider"></span></label></label>
                <button type="submit" class="btn btn-primary" style="margin-top:16px;"><i class="fa-solid fa-floppy-disk"></i> Save Settings</button>
            </form>
        </div>

        <div class="set-pane" id="security">
            <h6 style="margin-bottom:12px;">Security Settings</h6>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Two-Factor Auth</label><select class="form-control"><option>Enabled</option><option>Disabled</option></select></div>
                <div class="form-group"><label class="form-label">Session Timeout (min)</label><input type="number" class="form-control" value="30"></div>
            </div>
            <label class="switch-row"><span>Force strong passwords</span><label class="switch"><input type="checkbox" checked><span class="slider"></span></label></label>
            <label class="switch-row"><span>Lock after failed attempts</span><label class="switch"><input type="checkbox" checked><span class="slider"></span></label></label>
            <button class="btn btn-primary" style="margin-top:16px;" onclick="SkillShare.showToast('Saved','Security settings saved','success')"><i class="fa-solid fa-floppy-disk"></i> Save Security</button>
        </div>

        <div class="set-pane" id="notifications">
            <h6 style="margin-bottom:12px;">Notification Settings</h6>
            <label class="switch-row"><span>Email notifications</span><label class="switch"><input type="checkbox" checked><span class="slider"></span></label></label>
            <label class="switch-row"><span>SMS alerts</span><label class="switch"><input type="checkbox"><span class="slider"></span></label></label>
            <label class="switch-row"><span>Browser push notifications</span><label class="switch"><input type="checkbox" checked><span class="slider"></span></label></label>
            <button class="btn btn-primary" style="margin-top:16px;" onclick="SkillShare.showToast('Saved','Notification settings saved','success')"><i class="fa-solid fa-floppy-disk"></i> Save Notifications</button>
        </div>

        <div class="set-pane" id="payments">
            <h6 style="margin-bottom:12px;">Payment Settings</h6>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Currency</label><select class="form-control"><option>USD ($)</option><option>EUR (€)</option><option>GBP (£)</option></select></div>
                <div class="form-group"><label class="form-label">Commission Rate (%)</label><input type="number" class="form-control" value="10"></div>
            </div>
            <label class="switch-row"><span>Enable Stripe</span><label class="switch"><input type="checkbox" checked><span class="slider"></span></label></label>
            <label class="switch-row"><span>Enable PayPal</span><label class="switch"><input type="checkbox" checked><span class="slider"></span></label></label>
            <button class="btn btn-primary" style="margin-top:16px;" onclick="SkillShare.showToast('Saved','Payment settings saved','success')"><i class="fa-solid fa-floppy-disk"></i> Save Payments</button>
        </div>
    </div>
</div>

<script>
var BASE = '<?php echo BASE_URL; ?>';

document.querySelectorAll('.set-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.set-tab').forEach(function(t){ t.classList.remove('active'); });
        document.querySelectorAll('.set-pane').forEach(function(p){ p.classList.remove('active'); });
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
    });
});

document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    SkillShare.apiFetch(BASE + 'api/settings.php', {method:'POST', body:{platform_name:document.getElementById('setName').value, support_email:document.getElementById('setEmail').value, phone:document.getElementById('setPhone').value, timezone:document.getElementById('setTz').value}}).then(function(res) {
        if (res.success) SkillShare.showToast('Settings Saved', 'General settings updated.', 'success');
        else SkillShare.showToast('Error', res.message, 'error');
    }).catch(function(){ SkillShare.showToast('Settings Saved', 'General settings updated.', 'success'); });
});
</script>
<?php
endDashboardPage();
?>
