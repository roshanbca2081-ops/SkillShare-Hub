<?php
/**
 * Admin Header Include
 * Contains HTML head, meta tags, and external resource links
 */
$pageTitle = $pageTitle ?? 'Admin Panel';
$currentPage = basename($_SERVER['PHP_SELF']);
$adminUser = getAdminUser();
$adminName = $adminUser['full_name'] ?? 'Admin';
$adminAvatar = $adminUser['profile_picture'] ?? 'default.png';
$avatarUrl = (file_exists(__DIR__ . '/../assets/images/' . $adminAvatar) && $adminAvatar !== 'default.png')
    ? ADMIN_ASSETS_URL . 'images/' . $adminAvatar
    : 'https://ui-avatars.com/40/' . urlencode(substr($adminName, 0, 2)) . '?background=3b82f6&color=fff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo htmlspecialchars($pageTitle); ?> - SkillShare Hub Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>frontend/assets/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css">
</head>
<body>
<div class="bg-animated"></div>
<div class="admin-wrapper">
    <?php require_once __DIR__ . '/functions.php'; ?>
    <?php require_once __DIR__ . '/sidebar.php'; ?>
    <div class="admin-main">
        <?php require_once __DIR__ . '/navbar.php'; ?>
