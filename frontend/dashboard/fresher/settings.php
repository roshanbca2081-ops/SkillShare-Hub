<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Settings';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Fresher - SkillShare Hub</title>
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
            <div class="panel-header"><h5><i class="fa-solid fa-sliders" style="color:var(--primary);"></i> Account Settings</h5></div>
            <div class="panel-body">
                <div class="settings-nav">
                    <button class="set-tab active" data-tab="profile"><i class="fa-solid fa-user"></i> Profile</button>
                    <button class="set-tab" data-tab="preferences"><i class="fa-solid fa-wand-magic-sparkles"></i> Preferences</button>
                    <button class="set-tab" data-tab="security"><i class="fa-solid fa-shield-halved"></i> Security</button>
                    <button class="set-tab" data-tab="notifications"><i class="fa-solid fa-bell"></i> Notifications</button>
                </div>
                <div class="settings-content">
                    <div class="set-pane active" id="profile">
                        <h6>Profile Settings</h6>
                        <div class="dash-form-grid">
                            <div class="form-group"><label class="form-label">Display Name</label><input type="text" class="form-control" value="Sarah Johnson"></div>
                            <div class="form-group"><label class="form-label">Tagline</label><input type="text" class="form-control" value="Aspiring Data Scientist"></div>
                            <div class="form-group"><label class="form-label">Public URL</label><input type="text" class="form-control" value="skillsharehub.com/sarah-j"></div>
                            <div class="form-group"><label class="form-label">Location</label><input type="text" class="form-control" value="New York, USA"></div>
                        </div>
                        <button class="btn btn-primary mt-2"><i class="fa-solid fa-floppy-disk"></i> Save Profile</button>
                    </div>
                    <div class="set-pane" id="preferences">
                        <h6>Learning Preferences</h6>
                        <label class="switch-row"><span>Weekly progress report</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <label class="switch-row"><span>Course recommendations</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <label class="switch-row"><span>Mentor availability alerts</span><span class="switch"><input type="checkbox"><span class="slider"></span></span></label>
                        <div class="form-group" style="margin-top:var(--spacing-5);">
                            <label class="form-label">Learning Track</label>
                            <select class="form-control">
                                <option>Data Science</option>
                                <option>Web Development</option>
                                <option>Machine Learning</option>
                                <option>Business Analytics</option>
                            </select>
                        </div>
                        <button class="btn btn-primary mt-2"><i class="fa-solid fa-floppy-disk"></i> Save Preferences</button>
                    </div>
                    <div class="set-pane" id="security">
                        <h6>Security Settings</h6>
                        <div class="dash-form-grid">
                            <div class="form-group"><label class="form-label">Current Password</label><input type="password" class="form-control" placeholder="••••••••"></div>
                            <div class="form-group"><label class="form-label">New Password</label><input type="password" class="form-control" placeholder="New password"></div>
                            <div class="form-group"><label class="form-label">Confirm Password</label><input type="password" class="form-control" placeholder="Confirm password"></div>
                        </div>
                        <label class="switch-row"><span>Enable two-factor auth</span><span class="switch"><input type="checkbox"><span class="slider"></span></span></label>
                        <label class="switch-row"><span>Active sessions</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <button class="btn btn-primary mt-2"><i class="fa-solid fa-floppy-disk"></i> Update Password</button>
                    </div>
                    <div class="set-pane" id="notifications">
                        <h6>Notification Settings</h6>
                        <label class="switch-row"><span>Email notifications</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <label class="switch-row"><span>New message alerts</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <label class="switch-row"><span>Assignment reminders</span><span class="switch"><input type="checkbox" checked><span class="slider"></span></span></label>
                        <label class="switch-row"><span>Promotional emails</span><span class="switch"><input type="checkbox"><span class="slider"></span></span></label>
                        <button class="btn btn-primary mt-2"><i class="fa-solid fa-floppy-disk"></i> Save Notifications</button>
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
</content>

