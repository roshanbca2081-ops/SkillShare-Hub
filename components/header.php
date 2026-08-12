<?php
// Site Header - includes topbar and mobile nav toggle
// Vertical sidebar (navbar.php) is included separately
?>
<header class="site-header" id="siteHeader">
    <!-- Topbar -->
    <?php include __DIR__ . '/topbar.php'; ?>

    <!-- Mobile nav toggle (visible only on small screens) -->
    <div class="mobile-nav-toggle-container container-max" style="display:none;padding:.75rem 0;">
        <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</header>

<style>
    .mobile-nav-toggle-container { display: none !important; }
    @media (max-width: 991px) {
        .mobile-nav-toggle-container { display: flex !important; }
    }
</style>
