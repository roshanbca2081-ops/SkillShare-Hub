<?php
// Page Preloader
?>
<div class="preloader" id="preloader">
    <div class="loader-content">
        <div class="loader-logo">
            <img src="frontend/assets/images/logo/skillshare hub.png" alt="SkillShare Hub Logo" style="width:100%; height:auto; object-fit:contain; padding:10px;">
        </div>
        <div class="loader-spinner"></div>
        <p>SkillShare Hub</p>
    </div>
</div>

<style>
    .preloader {
        position: fixed;
        inset: 0;
        background: var(--gray-100);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    .preloader.hidden {
        opacity: 0;
        visibility: hidden;
    }

    .loader-content { text-align: center; }

    .loader-logo {
        width: 80px;
        height: 80px;
        margin: 0 auto var(--spacing-4);
        border-radius: var(--border-radius-lg);
        background: var(--gradient-primary);
        color: #fff;
        font-size: 2.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-primary);
        animation: pulse 1.5s infinite;
    }

    .loader-spinner {
        width: 40px;
        height: 40px;
        margin: 0 auto var(--spacing-4);
        border: 4px solid var(--primary-soft);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    .loader-content p {
        font-weight: 600;
        color: var(--gray-700);
        letter-spacing: 1px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
