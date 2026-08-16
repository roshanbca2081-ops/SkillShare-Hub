<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Settings';
$pdo = getDB();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_csrf_check();
    $group = $_POST['group'] ?? 'general';
    $settings = $_POST['settings'] ?? [];
    
    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (key_name, value, group_name, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE value = ?, updated_at = NOW()");
        $stmt->execute([$key, $value, $group, $value]);
    }
    $message = 'Settings saved successfully!';
}

$groups = $pdo->query("SELECT DISTINCT group_name FROM settings ORDER BY group_name")->fetchAll(PDO::FETCH_COLUMN);
$activeGroup = $_GET['group'] ?? ($groups[0] ?? 'general');
$settings = $pdo->prepare("SELECT * FROM settings WHERE group_name = ? ORDER BY key_name");
$settings->execute([$activeGroup]);
$settingsList = $settings->fetchAll();

// Default settings if none exist
if (empty($settingsList)) {
    $defaults = [
        'site_name' => 'SkillShare Hub',
        'site_description' => 'Bridging Education with Industry',
        'contact_email' => 'info@skillsharehub.com',
        'contact_phone' => '+1234567890',
        'site_status' => 'active',
        'registration_enabled' => '1',
        'mentor_registration_enabled' => '1',
        'notifications_enabled' => '1',
    ];
    foreach ($defaults as $key => $value) {
        $pdo->prepare("INSERT IGNORE INTO settings (key_name, value, group_name) VALUES (?, ?, ?)")->execute([$key, $value, $activeGroup]);
    }
    $settingsList = $pdo->prepare("SELECT * FROM settings WHERE group_name = ? ORDER BY key_name");
    $settingsList->execute([$activeGroup]);
    $settingsList = $settingsList->fetchAll();
}
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="admin-content">
    <div class="admin-page-header">
        <h3>System Settings</h3>
    </div>

    <?php if ($message): ?>
    <div class="admin-card" style="padding:16px 20px;background:#dcfce7;border:1px solid #bbf7d0;margin-bottom:24px;border-radius:12px;color:#16a34a;">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="admin-toolbar">
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <?php foreach ($groups as $group): ?>
                <a href="?group=<?php echo urlencode($group); ?>" class="admin-btn <?php echo $activeGroup === $group ? 'admin-btn-primary' : 'admin-btn-secondary'; ?> admin-btn-sm"><?php echo ucfirst($group); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="admin-card-body" style="padding:20px;">
            <form method="POST">
                <?php echo admin_csrf_field(); ?>
                <input type="hidden" name="group" value="<?php echo htmlspecialchars($activeGroup); ?>">
                <div class="admin-form-row">
                    <?php foreach ($settingsList as $setting): ?>
                    <div class="admin-form-group">
                        <label class="admin-form-label"><?php echo ucwords(str_replace('_', ' ', $setting['key_name'])); ?></label>
                        <?php if (in_array($setting['key_name'], ['site_status', 'registration_enabled', 'mentor_registration_enabled', 'notifications_enabled'])): ?>
                        <select name="settings[<?php echo $setting['key_name']; ?>]" class="admin-form-control">
                            <option value="1" <?php echo $setting['value'] == '1' ? 'selected' : ''; ?>>Enabled</option>
                            <option value="0" <?php echo $setting['value'] == '0' ? 'selected' : ''; ?>>Disabled</option>
                        </select>
                        <?php else: ?>
                        <input type="text" name="settings[<?php echo $setting['key_name']; ?>]" class="admin-form-control" value="<?php echo htmlspecialchars($setting['value']); ?>">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="admin-btn admin-btn-primary" style="margin-top:16px;"><i class="fas fa-save"></i> Save Settings</button>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

