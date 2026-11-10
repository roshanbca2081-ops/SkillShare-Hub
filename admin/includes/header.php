<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/user-preferences.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();
$adminThemePreference = getUserThemePreference($pdo, getUserId());
?>
<!DOCTYPE html>
<html lang="en" data-theme-preference="<?php echo htmlspecialchars($adminThemePreference, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
    <title>SkillShare Hub Admin - <?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></title>
    <script>
        (function() {
            try {
                const preference = '<?php echo htmlspecialchars($adminThemePreference, ENT_QUOTES, 'UTF-8'); ?>';
                const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = preference === 'system' ? (systemDark ? 'dark' : 'light') : preference;
                document.documentElement.setAttribute('data-theme', ['light', 'dark'].includes(theme) ? theme : 'light');
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/style.css'); ?>?v=20261106-theme-tokens-v5">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/landing.css'); ?>?v=20261106-theme-tokens-v4">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/admin.css'); ?>?v=20261106-theme-tokens-v4">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/role-dashboard.css'); ?>?v=20261106-theme-tokens-v4">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/mobile-responsive.css'); ?>?v=20261106-theme-tokens-v5">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/theme-overrides.css'); ?>?v=20261106-theme-tokens-v4">
</head>
<body class="admin-body role-dashboard-page admin-dashboard-page">
<header class="role-dashboard-topbar admin-role-topbar">
    <div class="role-topbar-context"><span class="role-topbar-eyebrow">ADMIN WORKSPACE</span><strong>Platform overview</strong></div>
    <div class="role-topbar-actions">
        <span class="role-topbar-user"><span class="role-topbar-avatar"><i class="fas fa-user-shield" aria-hidden="true"></i></span><?php echo htmlspecialchars(getUserName(), ENT_QUOTES, 'UTF-8'); ?></span>
        <a class="role-topbar-back" href="<?php echo appUrl('admin/dashboard.php'); ?>" onclick="if (window.history.length > 1) { window.history.back(); return false; }" aria-label="Go to previous page"><i class="fas fa-arrow-left" aria-hidden="true"></i><span>Back</span></a>
    </div>
</header>