<?php
session_start();
$page_title = 'Profile | SkillShare Hub';
$page_active = 'Profile';
include 'frontend/components/platform-header.php';
?>

<div class="container">
    <div class="academic-header reveal">
        <h1>My Profile</h1>
        <p>Manage your personal information and preferences</p>
    </div>

    <div class="profile-header reveal">
        <img src="frontend/assets/images/profile/avatar-1.svg" alt="Profile Avatar" class="profile-avatar">
        <div class="profile-info">
            <h2>Alex Johnson</h2>
            <div class="profile-role">Fresher Student</div>
            <div class="profile-bio">Passionate about learning new skills and becoming industry-ready through practical mentorship.</div>
            <div class="profile-meta">
                <span><i class="fa-solid fa-location-dot"></i> Education City</span>
                <span><i class="fa-solid fa-envelope"></i> alex@skillsharehub.com</span>
                <span><i class="fa-solid fa-calendar"></i> Joined Jan 2025</span>
            </div>
        </div>
        <div class="profile-actions">
            <button class="btn btn-edit" onclick="showToast('Info', 'Edit profile coming soon!', 'info')"><i class="fa-solid fa-pen"></i> Edit Profile</button>
            <button class="btn btn-outline" onclick="showToast('Info', 'Settings coming soon!', 'info')"><i class="fa-solid fa-gear"></i> Settings</button>
        </div>
    </div>

    <div class="section-title reveal">
        <h2>My Stats</h2>
        <div class="divider"></div>
    </div>
    <div class="dashboard-stats reveal">
        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-number">12</div>
                    <div class="stat-label">Courses Enrolled</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-number">8</div>
                    <div class="stat-label">Mentorship Sessions</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-video"></i></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-number">4</div>
                    <div class="stat-label">Certificates</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-certificate"></i></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-top">
                <div>
                    <div class="stat-number">15</div>
                    <div class="stat-label">Skills Gained</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
            </div>
        </div>
    </div>
</div>

<?php include 'frontend/components/platform-footer.php'; ?>
