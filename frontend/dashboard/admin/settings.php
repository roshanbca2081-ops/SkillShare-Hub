<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Settings';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Admin - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/setting.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include '../../components/loader.php'; ?>
    <?php include '../../components/sidebar.php'; ?>

    <div class="dashboard-main">
        <?php include __DIR__ . '/_topbar.php'; ?>

        <div class="panel reveal">
            <div class="panel-header"><h5><i class="fa-solid fa-sliders" style="color:var(--primary);"></i> Platform Settings</h5></div>
            <div class="panel-body">
                <div class="settings-nav">
                    <button class="set-tab active" data-tab="general"><i class="fa-solid fa-gear"></i> General</button>
                    <button class="set-tab" data-tab="security"><i class="fa-solid fa-shield-halved"></i> Security</button>
                    <button class="set-tab" data-tab="notifications"><i class="fa-solid fa-bell"></i> Notifications</button>
                    <button class="set-tab" data-tab="payments"><i class="fa-solid fa-credit-card"></i> Payments</button>
                </div>

                <div class="settings-content">
                    <div class="set-pane active" id="general">
                        <h6>General Settings</h6>
                        <div class="dash-form-grid">
                            <div class="form-group"><label class="form-label">Platform Name</label><input type="text" class="form-control" value="SkillShare Hub"></div>
                            <div class="form-group"><label class="form-label">Support Email</label><input type="email" class="form-control" value="support@skillsharehub.com"></div>
                            <div class="form-group"><label class="form-label">Contact Phone</label><input type="tel" class="form-control" value="+1 555 000 0000"></div>
                            <div class="form-group"><label class="form-label">Timezone</label><select class="form-control"><option>UTC-5 (Eastern)</option><option>UTC</option><option>UTC+5:30</option></select></div>
                        </div>
                        <label class="switch-row"><span>Maintenance Mode</span><span class="switch"><input type="checkbox"><span class="slider"></span></span></label>
                        <label class="switch-row"><span>Allow Registration</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <button class="btn btn-primary mt-2"><i class="fa-solid fa-floppy-disk"></i> Save Settings</button>
                    </div>
                    <div class="set-pane" id="security">
                        <h6>Security Settings</h6>
                        <div class="dash-form-grid">
                            <div class="form-group"><label class="form-label">Two-Factor Auth</label><select class="form-control"><option>Enabled</option><option>Disabled</option></select></div>
                            <div class="form-group"><label class="form-label">Session Timeout (min)</label><input type="number" class="form-control" value="30"></div>
                        </div>
                        <label class="switch-row"><span>Force strong passwords</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <label class="switch-row"><span>Lock after failed attempts</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <button class="btn btn-primary mt-2"><i class="fa-solid fa-floppy-disk"></i> Save Security</button>
                    </div>
                    <div class="set-pane" id="notifications">
                        <h6>Notification Settings</h6>
                        <label class="switch-row"><span>Email notifications</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <label class="switch-row"><span>SMS alerts</span><span class="switch"><input type="checkbox"><span class="slider"></span></span></label>
                        <label class="switch-row"><span>Browser push notifications</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <button class="btn btn-primary mt-2"><i class="fa-solid fa-floppy-disk"></i> Save Notifications</button>
                    </div>
                    <div class="set-pane" id="payments">
                        <h6>Payment Settings</h6>
                        <div class="dash-form-grid">
                            <div class="form-group"><label class="form-label">Currency</label><select class="form-control"><option>USD ($)</option><option>EUR (€)</option><option>GBP (£)</option></select></div>
                            <div class="form-group"><label class="form-label">Commission Rate (%)</label><input type="number" class="form-control" value="10"></div>
                        </div>
                        <label class="switch-row"><span>Enable Stripe</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <label class="switch-row"><span>Enable PayPal</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <button class="btn btn-primary mt-2"><i class="fa-solid fa-floppy-disk"></i> Save Payments</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/navbar.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/setting.js"></script>
    <script src="../../assets/js/animation.js"></script>
</body>

</html>
