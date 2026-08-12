<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Messages';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Fresher - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/message.css">
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

        <div class="panel reveal">
            <div class="panel-header"><h5><i class="fa-solid fa-comments" style="color:var(--primary);"></i> Messages</h5></div>
            <div class="chat-layout">
                <div class="chat-sidebar">
                    <div class="dt-search" style="margin:var(--spacing-4);"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search chats..."></div>
                    <div class="chat-list">
                        <div class="chat-item active">
                            <img src="../../assets/images/profile/avatar-2.svg" alt=""><div class="chat-info"><h6>Dr. Aisha Khan</h6><p>Sure, let's schedule...</p><span class="chat-time">Now</span></div>
                        </div>
                        <div class="chat-item">
                            <img src="../../assets/images/profile/avatar-3.svg" alt=""><div class="chat-info"><h6>Prof. James Carter</h6><p>Please review the...</p><span class="chat-time">5m</span></div>
                        </div>
                        <div class="chat-item">
                            <img src="../../assets/images/profile/avatar-4.svg" alt=""><div class="chat-info"><h6>Robert Garcia</h6><p>Great work on...</p><span class="chat-time">1h</span></div>
                        </div>
                        <div class="chat-item">
                            <img src="../../assets/images/profile/avatar-5.svg" alt=""><div class="chat-info"><h6>Dr. Emily Chen</h6><p>Your mock interview...</p><span class="chat-time">3h</span></div>
                        </div>
                    </div>
                </div>
                <div class="chat-main">
                    <div class="chat-header">
                        <div style="display:flex;align-items:center;gap:0.75rem;"><img src="../../assets/images/profile/avatar-2.svg" alt="" style="width:44px;height:44px;border-radius:50%;"><div><h6 style="margin:0;">Dr. Aisha Khan</h6><small style="color:var(--success);">Online</small></div></div>
                        <div style="display:flex;gap:0.5rem;"><button class="dt-icon-btn"><i class="fa-solid fa-phone"></i></button><button class="dt-icon-btn"><i class="fa-solid fa-video"></i></button></div>
                    </div>
                    <div class="chat-body">
                        <div class="msg received">Hi! I have a question about the Machine Learning module.</div>
                        <div class="msg sent">Sure Aisha! Let's review it together.</div>
                        <div class="msg received">Have you tried the neural network assignment I sent?</div>
                        <div class="msg sent">Yes, just submitted it. Can you check?</div>
                    </div>
                    <div class="chat-input"><input type="text" placeholder="Type a message..."><button class="btn btn-primary btn-icon"><i class="fa-solid fa-paper-plane"></i></button></div>
                </div>
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
