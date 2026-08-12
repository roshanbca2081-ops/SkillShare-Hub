<?php
session_start();
$sidebar_role = 'mentor';
$sidebar_active = 'Profile';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Mentor - SkillShare Hub</title>
    <link rel="stylesheet" href="../../assets/css/varables.css">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/navbar.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
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

        <div class="dash-grid-2" style="align-items:flex-start;">
            <div class="panel reveal">
                <div class="panel-header"><h5><i class="fa-solid fa-user-tie" style="color:var(--primary);"></i> Mentor Profile</h5></div>
                <div class="panel-body" style="text-align:center;">
                    <img src="../../assets/images/profile/avatar-2.svg" alt="Profile" style="width:120px;height:120px;border-radius:50%;margin:0 auto var(--spacing-4);border:4px solid var(--secondary-soft);">
                    <h5>Dr. Aisha Khan</h5>
                    <p style="color:var(--gray-500);font-size:.85rem;">Sr. Data Science Mentor</p>
                    <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;margin-top:0.5rem;">
                        <span class="badge badge-primary"><i class="fa-solid fa-star"></i> 4.9</span>
                        <span class="badge badge-secondary">240 Students</span>
                        <span class="badge badge-accent">12 Courses</span>
                    </div>
                    <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;margin-top:var(--spacing-5);">
                        <button class="btn btn-primary btn-sm"><i class="fa-solid fa-camera"></i> Change Photo</button>
                        <button class="btn btn-outline btn-sm"><i class="fa-solid fa-eye"></i> View Public</button>
                    </div>
                </div>
            </div>
            <div class="panel reveal reveal-delay-1">
                <div class="panel-header"><h5><i class="fa-solid fa-id-card" style="color:var(--primary);"></i> Professional Information</h5></div>
                <div class="panel-body">
                    <form>
                        <div class="dash-form-grid">
                            <div class="form-group"><label class="form-label">Full Name</label><input type="text" class="form-control" value="Dr. Aisha Khan"></div>
                            <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" value="aisha.khan@skillsharehub.com"></div>
                            <div class="form-group"><label class="form-label">Expertise</label><input type="text" class="form-control" value="Data Science, ML, Python"></div>
                            <div class="form-group"><label class="form-label">Hourly Rate</label><input type="text" class="form-control" value="$50/hour"></div>
                            <div class="form-group" style="grid-column:1/-1;"><label class="form-label">Bio</label><textarea class="form-control" rows="4">Experienced data scientist with 10+ years in industry and academia, passionate about mentoring the next generation of data professionals.</textarea></div>
                        </div>
                        <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/navbar.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/profile.js"></script>
    <script src="../../assets/js/animation.js"></script>
</body>

</html>
