<?php
require_once __DIR__ . '/../config.php';

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$userId = getUserId();
$role = getUserRole();
$user = getCurrentUser();
$userName = $user['full_name'] ?? 'User';
$avatar = $user['profile_picture'] ?? 'default.png';
$avatarUrl = BASE_URL . 'frontend/assets/images/profile/' . $avatar;
if ($avatar === 'default.png' || !file_exists(__DIR__ . '/../frontend/assets/images/profile/' . $avatar)) {
    $avatarUrl = 'https://ui-avatars.com/40/' . urlencode(substr($userName, 0, 2)) . '?background=3b82f6&color=fff';
}
?>
<header class="top-header">
    <div class="greeting">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='<?php echo BASE_URL; ?>index.php'" class="btn-back" title="Go Back">
                <i class="fas fa-arrow-left"></i> <span>Back</span>
            </button>
            <div>
                <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>! 👋</h2>
                <p>
                    <?php if ($role === 'fresher'): ?>
                        Here's what's happening with your learning journey
                    <?php elseif ($role === 'mentor'): ?>
                        Here's what's happening with your teaching journey
                    <?php elseif ($role === 'admin'): ?>
                        Here's what's happening on the platform
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    <div class="header-actions">
        <div class="search-box" id="dashboardSearch" style="display:none;">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search..." id="searchInput">
        </div>
        <a href="<?php echo BASE_URL . 'messages.php'; ?>" style="color:var(--text-secondary);text-decoration:none;">
            <i class="fas fa-envelope" style="font-size:1.2rem;"></i>
        </a>
        <a href="<?php echo BASE_URL . 'notifications.php'; ?>" style="color:var(--text-secondary);text-decoration:none;">
            <i class="fas fa-bell" style="font-size:1.2rem;"></i>
        </a>
        <div class="profile" id="profileDropdown">
            <img src="<?php echo $avatarUrl; ?>" alt="Profile">
            <span><?php echo htmlspecialchars($userName); ?></span>
            <i class="fas fa-chevron-down" style="font-size:0.7rem;color:var(--text-muted);margin-left:4px;"></i>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('menuToggle');
    var sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
    }
});
</script>
