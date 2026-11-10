<?php
$page_title = 'Reports';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../config/auth.php';
requireAdmin();

$reportCards = [
    ['title' => 'Users', 'description' => 'Account totals by role and access status.', 'href' => 'users.php', 'icon' => 'fa-users'],
    ['title' => 'Courses', 'description' => 'Course catalog status and instructor activity.', 'href' => 'courses.php', 'icon' => 'fa-book-open'],
    ['title' => 'Bookings', 'description' => 'Mentoring bookings summarized by status.', 'href' => 'bookings.php', 'icon' => 'fa-calendar-check'],
    ['title' => 'Payments', 'description' => 'Recorded transactions and payment totals.', 'href' => 'payment.php', 'icon' => 'fa-credit-card'],
];
?>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="admin-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-topbar"><div><h1 class="admin-title">Reports</h1><p class="admin-subtitle">Operational summaries generated from current platform records.</p></div></div>
        <div class="admin-kpis">
            <?php foreach ($reportCards as $card): ?><a class="admin-card text-decoration-none" href="<?php echo htmlspecialchars($card['href'], ENT_QUOTES, 'UTF-8'); ?>"><span class="d-inline-flex align-items-center gap-2 text-primary"><i class="fas <?php echo htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i><strong><?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></strong></span><p class="text-muted mt-2 mb-0"><?php echo htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8'); ?></p><span class="d-inline-block mt-3">Open report <i class="fas fa-arrow-right" aria-hidden="true"></i></span></a><?php endforeach; ?>
        </div>
        <div class="alert alert-info">Reports reflect recorded database data. Payment totals are not independently verified with a payment provider.</div>
    </main>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>