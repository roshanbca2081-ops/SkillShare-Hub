<?php
// Profile Card - used in dashboards and user listings
// Accepts optional $profile (array: name, role, image, location, rating, joined, verified)
$profile = isset($profile) && is_array($profile) ? $profile : [
    'name' => 'John Doe',
    'role' => 'Fresher',
    'image' => 'frontend/assets/images/profile/avatar-1.svg',
    'location' => 'New York, USA',
    'rating' => 4.5,
    'joined' => '2023',
    'verified' => true,
];
?>
<div class="profile-card card-base">
    <div class="profile-card-top">
        <div class="profile-avatar-wrap">
            <img src="<?php echo $profile['image']; ?>" alt="<?php echo $profile['name']; ?>">
            <?php if ($profile['verified']): ?>
                <span class="profile-verified"><i class="fa-solid fa-circle-check"></i></span>
            <?php endif; ?>
        </div>
        <h5><?php echo $profile['name']; ?></h5>
        <p class="profile-role"><?php echo $profile['role']; ?></p>
        <p class="profile-location"><i class="fa-solid fa-location-dot"></i> <?php echo $profile['location']; ?></p>
    </div>
    <div class="profile-card-meta">
        <div class="pc-item">
            <i class="fa-solid fa-star" style="color:var(--warning);"></i>
            <span><?php echo $profile['rating']; ?></span>
        </div>
        <div class="pc-item">
            <i class="fa-solid fa-calendar-day" style="color:var(--primary);"></i>
            <span><?php echo $profile['joined']; ?></span>
        </div>
    </div>
    <div class="profile-card-actions">
        <a href="profile.php" class="btn btn-primary btn-sm btn-block">
            <i class="fa-solid fa-eye"></i> View Profile
        </a>
    </div>
</div>

<style>
    .profile-card { text-align: center; padding: var(--spacing-5); }
    .profile-card-top { }
    .profile-avatar-wrap {
        position: relative;
        width: 90px;
        height: 90px;
        margin: 0 auto var(--spacing-4);
    }
    .profile-avatar-wrap img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--primary-soft);
    }
    .profile-verified {
        position: absolute;
        bottom: 4px;
        right: 4px;
        color: var(--info);
        font-size: 1.2rem;
        background: #fff;
        border-radius: 50%;
    }
    .profile-card h5 { margin-bottom: 0.2rem; }
    .profile-role { color: var(--primary); font-weight: 500; font-size: var(--font-size-sm); margin-bottom: 0.3rem; }
    .profile-location { color: var(--gray-500); font-size: var(--font-size-sm); margin-bottom: 0; }
    .profile-card-meta { display: flex; justify-content: center; gap: var(--spacing-5); padding: var(--spacing-4) 0; margin: var(--spacing-4) 0; border-top: 1px solid var(--gray-200); border-bottom: 1px solid var(--gray-200); }
    .profile-card-meta .pc-item { display: flex; align-items: center; gap: 0.4rem; font-size: var(--font-size-sm); font-weight: 500; color: var(--gray-700); }
</style>
