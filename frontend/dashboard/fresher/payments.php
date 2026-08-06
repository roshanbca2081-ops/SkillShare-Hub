<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Payments';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | Fresher - SkillShare Hub</title>
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

        <div class="dash-grid-2" style="margin-bottom:var(--spacing-5);align-items:stretch;">
            <div class="stat-card reveal">
                <div class="stat-icon primary"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-info"><h4>$480.00</h4><p>Total Spent</p></div>
            </div>
            <div class="stat-card reveal reveal-delay-1">
                <div class="stat-icon secondary"><i class="fa-solid fa-credit-card"></i></div>
                <div class="stat-info"><h4>$125.00</h4><p>Pending Invoices</p></div>
            </div>
        </div>

        <div class="panel reveal">
            <div class="panel-header"><h5><i class="fa-solid fa-receipt" style="color:var(--primary);"></i> Payment History</h5>
                <button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Add Payment Method</button>
            </div>
            <div class="table-responsive-wrap">
                <table class="data-table dash-table">
                    <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Invoice #</th><th>Description</th><th>Mentor</th><th>Amount</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $fresher_payments = [
                            ['INV-2025-045', 'Data Science Mentorship', 'Dr. Aisha Khan', '$50.00', 'Jan 10', 'Paid'],
                            ['INV-2025-044', 'Machine Learning Course', 'Prof. James Carter', '$79.99', 'Jan 08', 'Paid'],
                            ['INV-2025-043', 'Python Basics Course', 'Prof. James Carter', '$29.99', 'Jan 05', 'Paid'],
                            ['INV-2025-042', 'Mock Interview Session', 'Prof. James Carter', '$45.00', 'Jan 03', 'Pending'],
                            ['INV-2025-041', 'Career Guidance', 'Dr. Emily Chen', '$35.00', 'Dec 28', 'Paid'],
                        ];
                        foreach ($fresher_payments as $p): ?>
                            <tr>
                                <td><input type="checkbox" class="row-check"></td>
                                <td><strong><?php echo $p[0]; ?></strong></td>
                                <td><?php echo $p[1]; ?></td>
                                <td><div class="user-cell"><img src="../../assets/images/profile/avatar-2.svg" alt=""><strong><?php echo $p[2]; ?></strong></div></td>
                                <td style="color:var(--primary);font-weight:600;"><?php echo $p[3]; ?></td>
                                <td><?php echo $p[4]; ?></td>
                                <td><span class="status-pill <?php echo strtolower($p[5]) === 'paid' ? 'approved' : 'pending'; ?>"><?php echo $p[5]; ?></span></td>
                                <td><div class="table-actions"><button class="view-btn" aria-label="Receipt"><i class="fa-solid fa-file-invoice"></i></button><button class="edit-btn" aria-label="Download"><i class="fa-solid fa-download"></i></button></div></td>
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
</content>

