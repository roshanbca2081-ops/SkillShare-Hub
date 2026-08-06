<?php
// Top utility bar
$topbar_visible = isset($topbar_visible) ? $topbar_visible : true;
if (!$topbar_visible) return;
?>
<div class="site-topbar">
    <div class="container-max topbar-inner">
        <div class="topbar-left">
            <span><i class="fa-solid fa-envelope"></i> support@skillsharehub.com</span>
            <span><i class="fa-solid fa-phone"></i> +1 (555) 123-4567</span>
        </div>
        <div class="topbar-right">
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
            <a href="login.php"><i class="fa-solid fa-user"></i> Login</a>
            <a href="register.php" class="btn btn-primary btn-sm">Join Free</a>
        </div>
    </div>
</div>
