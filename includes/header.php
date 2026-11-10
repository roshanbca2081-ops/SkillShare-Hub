<!DOCTYPE html>
<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/user-preferences.php';
$currentScriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isFresherSectionPage = isLoggedIn()
    && getUserRole() === 'fresher'
    && strpos($currentScriptPath, '/fresher/') !== false;
$isMentorSectionPage = isLoggedIn()
    && getUserRole() === 'mentor'
    && strpos($currentScriptPath, '/mentor/') !== false;

// Get user theme preference from database if logged in
$userThemePreference = 'system';
if (isLoggedIn()) {
    $userThemePreference = getUserThemePreference($pdo, getUserId());
}
?>
<html lang="en" data-theme="<?php echo htmlspecialchars($userThemePreference, ENT_QUOTES, 'UTF-8'); ?>" data-theme-preference="<?php echo htmlspecialchars($userThemePreference, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillShare Hub - <?php echo $page_title ?? 'Home'; ?></title>
    <script>
        (function() {
            try {
                const storageKey = 'skillshare-theme';
                // Use user's database preference if available, otherwise fall back to localStorage
                const userTheme = '<?php echo htmlspecialchars($userThemePreference, ENT_QUOTES, 'UTF-8'); ?>';
                const savedTheme = localStorage.getItem(storageKey) || 'system';
                const theme = userTheme !== 'system' ? userTheme : savedTheme;
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const resolvedTheme = theme === 'system' ? (prefersDark ? 'dark' : 'light') : theme;
                document.documentElement.setAttribute('data-theme', resolvedTheme);
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <script>
        // Global appUrl function for JavaScript
        window.appUrl = function(path) {
            const baseUrl = '<?php echo appUrl(''); ?>';
            return baseUrl + (path || '').replace(/^\//, '');
        };
    </script>
    <?php if (isLoggedIn()): ?><meta name="csrf-token" content="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>"><?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/style.css'); ?>?v=20261106-theme-tokens-v5">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/responsive.css'); ?>?v=20261106-theme-tokens-v5">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/landing.css'); ?>?v=20261106-theme-tokens-v4">
    <?php if ($isFresherSectionPage): ?>
        <link rel="stylesheet" href="<?php echo appUrl('assets/css/fresher-layout.css'); ?>?v=20261106-theme-tokens-v4">
    <?php endif; ?>
    <?php if ($isMentorSectionPage): ?>
        <link rel="stylesheet" href="<?php echo appUrl('assets/css/role-dashboard.css'); ?>?v=20261106-theme-tokens-v4">
    <?php endif; ?>
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?php echo appUrl('assets/css/' . $page_css); ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/mobile-responsive.css'); ?>?v=20261106-theme-tokens-v5">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/theme-overrides.css'); ?>?v=20261106-theme-tokens-v4">
</head>
<body<?php echo $isFresherSectionPage ? ' class="fresher-layout-page"' : ($isMentorSectionPage ? ' class="role-dashboard-page mentor-dashboard-page"' : ''); ?>>