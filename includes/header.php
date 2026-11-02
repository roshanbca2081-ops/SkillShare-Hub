<!DOCTYPE html>
<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';
$currentScriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isFresherSectionPage = isLoggedIn()
    && getUserRole() === 'fresher'
    && strpos($currentScriptPath, '/fresher/') !== false;
$isMentorSectionPage = isLoggedIn()
    && getUserRole() === 'mentor'
    && strpos($currentScriptPath, '/mentor/') !== false;
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillShare Hub - <?php echo $page_title ?? 'Home'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/style.css'); ?>?v=20261031-fixed-sidebar-independent-scroll-v2">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/responsive.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/landing.css'); ?>?v=20261101-landing-v6">
    <?php if ($isFresherSectionPage): ?>
        <link rel="stylesheet" href="<?php echo appUrl('assets/css/fresher-layout.css'); ?>?v=20261101-fresher-layout-v10">
    <?php endif; ?>
    <?php if ($isMentorSectionPage): ?>
        <link rel="stylesheet" href="<?php echo appUrl('assets/css/role-dashboard.css'); ?>?v=20261101-role-dashboard-v6">
    <?php endif; ?>
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?php echo appUrl('assets/css/' . $page_css); ?>">
    <?php endif; ?>
</head>
<body<?php echo $isFresherSectionPage ? ' class="fresher-layout-page"' : ($isMentorSectionPage ? ' class="role-dashboard-page mentor-dashboard-page"' : ''); ?>>