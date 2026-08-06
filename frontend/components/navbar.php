<?php
// Vertical Navbar (Sidebar Navigation)
// Set $navbar_active to highlight current link
$nav_items = isset($nav_items) ? $nav_items : [
    ['label' => 'Home', 'icon' => 'fa-house', 'link' => 'index.php'],
    ['label' => 'About', 'icon' => 'fa-circle-info', 'link' => 'about.php'],
    ['label' => 'Academic Fields', 'icon' => 'fa-layer-group', 'link' => 'academic-filed.php'],
    ['label' => 'Courses', 'icon' => 'fa-book-open', 'link' => 'courses.php'],
    ['label' => 'Mentors', 'icon' => 'fa-user-tie', 'link' => 'mentor.php'],
    ['label' => 'Research', 'icon' => 'fa-flask', 'link' => 'research.php'],
    ['label' => 'Blog', 'icon' => 'fa-blog', 'link' => '#'],
    ['label' => 'Contact', 'icon' => 'fa-envelope', 'link' => 'contact.php'],
];
?>
<nav class="v-navbar" id="vNavbar">
    <button class="nav-toggle-btn" id="navToggleBtn" aria-label="Toggle navigation">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <div class="nav-brand">
        <div class="brand-logo"><i class="fa-solid fa-graduation-cap"></i></div>
        <div class="brand-text">
            <span>SkillShare</span>
            <small>Hub</small>
        </div>
    </div>

    <div class="nav-menu">
        <p class="menu-label">Main Menu</p>
        <ul>
            <?php foreach ($nav_items as $item): ?>
                <?php
                $active = (isset($navbar_active) && $navbar_active === $item['label']);
                ?>
                <li>
                    <a href="<?php echo $item['link']; ?>" class="nav-link <?php echo $active ? 'active' : ''; ?>">
                        <i class="fa-solid <?php echo $item['icon']; ?>"></i>
                        <span><?php echo $item['label']; ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="menu-label">Account</p>
        <ul>
            <li>
                <a href="login.php" class="nav-link">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Login</span>
                </a>
            </li>
            <li>
                <a href="register.php" class="nav-link">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Register</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="nav-footer">
        <div class="nav-user">
            <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
            <div class="user-info">
                <small style="display:block;color:rgba(255,255,255,.6);font-size:.7rem;">Guest</small>
                <strong style="font-size:.85rem;">Join SkillShare</strong>
            </div>
        </div>
    </div>
</nav>

<!-- Overlay for mobile -->
<div class="nav-overlay" id="navOverlay"></div>
