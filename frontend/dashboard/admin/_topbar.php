<?php
// Shared dashboard topbar
?>
<div class="dash-topbar">
    <div class="dt-left">
        <button class="hamburger sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar"><span></span><span></span><span></span></button>
        <div class="dt-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="tableSearch" placeholder="Search dashboard..."></div>
    </div>
    <div class="dt-right">
        <button class="dt-icon-btn" aria-label="Notifications"><i class="fa-solid fa-bell"></i><span class="notif-dot"></span></button>
        <button class="dt-icon-btn" aria-label="Messages"><i class="fa-solid fa-envelope"></i></button>
        <button class="dt-avatar-btn"><img src="../../assets/images/profile/avatar-1.svg" alt="Admin"><span><?php echo ucfirst($sidebar_role); ?></span><i class="fa-solid fa-chevron-down" style="font-size:.7rem;color:var(--gray-500);"></i></button>
    </div>
</div>
