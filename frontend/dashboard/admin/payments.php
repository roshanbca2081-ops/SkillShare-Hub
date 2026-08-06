<?php
session_start();
$sidebar_role = 'admin';
$sidebar_active = 'Payments';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | Admin - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/payment.css">
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

        <div class="dash-grid-4" style="margin-bottom:var(--spacing-5);">
            <div class="stat-card reveal"><div class="stat-icon primary"><i class="fa-solid fa-circle-dollar"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="45000">0</h4><p>Total Revenue</p></div></div>
            <div class="stat-card reveal reveal-delay-1"><div class="stat-icon secondary"><i class="fa-solid fa-receipt"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="580">0</h4><p>Transactions</p></div></div>
            <div class="stat-card reveal reveal-delay-2"><div class="stat-icon accent"><i class="fa-solid fa-circle-check"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="520">0</h4><p>Successful</p></div></div>
            <div class="stat-card reveal reveal-delay-3"><div class="stat-icon warning-bg"><i class="fa-solid fa-clock"></i></div><div class="stat-info"><h4 class="dash-counter" data-target="60">0</h4><p>Pending</p></div></div>
        </div>

        <div class="panel reveal">
            <div class="panel-header">
                <h5><i class="fa-solid fa-credit-card" style="color:var(--primary);"></i> Transactions</h5>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-file-export"></i> Export</button>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Transaction ID</th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $payments = [
                            ['TXN-2581', 'Sarah Johnson', 'Data Science Fundamentals', '$49.99', 'Card', 'Completed'],
                            ['TXN-2580', 'Michael Chen', 'Machine Learning Mastery', '$79.99', 'PayPal', 'Completed'],
                            ['TXN-2579', 'Emily Davis', 'Full-Stack Web Development', '$59.99', 'Card', 'Pending'],
                            ['TXN-2578', 'James Wilson', 'Human Anatomy Basics', '$39.99', 'Bank', 'Completed'],
                            ['TXN-2577', 'Olivia Martinez', 'Business Strategy 101', '$29.99', 'Card', 'Refunded'],
                            ['TXN-2576', 'Daniel Lee', 'UI/UX Design Principles', '$44.99', 'PayPal', 'Completed'],
                        ];
                        foreach ($payments as $p): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><strong><?php echo $p[0]; ?></strong></td>
                                <td><div class="user-cell"><img src="../../assets/images/profile/avatar-1.svg" alt=""><strong><?php echo $p[1]; ?></strong></div></td>
                                <td><?php echo $p[2]; ?></td>
                                <td><strong><?php echo $p[3]; ?></strong></td>
                                <td><span class="badge badge-primary"><?php echo $p[4]; ?></span></td>
                                <td><span class="status-pill <?php echo strtolower($p[5]) === 'completed' ? 'approved' : (strtolower($p[5]) === 'pending' ? 'pending' : 'rejected'); ?>"><?php echo $p[5]; ?></span></td>
                                <td>
                                    <div class="table-actions">
                                        <button class="view-btn" aria-label="View"><i class="fa-solid fa-eye"></i></button>
                                        <button class="edit-btn" aria-label="Edit"><i class="fa-solid fa-file-invoice"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
