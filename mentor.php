<?php
session_start();
$page_title = 'Mentors | SkillShare Hub';
$page_active = 'Mentors';
include 'frontend/components/platform-header.php';
?>

<div class="container">
    <div class="academic-header reveal">
        <h1>Our Top Mentors</h1>
        <p>Learn directly from experienced graduates and industry experts</p>
    </div>

    <div class="mentors-grid">
        <div class="mentor-card reveal">
            <div class="m-avatar blue">JD</div>
            <div class="m-name">John Doe</div>
            <div class="m-title">Software Engineer at Google</div>
            <div class="m-rating">★★★★★ <span>(156 reviews)</span></div>
            <div class="m-rate">$45 <span>/ hour</span></div>
            <button class="m-btn" onclick="showToast('Success', 'Viewing John Doe\'s profile...', 'success')">View Profile</button>
        </div>
        <div class="mentor-card reveal">
            <div class="m-avatar purple">SM</div>
            <div class="m-name">Sarah Miller</div>
            <div class="m-title">Data Scientist at Amazon</div>
            <div class="m-rating">★★★★★ <span>(128 reviews)</span></div>
            <div class="m-rate">$50 <span>/ hour</span></div>
            <button class="m-btn" onclick="showToast('Success', 'Viewing Sarah Miller\'s profile...', 'success')">View Profile</button>
        </div>
        <div class="mentor-card reveal">
            <div class="m-avatar green">AK</div>
            <div class="m-name">Alex Kumar</div>
            <div class="m-title">Full Stack Developer at Microsoft</div>
            <div class="m-rating">★★★★★ <span>(142 reviews)</span></div>
            <div class="m-rate">$40 <span>/ hour</span></div>
            <button class="m-btn" onclick="showToast('Success', 'Viewing Alex Kumar\'s profile...', 'success')">View Profile</button>
        </div>
        <div class="mentor-card reveal">
            <div class="m-avatar" style="background:#ec4899;">EM</div>
            <div class="m-name">Emma Watson</div>
            <div class="m-title">UX/UI Designer at Figma</div>
            <div class="m-rating">★★★★☆ <span>(98 reviews)</span></div>
            <div class="m-rate">$35 <span>/ hour</span></div>
            <button class="m-btn" onclick="showToast('Success', 'Viewing Emma Watson\'s profile...', 'success')">View Profile</button>
        </div>
        <div class="mentor-card reveal">
            <div class="m-avatar" style="background:#f59e0b;">DR</div>
            <div class="m-name">David Roberts</div>
            <div class="m-title">Cybersecurity Expert at Cisco</div>
            <div class="m-rating">★★★★★ <span>(110 reviews)</span></div>
            <div class="m-rate">$48 <span>/ hour</span></div>
            <button class="m-btn" onclick="showToast('Success', 'Viewing David Roberts\' profile...', 'success')">View Profile</button>
        </div>
        <div class="mentor-card reveal">
            <div class="m-avatar" style="background:#14b8a6;">LN</div>
            <div class="m-name">Lisa Nguyen</div>
            <div class="m-title">Marketing Director at HubSpot</div>
            <div class="m-rating">★★★★★ <span>(134 reviews)</span></div>
            <div class="m-rate">$38 <span>/ hour</span></div>
            <button class="m-btn" onclick="showToast('Success', 'Viewing Lisa Nguyen\'s profile...', 'success')">View Profile</button>
        </div>
    </div>
</div>

<?php include 'frontend/components/platform-footer.php'; ?>
