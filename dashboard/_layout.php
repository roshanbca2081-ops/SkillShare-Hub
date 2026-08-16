<?php
/**
 * Dashboard page renderer
 * Usage in a dashboard page:
 *   require_once __DIR__ . '/../../config.php';
 *   $pageTitle = 'Dashboard';
 *   $sidebar_role = 'fresher';
 *   $sidebar_active = 'Dashboard';
 *   startDashboardPage();  // captures output
 *   // ... HTML content ...
 *   endDashboardPage();  // renders the full page
 */

function startDashboardPage() {
    ob_start();
}

function endDashboardPage() {
    global $pageTitle, $sidebar_role, $sidebar_active;
    $content = ob_get_clean();
    $sidebar_items = getSidebarItems($sidebar_role);

    $user = getCurrentUser();
    $userName = $user['full_name'] ?? 'User';
    $avatar = $user['profile_picture'] ?? 'default.png';
    $avatarUrl = (file_exists(__DIR__ . '/../../frontend/assets/images/profile/' . $avatar) && $avatar !== 'default.png')
        ? BASE_URL . 'frontend/assets/images/profile/' . $avatar
        : 'https://ui-avatars.com/40/' . urlencode(substr($userName, 0, 2)) . '?background=3b82f6&color=fff';

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - SkillShare Hub'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>frontend/assets/css/dashboard.css">
</head>
<body>
<div class="bg-animated"></div>
<div class="toast-container" id="toastContainer"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="<?php echo BASE_URL; ?>frontend/assets/images/logo/skillshare hub.png" alt="SkillShare Hub Logo" style="height:36px; width:auto; object-fit:contain;">
        <h4>SkillShare <span>Hub</span></h4>
    </div>
    <nav class="sidebar-nav">
        <?php foreach ($sidebar_items as $group => $items): ?>
            <p class="menu-label"><?php echo $group; ?></p>
            <?php foreach ($items as $item): ?>
                <?php $isActive = ($sidebar_active === $item['label']); ?>
                <a href="<?php echo $item['link']; ?>" class="nav-item <?php echo $isActive ? 'active' : ''; ?>">
                    <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                    <?php echo $item['label']; ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
        <a href="<?php echo BASE_URL; ?>settings.php" class="nav-item"><i class="fas fa-cog"></i> Settings</a>
        <a href="<?php echo BASE_URL; ?>logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>

<div class="main-content">
    <header class="top-header">
        <div class="greeting">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='<?php echo BASE_URL; ?>index.php'" class="btn-back" title="Go Back">
                    <i class="fas fa-arrow-left"></i> <span>Back</span>
                </button>
                <div>
                    <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</h2>
                    <p>
                        <?php if ($sidebar_role === 'fresher'): ?>
                            Here's your learning dashboard
                        <?php elseif ($sidebar_role === 'mentor'): ?>
                            Here's your teaching dashboard
                        <?php else: ?>
                            Here's your admin dashboard
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <a href="<?php echo BASE_URL; ?>messages.php" style="color:var(--text-secondary);text-decoration:none;"><i class="fas fa-envelope" style="font-size:1.2rem;"></i></a>
            <a href="<?php echo BASE_URL; ?>notifications.php" style="color:var(--text-secondary);text-decoration:none;"><i class="fas fa-bell" style="font-size:1.2rem;"></i></a>
            <div class="profile" id="profileDropdown">
                <img src="<?php echo $avatarUrl; ?>" alt="Profile">
                <span><?php echo htmlspecialchars($userName); ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </header>
    <?php echo $content; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('menuToggle');
    var sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function() { sidebar.classList.toggle('open'); });
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }
});
function showToast(title, msg, type, duration) {
    var container = document.getElementById('toastContainer');
    if (!container) { container = document.createElement('div'); container.id = 'toastContainer'; container.className = 'toast-container'; document.body.appendChild(container); }
    var toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.innerHTML = '<div class="icon"><i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i></div><div class="content"><div class="title">' + title + '</div><div class="message">' + msg + '</div></div><button class="close">&times;</button>';
    container.appendChild(toast);
    setTimeout(function(){ toast.remove(); }, duration || 4000);
    toast.querySelector('.close').onclick = function(){ toast.remove(); };
}
function escapeHtml(text) {
    var div = document.createElement('div'); div.textContent = text; return div.innerHTML;
}
function apiFetch(url, options) {
    options = options || {};
    options.headers = Object.assign({'Content-Type': 'application/json'}, options.headers || {});
    return fetch(url, options).then(r => r.json());
}
</script>
<script src="<?php echo BASE_URL; ?>frontend/assets/js/dashboard.js"></script>
</body>
</html>
<?php
}
