<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillShare Hub Admin - <?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/landing.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/admin.css'); ?>">
</head>
<body class="admin-body">