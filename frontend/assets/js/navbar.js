/* =============================================
   SkillShare Hub - Navbar / Vertical Sidebar JS
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // Vertical navbar collapse toggle
    const navToggle = document.querySelector('.nav-toggle-btn');
    const vNavbar = document.querySelector('.v-navbar');
    if (navToggle && vNavbar) {
        navToggle.addEventListener('click', function () {
            vNavbar.classList.toggle('collapsed');
            document.body.classList.toggle('nav-collapsed');
        });
    }

    // Submenu dropdown toggle
    document.querySelectorAll('.v-navbar .has-submenu > .nav-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const parent = this.parentElement;
            const isOpen = parent.classList.contains('open');
            // Close all sibling submenus
            parent.parentElement.querySelectorAll('.has-submenu.open').forEach(function (item) {
                item.classList.remove('open');
            });
            if (!isOpen) parent.classList.add('open');
        });
    });

    // Mobile sidebar toggle (hamburger for vertical nav)
    const mobileNavToggle = document.querySelectorAll('.mobile-nav-toggle');
    const overlay = document.querySelector('.nav-overlay');
    mobileNavToggle.forEach(function (btn) {
        btn.addEventListener('click', function () {
            vNavbar.classList.toggle('mobile-open');
            if (overlay) overlay.classList.toggle('show');
        });
    });

    // Active link highlight based on current page
    const currentPath = window.location.pathname.split('/').pop();
    document.querySelectorAll('.v-navbar .nav-menu a, .site-navbar .site-links a').forEach(function (link) {
        const href = link.getAttribute('href');
        if (href && href === currentPath) {
            link.classList.add('active');
        }
    });
});
