<?php
/*
 * Shared Platform Header - includes <head>, fonts, shared CSS,
 * animated background, floating logo/icons, and vertical navbar.
 * Usage: include 'frontend/components/platform-header.php';
 * Set $page_title before including.
 */
$page_title = isset($page_title) ? $page_title : 'SkillShare Hub';
$page_active = isset($page_active) ? $page_active : 'Home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/navbar.css">
    <link rel="stylesheet" href="frontend/assets/css/platform.css">
</head>
<body>

<!-- ============================================
   ANIMATED BACKGROUND
   ============================================ -->
<div class="bg-animated"></div>
<div class="floating-logo">SkillShare Hub</div>

<div class="floating-icons">
    <div class="icon" style="top:8%;left:5%;"><i class="fa-solid fa-robot"></i><span class="label">Engineering</span></div>
    <div class="icon" style="top:15%;right:8%;animation-delay:2s;"><i class="fa-solid fa-laptop-code"></i><span class="label">IT</span></div>
    <div class="icon" style="top:45%;left:3%;animation-delay:4s;"><i class="fa-solid fa-flask"></i><span class="label">Science</span></div>
    <div class="icon" style="top:55%;right:4%;animation-delay:6s;"><i class="fa-solid fa-briefcase"></i><span class="label">Management</span></div>
    <div class="icon" style="bottom:20%;left:8%;animation-delay:8s;"><i class="fa-solid fa-scale-balanced"></i><span class="label">Law</span></div>
    <div class="icon" style="bottom:30%;right:10%;animation-delay:10s;"><i class="fa-solid fa-graduation-cap"></i><span class="label">Education</span></div>
    <div class="icon" style="top:30%;left:12%;animation-delay:3s;"><i class="fa-solid fa-seedling"></i><span class="label">Agriculture</span></div>
    <div class="icon" style="top:70%;left:15%;animation-delay:7s;"><i class="fa-solid fa-heart-pulse"></i><span class="label">Health</span></div>
    <div class="icon" style="top:85%;right:15%;animation-delay:5s;"><i class="fa-solid fa-palette"></i><span class="label">Arts</span></div>
    <div class="icon" style="top:10%;left:25%;animation-delay:9s;"><i class="fa-solid fa-newspaper"></i><span class="label">Media</span></div>
    <div class="icon" style="bottom:10%;left:30%;animation-delay:1s;"><i class="fa-solid fa-umbrella-beach"></i><span class="label">Hospitality</span></div>
    <div class="icon" style="top:50%;left:50%;animation-delay:11s;"><i class="fa-solid fa-microscope"></i><span class="label">Research</span></div>
</div>

<div class="mobile-nav-toggle-container">
    <button class="mobile-nav-toggle" type="button" aria-label="Open navigation">
        <i class="fa-solid fa-bars"></i>
    </button>
</div>

<!-- ============================================
   VERTICAL NAVBAR (SIDEBAR)
   ============================================ -->
<?php $navbar_active = $page_active; include 'frontend/components/navbar.php'; ?>

<!-- ============================================
   MAIN CONTENT WRAP (offset by sidebar)
   ============================================ -->
<div class="with-v-nav">
    <main class="page-section">
