<?php
// Renders sidebar from $sidebar_items and $sidebar_active set by _shared.php
$roleLabel = '';
if ($sidebar_role === 'fresher') $roleLabel = 'Fresher';
elseif ($sidebar_role === 'mentor') $roleLabel = 'Mentor';
elseif ($sidebar_role === 'admin') $roleLabel = 'Admin';

$logoutUrl = BASE_URL . 'logout.php';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="<?php echo BASE_URL; ?>frontend/assets/images/logo/skillshare hub.png" alt="SkillShare Hub Logo" style="height:36px; width:auto; object-fit:contain;">
        <h4>SkillShare <span>Hub</span></h4>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($sidebar_items as $group => $items): ?>
            <p class="menu-label" style="color:var(--text-muted);font-size:0.7rem;font-weight:600;padding:8px 14px 4px;letter-spacing:1px;"><?php echo $group; ?></p>
            <?php foreach ($items as $item): ?>
                <?php
                $isActive = ($sidebar_active === $item['label']);
                $badge = isset($item['badge']) ? '<span class="badge">' . e($item['badge']) . '</span>' : '';
                ?>
                <a href="<?php echo $item['link']; ?>" class="nav-item <?php echo $isActive ? 'active' : ''; ?>">
                    <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                    <?php echo $item['label']; ?>
                    <?php echo $badge; ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="<?php echo BASE_URL; ?>settings.php" class="nav-item">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="<?php echo $logoutUrl; ?>" class="nav-item">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>    </div>
</aside>
