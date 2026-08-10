<?php
session_start();
$navbar_active = 'Home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareSkill Hub - Learn • Connect • Grow</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Site CSS -->
    <link rel="stylesheet" href="frontend/assets/css/varables.css">
    <link rel="stylesheet" href="frontend/assets/css/navbar.css">

    <style>
        /* ============================================
           CSS VARIABLES
           ============================================ */
        :root {
            --primary-400: #60a5fa;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --secondary-400: #a78bfa;
            --secondary-500: #8b5cf6;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.8);
            --text-muted: rgba(255,255,255,0.4);
            --glass-bg: rgba(255,255,255,0.05);
            --glass-border: rgba(255,255,255,0.1);
            --shadow-lg: 0 8px 40px rgba(0,0,0,0.4);
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-2xl: 24px;
            --radius-full: 50px;
            --transition-bounce: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --gradient-primary: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --gradient-hero: linear-gradient(135deg, #ffffff 0%, #60a5fa 50%, #a78bfa 100%);
            --font-heading: 'Poppins', sans-serif;
            --font-primary: 'Inter', sans-serif;
            --sidebar-width: 260px;
        }

        /* ============================================
           RESET & BASE
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-primary);
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1a1a2e 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
            line-height: 1.6;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: var(--gradient-primary); border-radius: 10px; }

        /* ============================================
           ANIMATED BACKGROUND
           ============================================ */
        .bg-animated {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .bg-animated::before {
            content: '';
            position: absolute;
            inset: -50%;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.10) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.10) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(6,182,212,0.05) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite alternate;
        }
        @keyframes bgShift {
            0% { transform: translate(0,0) scale(1); }
            50% { transform: translate(5%,-5%) scale(1.05); }
            100% { transform: translate(-5%,5%) scale(0.95); }
        }

        .floating-logo {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 15rem;
            font-weight: 900;
            font-family: var(--font-heading);
            color: rgba(255,255,255,0.015);
            pointer-events: none;
            z-index: 0;
            letter-spacing: 10px;
            animation: floatLogo 25s ease-in-out infinite;
            user-select: none;
            white-space: nowrap;
        }
        @keyframes floatLogo {
            0%,100% { transform: translate(-50%,-50%) scale(1) rotate(0deg); }
            25% { transform: translate(-50%,-55%) scale(1.02) rotate(1deg); }
            75% { transform: translate(-50%,-45%) scale(0.98) rotate(-1deg); }
        }

        /* Floating Icons */
        .floating-icons {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .floating-icons .icon {
            position: absolute;
            opacity: 0.04;
            font-size: 2.5rem;
            animation: floatIcon 25s ease-in-out infinite;
            transition: all 0.3s ease;
        }
        .floating-icons .icon .label {
            display: block;
            font-size: 0.5rem;
            text-align: center;
            opacity: 0.5;
            margin-top: 2px;
        }
        @keyframes floatIcon {
            0%,100% { transform: translate(0,0) rotate(0deg); }
            25% { transform: translate(30px,-20px) rotate(5deg); }
            50% { transform: translate(-15px,30px) rotate(-3deg); }
            75% { transform: translate(20px,-10px) rotate(8deg); }
        }

        /* ============================================
           MAIN LAYOUT (with vertical sidebar)
           ============================================ */
        .with-v-nav {
            margin-left: var(--sidebar-width);
            position: relative;
            z-index: 1;
        }

        /* Mobile nav toggle */
        .mobile-nav-toggle-container {
            display: none;
            position: fixed;
            top: 14px;
            left: 14px;
            z-index: 1060;
        }
        .mobile-nav-toggle {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--gradient-primary);
            color: #fff;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-lg);
        }

        /* ============================================
           HERO SECTION
           ============================================ */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 100px 0 60px;
        }
        .hero .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(59,130,246,0.2);
            border-radius: var(--radius-full);
            padding: 6px 20px;
            color: var(--primary-400);
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }
        .hero-title {
            font-family: var(--font-heading);
            font-weight: 900;
            font-size: 3.8rem;
            line-height: 1.1;
            background: var(--gradient-hero);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }
        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-secondary);
            max-width: 600px;
            font-weight: 300;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }
        .btn-hero {
            padding: 14px 36px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
        }
        .btn-hero-primary {
            background: var(--gradient-primary);
            color: #fff;
            border: none;
            box-shadow: 0 10px 30px rgba(59,130,246,0.3);
        }
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(59,130,246,0.4);
            color: #fff;
        }
        .btn-hero-outline {
            border: 2px solid rgba(255,255,255,0.2);
            color: var(--text-secondary);
            background: transparent;
            backdrop-filter: blur(10px);
        }
        .btn-hero-outline:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.4);
            color: var(--text-primary);
        }

        .hero-stats {
            display: flex;
            gap: 40px;
        }
        .hero-stats .stat-item { text-align: center; }
        .hero-stats .stat-number {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 2.2rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-stats .stat-label {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: 2px;
        }

        .hero-illustration {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-2xl);
            padding: 40px;
            text-align: center;
        }
        .hero-illustration .illustration-icon {
            font-size: 8rem;
            color: var(--primary-400);
            opacity: 0.6;
            margin-bottom: 20px;
        }
        .hero-illustration .illustration-features {
            display: flex;
            justify-content: space-around;
            gap: 16px;
        }
        .hero-illustration .illustration-features .feature {
            text-align: center;
            animation: float 3s ease-in-out infinite;
        }
        .hero-illustration .illustration-features .feature:nth-child(2) { animation-delay: 0.5s; }
        .hero-illustration .illustration-features .feature:nth-child(3) { animation-delay: 1s; }
        .hero-illustration .illustration-features .feature .f-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-md);
            background: rgba(59,130,246,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 1.5rem;
            color: var(--primary-400);
        }
        .hero-illustration .illustration-features .feature span {
            color: var(--text-muted);
            font-size: 0.75rem;
        }
        @keyframes float {
            0%,100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* ============================================
           SECTIONS COMMON
           ============================================ */
        .section { padding: 80px 0; position: relative; z-index: 1; }
        .section .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .section-header { text-align: center; margin-bottom: 50px; }
        .section-header h2 {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 2.5rem;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        .section-header p {
            color: var(--text-secondary);
            font-size: 1.05rem;
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
        }
        .section-header .divider {
            width: 60px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: var(--radius-full);
            margin: 12px auto 0;
        }

        /* ============================================
           FEATURES
           ============================================ */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
        }
        .feature-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: 30px 24px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .feature-card:hover {
            transform: translateY(-8px);
            background: rgba(255,255,255,0.06);
            border-color: var(--primary-400);
            box-shadow: var(--shadow-lg);
        }
        .feature-card .f-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(59,130,246,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.8rem;
            color: var(--primary-400);
            transition: all 0.3s ease;
        }
        .feature-card:hover .f-icon {
            background: var(--gradient-primary);
            color: #fff;
            transform: scale(1.1) rotate(-5deg);
        }
        .feature-card h5 {
            font-family: var(--font-heading);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }
        .feature-card p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0;
        }

        /* ============================================
           FIELDS
           ============================================ */
        .fields-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
        }
        .field-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 20px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
        }
        .field-card:hover {
            transform: translateY(-6px) scale(1.02);
            background: rgba(255,255,255,0.06);
            border-color: var(--primary-400);
            box-shadow: 0 10px 40px rgba(59,130,246,0.15);
        }
        .field-card .f-icon {
            font-size: 2.5rem;
            color: var(--text-muted);
            transition: all 0.3s ease;
        }
        .field-card:hover .f-icon {
            color: var(--primary-400);
            transform: scale(1.1) rotate(-5deg);
        }
        .field-card .f-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.9rem;
            margin-top: 6px;
        }
        .field-card .f-count { color: var(--text-muted); font-size: 0.7rem; }

        /* ============================================
           HOW IT WORKS
           ============================================ */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
        }
        .step-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: 30px 24px;
            text-align: center;
            position: relative;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
        }
        .step-card:hover {
            transform: translateY(-8px);
            background: rgba(255,255,255,0.06);
            border-color: var(--primary-400);
            box-shadow: var(--shadow-lg);
        }
        .step-card .step-num {
            position: absolute;
            top: 10px;
            right: 16px;
            font-family: var(--font-heading);
            font-weight: 900;
            font-size: 3rem;
            color: rgba(255,255,255,0.06);
        }
        .step-card .step-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 1.5rem;
            color: #fff;
            box-shadow: 0 8px 24px rgba(59,130,246,0.3);
        }
        .step-card h5 {
            font-family: var(--font-heading);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }
        .step-card p { color: var(--text-secondary); font-size: 0.9rem; margin: 0; }

        /* ============================================
           MENTORS
           ============================================ */
        .mentors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .mentor-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: 24px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .mentor-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.06);
            border-color: var(--primary-400);
            box-shadow: var(--shadow-lg);
        }
        .mentor-card .m-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            margin: 0 auto 12px;
            overflow: hidden;
            border: 2px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
        }
        .mentor-card .m-name { font-weight: 600; color: var(--text-primary); font-size: 1.05rem; }
        .mentor-card .m-title { color: var(--text-muted); font-size: 0.85rem; }
        .mentor-card .m-rating { color: #fbbf24; font-size: 0.9rem; margin: 4px 0; }
        .mentor-card .m-rating span { color: var(--text-muted); font-size: 0.75rem; }
        .mentor-card .m-rate { color: var(--primary-400); font-weight: 600; font-size: 1.1rem; margin-top: 6px; }
        .mentor-card .m-rate span { color: var(--text-muted); font-weight: 400; font-size: 0.8rem; }
        .mentor-card .m-btn {
            margin-top: 12px;
            padding: 6px 20px;
            border-radius: var(--radius-full);
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .mentor-card .m-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }

        /* ============================================
           TESTIMONIALS
           ============================================ */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .testimonial-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: 24px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .testimonial-card:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.06);
            border-color: var(--primary-400);
        }
        .testimonial-card .t-rating { color: #fbbf24; font-size: 0.95rem; margin-bottom: 8px; }
        .testimonial-card .t-content {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
            font-style: italic;
        }
        .testimonial-card .t-author {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--glass-border);
        }
        .testimonial-card .t-author .t-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
        }
        .testimonial-card .t-author .t-info .t-name { font-weight: 600; color: var(--text-primary); font-size: 0.9rem; }
        .testimonial-card .t-author .t-info .t-role { color: var(--text-muted); font-size: 0.8rem; }

        /* ============================================
           CTA SECTION
           ============================================ */
        .cta-section {
            background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(139,92,246,0.08));
            border-radius: var(--radius-2xl);
            padding: 60px 40px;
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            text-align: center;
        }
        .cta-section h2 {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 2.2rem;
            color: var(--text-primary);
            margin-bottom: 12px;
        }
        .cta-section p {
            color: var(--text-secondary);
            font-size: 1.05rem;
            max-width: 600px;
            margin: 0 auto 24px;
        }
        .cta-buttons { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .btn-cta {
            padding: 14px 40px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }
        .btn-cta-primary { background: var(--gradient-primary); color: #fff; border: none; box-shadow: 0 10px 30px rgba(59,130,246,0.3); }
        .btn-cta-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(59,130,246,0.4); color: #fff; }
        .btn-cta-outline { border: 2px solid rgba(255,255,255,0.2); color: var(--text-secondary); background: transparent; }
        .btn-cta-outline:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.4); color: var(--text-primary); }

        /* ============================================
           FOOTER
           ============================================ */
        .footer {
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(20px);
            border-top: 1px solid var(--glass-border);
            padding: 40px 0 20px;
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }
        .footer .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 40px; }
        .footer-brand .brand {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 1.3rem;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            text-decoration: none;
        }
        .footer-brand .brand i { color: var(--primary-400); }
        .footer-brand .brand span { color: var(--primary-400); }
        .footer-brand p { color: var(--text-muted); font-size: 0.9rem; line-height: 1.8; }
        .footer-social { display: flex; gap: 12px; margin-top: 16px; }
        .footer-social a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .footer-social a:hover { background: rgba(255,255,255,0.08); color: var(--primary-400); transform: translateY(-3px); }
        .footer-links h6 { color: var(--text-primary); font-weight: 600; font-size: 0.95rem; margin-bottom: 16px; }
        .footer-links ul { list-style: none; padding: 0; }
        .footer-links ul li { margin-bottom: 8px; }
        .footer-links ul li a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .footer-links ul li a:hover { color: var(--text-primary); padding-left: 4px; }
        .footer-newsletter p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 12px; }
        .footer-newsletter .input-group {
            display: flex;
            background: var(--glass-bg);
            border-radius: var(--radius-full);
            overflow: hidden;
            border: 1px solid var(--glass-border);
        }
        .footer-newsletter .input-group input {
            flex: 1;
            background: transparent;
            border: none;
            padding: 12px 20px;
            color: var(--text-primary);
            font-size: 0.9rem;
            outline: none;
        }
        .footer-newsletter .input-group input::placeholder { color: var(--text-muted); }
        .footer-newsletter .input-group button {
            background: var(--gradient-primary);
            border: none;
            color: #fff;
            padding: 12px 24px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .footer-newsletter .input-group button:hover { opacity: 0.9; }
        .footer-bottom {
            border-top: 1px solid var(--glass-border);
            padding-top: 20px;
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .footer-bottom p { color: var(--text-muted); font-size: 0.8rem; margin: 0; }
        .footer-bottom-links { display: flex; gap: 20px; align-items: center; }
        .footer-bottom-links a { color: var(--text-muted); text-decoration: none; font-size: 0.8rem; transition: all 0.3s ease; }
        .footer-bottom-links a:hover { color: var(--text-primary); }
        .footer-bottom-links span { color: var(--text-muted); font-size: 0.8rem; }

        /* ============================================
           TOAST NOTIFICATIONS
           ============================================ */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-width: 380px;
            width: 100%;
        }
        .toast {
            background: rgba(20,20,40,0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            color: var(--text-primary);
            box-shadow: var(--shadow-lg);
            animation: slideUp 0.4s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .toast.success { border-left: 4px solid var(--success); }
        .toast.error { border-left: 4px solid var(--danger); }
        .toast.info { border-left: 4px solid var(--primary-500); }
        .toast .icon { font-size: 1.2rem; flex-shrink: 0; }
        .toast .content { flex: 1; }
        .toast .title { font-weight: 600; font-size: 0.9rem; }
        .toast .message { font-size: 0.8rem; color: var(--text-secondary); }
        .toast .close { cursor: pointer; color: var(--text-muted); background: none; border: none; font-size: 1rem; padding: 4px; }
        .toast .close:hover { color: var(--text-primary); }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 992px) {
            .hero .container { grid-template-columns: 1fr; gap: 40px; }
            .hero-title { font-size: 2.8rem; }
            .hero-illustration { display: none; }
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 30px; }
        }

        @media (max-width: 991px) {
            .with-v-nav { margin-left: 0 !important; }
            .mobile-nav-toggle-container { display: flex; }
            .hero { padding-top: 90px; }
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem; }
            .hero-subtitle { font-size: 1rem; }
            .hero-stats { gap: 20px; flex-wrap: wrap; justify-content: center; }
            .hero-stats .stat-number { font-size: 1.8rem; }
            .section-header h2 { font-size: 1.8rem; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .fields-grid { grid-template-columns: repeat(3, 1fr); }
            .mentors-grid { grid-template-columns: 1fr; }
            .testimonials-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .cta-section { padding: 30px 20px; }
            .cta-section h2 { font-size: 1.6rem; }
            .cta-buttons { flex-direction: column; }
            .btn-cta { width: 100%; text-align: center; }
            .floating-logo { font-size: 8rem; }
            .floating-icons .icon { display: none; }
        }

        @media (max-width: 480px) {
            .fields-grid { grid-template-columns: repeat(2, 1fr); }
            .features-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .hero-title { font-size: 1.8rem; }
            .hero-buttons { flex-direction: column; }
            .btn-hero { width: 100%; justify-content: center; }
            .hero-stats .stat-item { width: 100%; display: flex; justify-content: space-between; padding: 8px 16px; background: var(--glass-bg); border-radius: var(--radius-md); }
            .toast-container { right: 10px; left: 10px; max-width: 100%; }
            .floating-logo { font-size: 5rem; }
        }
    </style>
</head>
<body>

<!-- ============================================
   ANIMATED BACKGROUND
   ============================================ -->
<div class="bg-animated"></div>
<div class="floating-logo">SkillShare Hub</div>

<div class="floating-icons">
    <div class="icon" style="top:8%;left:5%;"><i class="fas fa-robot"></i><span class="label">Engineering</span></div>
    <div class="icon" style="top:15%;right:8%;animation-delay:2s;"><i class="fas fa-laptop-code"></i><span class="label">IT</span></div>
    <div class="icon" style="top:45%;left:3%;animation-delay:4s;"><i class="fas fa-flask"></i><span class="label">Science</span></div>
    <div class="icon" style="top:55%;right:4%;animation-delay:6s;"><i class="fas fa-briefcase"></i><span class="label">Management</span></div>
    <div class="icon" style="bottom:20%;left:8%;animation-delay:8s;"><i class="fas fa-scale-balanced"></i><span class="label">Law</span></div>
    <div class="icon" style="bottom:30%;right:10%;animation-delay:10s;"><i class="fas fa-graduation-cap"></i><span class="label">Education</span></div>
    <div class="icon" style="top:30%;left:12%;animation-delay:3s;"><i class="fas fa-seedling"></i><span class="label">Agriculture</span></div>
    <div class="icon" style="top:70%;left:15%;animation-delay:7s;"><i class="fas fa-heart-pulse"></i><span class="label">Health</span></div>
    <div class="icon" style="top:85%;right:15%;animation-delay:5s;"><i class="fas fa-palette"></i><span class="label">Arts</span></div>
    <div class="icon" style="top:10%;left:25%;animation-delay:9s;"><i class="fas fa-newspaper"></i><span class="label">Media</span></div>
    <div class="icon" style="bottom:10%;left:30%;animation-delay:1s;"><i class="fas fa-umbrella-beach"></i><span class="label">Hospitality</span></div>
    <div class="icon" style="top:50%;left:50%;animation-delay:11s;"><i class="fas fa-microscope"></i><span class="label">Research</span></div>
    <div class="icon" style="top:60%;right:30%;animation-delay:12s;"><i class="fas fa-atom"></i><span class="label">politics</span></div>
</div>

<!-- ============================================
   HEADER
   ============================================ -->
<?php include 'frontend/components/header.php'; ?>
</div>

<!-- Mobile nav toggle -->
<div class="mobile-nav-toggle-container">
    <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
    </button>
</div>

<!-- ============================================
   VERTICAL NAVBAR (SIDEBAR)
   ============================================ -->
<?php include 'frontend/components/navbar.php'; ?>

<!-- ============================================
   MAIN CONTENT (offset by sidebar)
   ============================================ -->
<div class="with-v-nav">

    <!-- ============================================
       HERO SECTION
       ============================================ -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-sparkles"></i> Bridging Education with Industry
                </div>
                <h1 class="hero-title">Learn from Industry Experts & Build Your Future Career</h1>
                <p class="hero-subtitle">
                    Connect with experienced graduates, gain practical knowledge,
                    and become industry-ready before you graduate.
                </p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn-hero btn-hero-primary">
                        <i class="fas fa-rocket"></i> Get Started Free
                    </a>
                    <a href="#features" class="btn-hero btn-hero-outline">
                        <i class="fas fa-play-circle"></i> Learn More
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Expert Graduates</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100+</div>
                        <div class="stat-label">Active Students</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">10+</div>
                        <div class="stat-label">Practical Skills</div>
                    </div>
                </div>
            </div>
            <div class="hero-illustration">
                <div class="illustration-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="illustration-features">
                    <div class="feature">
                        <div class="f-icon"><i class="fas fa-video"></i></div>
                        <span>Mentorship</span>
                    </div>
                    <div class="feature">
                        <div class="f-icon"><i class="fas fa-code"></i></div>
                        <span>Practical</span>
                    </div>
                    <div class="feature">
                        <div class="f-icon"><i class="fas fa-certificate"></i></div>
                        <span>Certified</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
       FEATURES SECTION
       ============================================ -->
    <section class="section" id="features">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose ShareSkill Hub?</h2>
                <p>Everything you need to bridge the gap between education and industry</p>
                <div class="divider"></div>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="f-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h5>Expert Mentorship</h5>
                    <p>Learn from experienced graduates with real-world industry knowledge</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon"><i class="fas fa-laptop-code"></i></div>
                    <h5>Practical Skills</h5>
                    <p>Access hands-on projects, assignments, and real-world case studies</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon"><i class="fas fa-video"></i></div>
                    <h5>Video Sessions</h5>
                    <p>One-on-one mentorship through live video calls and screen sharing</p>
                </div>
                <div class="feature-card">
                    <div class="f-icon"><i class="fas fa-briefcase"></i></div>
                    <h5>Career Ready</h5>
                    <p>Interview prep, placement materials, and career guidance for your dream job</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
       HOW IT WORKS (added content)
       ============================================ -->
    <section class="section" id="how">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Your journey to industry-readiness in just a few simple steps</p>
                <div class="divider"></div>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <span class="step-num">01</span>
                    <div class="step-icon"><i class="fas fa-user-plus"></i></div>
                    <h5>Create Account</h5>
                    <p>Sign up as a fresher or a graduate and set up your professional profile.</p>
                </div>
                <div class="step-card">
                    <span class="step-num">02</span>
                    <div class="step-icon"><i class="fas fa-search"></i></div>
                    <h5>Find Your Mentor</h5>
                    <p>Browse graduates by field, skills, and ratings to find the perfect match.</p>
                </div>
                <div class="step-card">
                    <span class="step-num">03</span>
                    <div class="step-icon"><i class="fas fa-video"></i></div>
                    <h5>Book a Session</h5>
                    <p>Schedule one-on-one video sessions at a time that suits you best.</p>
                </div>
                <div class="step-card">
                    <span class="step-num">04</span>
                    <div class="step-icon"><i class="fas fa-rocket"></i></div>
                    <h5>Grow & Succeed</h5>
                    <p>Learn practical skills, build confidence, and become industry-ready.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
       FIELDS SECTION
       ============================================ -->
    <section class="section" id="fields">
        <div class="container">
            <div class="section-header">
                <h2>Explore Academic Fields</h2>
                <p>Find your path across multiple disciplines</p>
                <div class="divider"></div>
            </div>
            <div class="fields-grid">
                <a href="academic-filed.php" class="field-card">
                    <div class="f-icon" style="color:#60a5fa;"><i class="fas fa-robot"></i></div>
                    <div class="f-name">Engineering</div>
                    <div class="f-count">10 Courses</div>
                </a>
                <a href="academic-filed.php" class="field-card">
                    <div class="f-icon" style="color:#8b5cf6;"><i class="fas fa-laptop-code"></i></div>
                    <div class="f-name">Information Technology</div>
                    <div class="f-count">12 Courses</div>
                </a>
                <a href="academic-filed.php" class="field-card">
                    <div class="f-icon" style="color:#22c55e;"><i class="fas fa-flask"></i></div>
                    <div class="f-name">Science</div>
                    <div class="f-count">8 Courses</div>
                </a>
                <a href="academic-filed.php" class="field-card">
                    <div class="f-icon" style="color:#f59e0b;"><i class="fas fa-briefcase"></i></div>
                    <div class="f-name">Management</div>
                    <div class="f-count">10 Courses</div>
                </a>
                <a href="academic-filed.php" class="field-card">
                    <div class="f-icon" style="color:#ef4444;"><i class="fas fa-scale-balanced"></i></div>
                    <div class="f-name">Law</div>
                    <div class="f-count">6 Courses</div>
                </a>
                <a href="academic-filed.php" class="field-card">
                    <div class="f-icon" style="color:#8b5cf6;"><i class="fas fa-graduation-cap"></i></div>
                    <div class="f-name">Education</div>
                    <div class="f-count">5 Courses</div>
                </a>
                <a href="academic-filed.php" class="field-card">
                    <div class="f-icon" style="color:#22c55e;"><i class="fas fa-seedling"></i></div>
                    <div class="f-name">Agriculture</div>
                    <div class="f-count">5 Courses</div>
                </a>
                <a href="academic-filed.php" class="field-card">
                    <div class="f-icon" style="color:#ec4899;"><i class="fas fa-heart-pulse"></i></div>
                    <div class="f-name">Health Sciences</div>
                    <div class="f-count">7 Courses</div>
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================
       MENTORS SECTION
       ============================================ -->
    <section class="section" id="mentors">
        <div class="container">
            <div class="section-header">
                <h2>Top Graduates</h2>
                <p>Learn from the best industry experts</p>
                <div class="divider"></div>
            </div>
            <div class="mentors-grid">
                <div class="mentor-card">
                    <div class="m-avatar" style="background:#60a5fa;">JD</div>
                    <div class="m-name">Roshan Timalsina</div>
                    <div class="m-title">full stack developerand learning AI/ML</div>
                    <div class="m-rating">
                        ★★★★★ <span>(156 reviews)</span>
                    </div>
                    <div class="m-rate">$45 <span>/ hour</span></div>
                    <button class="m-btn" onclick="showToast('Success', 'Viewing Roshan Timalsina\'s profile...', 'success')">View Profile</button>
                </div>
                <div class="mentor-card">
                    <div class="m-avatar" style="background:#8b5cf6;">SM</div>
                    <div class="m-name">Abiral Rai</div>
                    <div class="m-title">UI/UX designer and currently running full g</div>
                    <div class="m-rating">
                        ★★★★★ <span>(128 reviews)</span>
                    </div>
                    <div class="m-rate">$50 <span>/ hour</span></div>
                    <button class="m-btn" onclick="showToast('Success', 'Viewing Abiral Rai\'s profile...', 'success')">View Profile</button>
                </div>
                <div class="mentor-card">
                    <div class="m-avatar" style="background:#22c55e;">AK</div>
                    <div class="m-name">Alex Kumar</div>
                    <div class="m-title">Full Stack Developer at Microsoft</div>
                    <div class="m-rating">
                        ★★★★★ <span>(142 reviews)</span>
                    </div>
                    <div class="m-rate">$40 <span>/ hour</span></div>
                    <button class="m-btn" onclick="showToast('Success', 'Viewing Alex Kumar\'s profile...', 'success')">View Profile</button>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
       TESTIMONIALS SECTION
       ============================================ -->
    <section class="section" id="testimonials">
        <div class="container">
            <div class="section-header">
                <h2>What Our Students Say</h2>
                <p>Real stories from real learners</p>
                <div class="divider"></div>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="t-rating">★★★★★</div>
                    <div class="t-content">"ShareSkill Hub helped me transition from theory to practical skills. I landed my dream job at a top tech company!"</div>
                    <div class="t-author">
                        <div class="t-avatar">RS</div>
                        <div class="t-info">
                            <div class="t-name">Rahul Sharma</div>
                            <div class="t-role">Fresher - BCA</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="t-rating">★★★★★</div>
                    <div class="t-content">"I love mentoring students and helping them grow. This platform is truly amazing and rewarding!"</div>
                    <div class="t-author">
                        <div class="t-avatar">PP</div>
                        <div class="t-info">
                            <div class="t-name">Priya Patel</div>
                            <div class="t-role">Graduate - Mentor</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="t-rating">★★★★☆</div>
                    <div class="t-content">"The mentorship sessions were incredibly helpful. My confidence has grown tremendously."</div>
                    <div class="t-author">
                        <div class="t-avatar">AS</div>
                        <div class="t-info">
                            <div class="t-name">Amit Singh</div>
                            <div class="t-role">Fresher - BSc CSIT</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
       CTA SECTION
       ============================================ -->
    <section class="section">
        <div class="container">
            <div class="cta-section">
                <h2>Ready to Start Your Journey?</h2>
                <p>Join thousands of students and graduates who are already building their careers</p>
                <div class="cta-buttons">
                    <a href="register.php" class="btn-cta btn-cta-primary">
                        <i class="fas fa-user-plus"></i> Join Now - It's Free
                    </a>
                    <a href="about.php" class="btn-cta btn-cta-outline">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
       FOOTER
       ============================================ -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.php" class="brand">
                        <i class="fas fa-graduation-cap"></i>
                        ShareSkill <span>Hub</span>
                    </a>
                    <p>Bridging Education with Industry Through Practical Learning and Mentorship.</p>
                    <div class="footer-social">
                        <a href="#" onclick="showToast('Info', 'Facebook page coming soon!', 'info')"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" onclick="showToast('Info', 'Twitter page coming soon!', 'info')"><i class="fab fa-twitter"></i></a>
                        <a href="#" onclick="showToast('Info', 'LinkedIn page coming soon!', 'info')"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" onclick="showToast('Info', 'YouTube channel coming soon!', 'info')"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h6>Platform</h6>
                    <ul>
<li><a href="index.php">Home</a></li>
                        <li><a href="academic-fields.php">Fields</a></li>
                        <li><a href="courses.php">Courses</a></li>
                        <li><a href="mentor.php">Mentors</a></li>
                        <li><a href="research.php">Research</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h6>Resources</h6>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-newsletter">
                    <h6>Stay Updated</h6>
                    <p>Subscribe to our newsletter for updates and news</p>
                    <div class="input-group">
                        <input type="email" placeholder="Your email" aria-label="Email">
                        <button onclick="showToast('Success', 'Subscribed successfully!', 'success')"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 ShareSkill Hub. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#" onclick="showToast('Info', 'Privacy Policy coming soon!', 'info')">Privacy Policy</a>
                    <a href="#" onclick="showToast('Info', 'Terms of Service coming soon!', 'info')">Terms of Service</a>
                    <span>Learn • Connect • Grow</span>
                </div>
            </div>
        </div>
    </footer>

</div><!-- /.with-v-nav -->

<!-- ============================================
   TOAST CONTAINER
   ============================================ -->
<div class="toast-container" id="toastContainer"></div>

<!-- ============================================
   JAVASCRIPT
   ============================================ -->
<script src="frontend/assets/js/navbar.js"></script>
<script>
    // ============================================
    // TOAST SYSTEM
    // ============================================
    function showToast(title, message, type = 'info', duration = 4000) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            info: 'fa-info-circle'
        };
        const colors = {
            success: '#22c55e',
            error: '#ef4444',
            info: '#3b82f6'
        };

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <span class="icon" style="color:${colors[type]}"><i class="fas ${icons[type] || icons.info}"></i></span>
            <div class="content">
                <div class="title">${title}</div>
                <div class="message">${message}</div>
            </div>
            <button class="close"><i class="fas fa-times"></i></button>
        `;

        toast.querySelector('.close').addEventListener('click', function() {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(50px)';
            setTimeout(() => toast.remove(), 300);
        });

        container.appendChild(toast);

        if (duration > 0) {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(50px)';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    }

    // ============================================
    // MOBILE MENU TOGGLE (for vertical navbar)
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const mobileToggle = document.getElementById('mobileNavToggle');
        const vNavbar = document.getElementById('vNavbar');
        const navOverlay = document.getElementById('navOverlay');

        function closeNav() {
            if (vNavbar) vNavbar.classList.remove('mobile-open');
            if (navOverlay) navOverlay.classList.remove('show');
        }

        if (mobileToggle && vNavbar) {
            mobileToggle.addEventListener('click', function() {
                vNavbar.classList.toggle('mobile-open');
                if (navOverlay) navOverlay.classList.toggle('show');
            });
        }
        if (navOverlay) {
            navOverlay.addEventListener('click', closeNav);
        }

        // Close menu on link click (mobile)
        if (vNavbar) {
            vNavbar.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 991) closeNav();
                });
            });
        }

        // ============================================
        // SMOOTH SCROLL FOR ANCHOR LINKS
        // ============================================
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    const navHeight = 70;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navHeight - 20;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // ============================================
        // WELCOME TOAST
        // ============================================
        setTimeout(() => {
            showToast('👋 Welcome!', 'Welcome to ShareSkill Hub. Start your learning journey today!', 'info', 5000);
        }, 1000);
    });

    // ============================================
    // CONSOLE
    // ============================================
    console.log('🏠 ShareSkill Hub - Homepage Loaded');
    console.log('📚 8 Academic Fields | 4 Features | 3 Mentors');
    console.log('🎓 Start your learning journey today!');
</script>

</body>
</html>
</content>
