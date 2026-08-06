<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Notifications';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | Admin - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/notification.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include '../../components/loader.php'; ?>
    <?php include '../../components/sidebar.php'; ?>

    <div class="dashboard-main">
        <?php include __DIR__ . '/_topbar.php'; ?>

        <div class="dash-grid-3" style="margin-bottom:var(--spacing-5);">
            <div class="stat-card reveal"><div class="stat-icon primary"><i class="fa-solid fa-bell"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="24">0</h4><p>Total Notifications</p></div></div>
            <div class="stat-card reveal reveal-delay-1"><div class="stat-icon secondary"><i class="fa-solid fa-envelope-open"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="18">0</h4><p>Read</p></div></div>
            <div class="stat-card reveal reveal-delay-2"><div class="stat-icon accent"><i class="fa-solid fa-envelope"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="6">0</h4><p>Unread</p></div></div>
        </div>

        <div class="panel reveal">
            <div class="panel-header">
                <h5><i class="fa-solid fa-bell" style="color:var(--primary);"></i> Notification Center</h5>
                <div style="display:flex;gap:0.5rem;">
                    <button class="btn btn-outline btn-sm"><i class="fa-solid fa-check-double"></i> Mark All Read</button>
                    <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Clear All</button>
                </div>
            </div>
            <div class="notifications-list">
                <?php
                $notifs = [
                    ['fas fa-user-plus', 'New user registered', 'Sarah Johnson just created an account.', '2m ago', true, 'var(--primary-soft)', 'var(--primary)'],
                    ['fas fa-cart-shopping', 'New course purchase', 'Advanced Data Science was purchased.', '18m ago', true, 'var(--secondary-soft)', 'var(--secondary-dark)'],
                    ['fas fa-calendar-check', 'Session booked', 'A mentor session was scheduled.', '1h ago', false, 'var(--accent-soft)', 'var(--accent)'],
                    ['fas fa-triangle-exclamation', 'Payment issue', 'Payment TXN-2579 is pending.', '3h ago', false, '#fff3d6', '#d1910a'],
                    ['fas fa-user-plus', 'New mentor applied', 'Dr. Emily Chen applied as a mentor.', '5h ago', false, 'var(--primary-soft)', 'var(--primary)'],
                    ['fas fa-star', 'New review received', '5-star review on Data Science course.', '1d ago', true, 'var(--secondary-soft)', 'var(--secondary-dark)'],
                ];
                foreach ($notifs as $n): ?>
                    <div class="notif-item <?php echo $n[4] ? '' : 'unread'; ?>" style="display:flex;align-items:flex-start;gap:var(--spacing-4);padding:var(--spacing-4) var(--spacing-5);border-bottom:1px solid var(--gray-100);">
                        <div class="notif-icon" style="width:44px;height:44px;border-radius:50%;background:<?php echo $n[5]; ?>;color:<?php echo $n[6]; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fa-solid <?php echo $n[0]; ?>"></i></div>
                        <div style="flex:1;">
                            <h6 style="margin:0;font-size:.9rem;"><?php echo $n[1]; ?> <?php if (!$n[4]) echo '<span class="badge badge-primary" style="margin-left:.3rem;">New</span>'; ?></h6>
                            <p style="margin:0;font-size:.8rem;color:var(--gray-500);"><?php echo $n[2]; ?></p>
                        </div>
                        <span style="font-size:.75rem;color:var(--gray-400);white-space:nowrap;"><?php echo $n[3]; ?></span>
                        <button class="delete-btn" aria-label="Delete" style="width:30px;height:30px;border-radius:var(--border-radius-sm);border:none;background:#ffe4dd;color:var(--danger);cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/navbar.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/animation.js"></script>
</body>

</html>
