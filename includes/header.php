<!DOCTYPE html>
<?php require_once __DIR__ . '/../config/session.php'; ?>
<?php require_once __DIR__ . '/../config/functions.php'; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillShare Hub - <?php echo $page_title ?? 'Home'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/responsive.css'); ?>">
    <link rel="stylesheet" href="<?php echo appUrl('assets/css/landing.css'); ?>">
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?php echo appUrl('assets/css/' . $page_css); ?>">
    <?php endif; ?>
</head>
<body>