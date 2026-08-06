<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | SkillShare Hub</title>

    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/main.css">
    <link rel="stylesheet" href="frontend/assets/css/login.css">
    <link rel="stylesheet" href="frontend/assets/css/responsive.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="auth-body">

    <?php include 'frontend/components/loader.php'; ?>

    <div class="auth-wrap">
        <div class="auth-card center-card" style="text-align:center;">
            <div class="error-404">
                <div class="error-code">4<span class="text-gradient">0</span>4</div>
                <div class="error-icon">
                    <i class="fa-solid fa-face-frown-open"></i>
                </div>
                <h2>Oops! Page Not Found</h2>
                <p>The page you're looking for might have been moved, deleted, or never existed in the first place.</p>

                <div class="error-actions" style="display:flex;gap:var(--spacing-4);justify-content:center;flex-wrap:wrap;margin-top:var(--spacing-5);">
                    <a href="index.php" class="btn btn-primary btn-lg"><i class="fa-solid fa-house"></i> Back to Home</a>
                    <a href="courses.php" class="btn btn-outline btn-lg"><i class="fa-solid fa-book-open"></i> Browse Courses</a>
                </div>

                <div style="margin-top:var(--spacing-6);">
                    <a href="contact.php"><i class="fa-solid fa-headset"></i> Report a broken link</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/assets/js/main.js"></script>

</body>

</html>
