<?php
session_start();
$sidebar_role = 'mentor';
$sidebar_active = 'Messages';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Mentor - SkillShare Hub</title>
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

        <div class="messages-layout panel reveal">
            <div class="msg-sidebar">
                <div class="msg-search"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search conversations..."></div>
                <div class="conversation-list">
                    <?php
                    $chats = [
                        ['Sarah Johnson', 'When is the next session?', '2m', true],
                        ['Michael Chen', 'Thanks for the feedback!', '18m', false],
                        ['Emily Davis', 'Can you review my assignment?', '1h', true],
                        ['James Wilson', 'I have a question about...', '3h', false],
                        ['Olivia Martinez', 'Great session today!', '1d', false],
                    ];
                    foreach ($chats as $c): ?>
                        <div class="conversation-item <?php echo $c[3] ? 'unread' : ''; ?>" style="display:flex;align-items:center;gap:0.8rem;padding:0.9rem var(--spacing-4);cursor:pointer;border-bottom:1px solid var(--gray-100);transition:var(--transition-fast);">
                            <img src="../../assets/images/profile/avatar-1.svg" alt="" style="width:44px;height:44px;border-radius:50%;flex-shrink:0;">
                            <div style="flex:1;min-width:0;">
                                <strong style="font-size:.85rem;display:block;"><?php echo $c[0]; ?></strong>
                                <p style="margin:0;font-size:.78rem;color:var(--gray-500);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo $c[1]; ?></p>
                            </div>
                            <span style="font-size:.7rem;color:var(--gray-400);"><?php echo $c[2]; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="message-thread">
                <div class="thread-header" style="display:flex;align-items:center;gap:0.8rem;padding:var(--spacing-4) var(--spacing-5);border-bottom:1px solid var(--gray-100);">
                    <img src="../../assets/images/profile/avatar-1.svg" alt="" style="width:42px;height:42px;border-radius:50%;">
                    <div><strong style="font-size:.9rem;">Sarah Johnson</strong><p style="margin:0;font-size:.75rem;color:var(--gray-500);">Online now</p></div>
                </div>
                <div class="message-history" style="padding:var(--spacing-5);min-height:350px;display:flex;flex-direction:column;gap:var(--spacing-4);background:var(--gray-100);">
                    <div class="msg-bubble received" style="align-self:flex-start;max-width:70%;background:#fff;padding:0.7rem 1.1rem;border-radius:var(--border-radius-lg);border-bottom-left-radius:4px;font-size:.85rem;box-shadow:var(--shadow-sm);">Hello! When is the next data science session?</div>
                    <div class="msg-bubble received" style="align-self:flex-start;max-width:70%;background:#fff;padding:0.7rem 1.1rem;border-radius:var(--border-radius-lg);border-bottom-left-radius:4px;font-size:.85rem;box-shadow:var(--shadow-sm);">I'd like to prepare some questions.</div>
                    <div class="msg-bubble sent" style="align-self:flex-end;max-width:70%;background:var(--gradient-primary);color:#fff;padding:0.7rem 1.1rem;border-radius:var(--border-radius-lg);border-bottom-right-radius:4px;font-size:.85rem;box-shadow:var(--shadow-sm);">Sure! The next session is on Jan 14 at 10 AM.</div>
                    <div class="msg-bubble sent" style="align-self:flex-end;max-width:70%;background:var(--gradient-primary);color:#fff;padding:0.7rem 1.1rem;border-radius:var(--border-radius-lg);border-bottom-right-radius:4px;font-size:.85rem;box-shadow:var(--shadow-sm);">Feel free to bring any questions on Python or statistics. 🚀</div>
                </div>
                <div class="message-input" style="display:flex;align-items:center;gap:0.6rem;padding:var(--spacing-4);border-top:1px solid var(--gray-100);">
                    <button class="dt-icon-btn" aria-label="Attach"><i class="fa-solid fa-paperclip"></i></button>
                    <input type="text" class="form-control" placeholder="Type a message..." style="border-radius:var(--border-radius-full);">
                    <button class="btn btn-primary btn-sm" style="border-radius:50%;width:42px;height:42px;padding:0;"><i class="fa-solid fa-paper-plane"></i></button>
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
