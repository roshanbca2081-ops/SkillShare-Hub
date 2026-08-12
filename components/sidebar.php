<?php
// Dashboard Sidebar
// Uses $sidebar_items (array) and $sidebar_active (string) from the page
$sidebar_items = isset($sidebar_items) ? $sidebar_items : [];
$sidebar_active = isset($sidebar_active) ? $sidebar_active : '';
$sidebar_role = isset($sidebar_role) ? $sidebar_role : 'user';
?>
<nav class="v-navbar dashboard-sidebar" id="vNavbar">
    <button class="nav-toggle-btn" id="navToggleBtn" aria-label="Toggle sidebar">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <div class="nav-brand">
        <div class="brand-logo">
            <img src="frontend/assets/images/logo/skillshare hub.png" alt="SkillShare Hub logo">
        </div>
        <div class="brand-text">
            <span>SkillShare</span>
            <small>Dashboard</small>
        </div>
    </div>

    <div class="nav-menu">
        <?php foreach ($sidebar_items as $group => $items): ?>
            <p class="menu-label"><?php echo $group; ?></p>
            <ul>
                <?php foreach ($items as $item): ?>
                    <?php
                    $is_active = ($sidebar_active === $item['label']);
                    $has_sub = isset($item['children']);
                    ?>
                    <li class="<?php echo $has_sub ? 'has-submenu' : ''; ?> <?php echo $is_active ? 'open' : ''; ?>">
                        <a href="<?php echo $has_sub ? '#' : $item['link']; ?>" class="nav-link <?php echo $is_active ? 'active' : ''; ?>">
                            <i class="fa-solid <?php echo $item['icon']; ?>"></i>
                            <span><?php echo $item['label']; ?></span>
                            <?php if (isset($item['badge'])): ?>
                                <span class="nav-badge"><?php echo $item['badge']; ?></span>
                            <?php endif; ?>
                        </a>
                        <?php if ($has_sub): ?>
                            <ul class="submenu">
                                <?php foreach ($item['children'] as $child): ?>
                                    <li>
                                        <a href="<?php echo $child['link']; ?>" class="<?php echo ($sidebar_active === $child['label']) ? 'active' : ''; ?>">
                                            <i class="fa-solid <?php echo $child['icon']; ?>"></i>
                                            <span><?php echo $child['label']; ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </div>

    <div class="nav-footer">
        <div class="nav-user">
            <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
            <div class="user-info">
                <small style="display:block;color:rgba(255,255,255,.6);font-size:.7rem;"><?php echo ucfirst($sidebar_role); ?></small>
                <strong style="font-size:.85rem;">User Name</strong>
            </div>
        </div>
    </div>
</nav>

<div class="nav-overlay" id="navOverlay"></div>
