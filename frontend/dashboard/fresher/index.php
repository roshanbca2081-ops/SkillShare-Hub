<?php
session_start();
$sidebar_role = 'fresher';
$sidebar_active = 'Dashboard';
require_once __DIR__ . '/_shared.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresher Dashboard | ShareSkill Hub</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
            --radius-full: 50px;
            --transition-bounce: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --gradient-primary: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --font-heading: 'Poppins', sans-serif;
            --font-primary: 'Inter', sans-serif;

            /* Sidebar */
            --sidebar-width: 240px;
        }

        /* ============================================
           RESET & BASE
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-primary);
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1a1a2e 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
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
                radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.08) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite alternate;
        }
        @keyframes bgShift {
            0% { transform: translate(0,0) scale(1); }
            100% { transform: translate(5%,-5%) scale(1.05); }
        }

        /* ============================================
           MAIN LAYOUT
           ============================================ */
        .dashboard {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* ============================================
           SIDEBAR
           ============================================ */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(10,10,30,0.92);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            display: flex;
            flex-direction: column;
            padding: 20px 12px;
            z-index: 100;
            overflow-y: auto;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 8px 20px;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 16px;
        }
        .sidebar-brand i {
            font-size: 1.8rem;
            color: var(--primary-400);
        }
        .sidebar-brand h4 {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--text-primary);
        }
        .sidebar-brand h4 span {
            color: var(--primary-400);
        }

        .sidebar-nav {
            flex: 1;
        }
        .sidebar-nav .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 2px;
            cursor: pointer;
        }
        .sidebar-nav .nav-item:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-primary);
        }
        .sidebar-nav .nav-item.active {
            background: rgba(59,130,246,0.12);
            color: var(--primary-400);
        }
        .sidebar-nav .nav-item i {
            width: 20px;
            font-size: 1rem;
        }
        .sidebar-nav .nav-item .badge {
            margin-left: auto;
            background: var(--danger);
            color: #fff;
            font-size: 0.6rem;
            padding: 2px 8px;
            border-radius: var(--radius-full);
        }

        .sidebar-footer {
            border-top: 1px solid var(--glass-border);
            padding-top: 12px;
            margin-top: auto;
        }
        .sidebar-footer .nav-item {
            color: var(--text-muted);
        }
        .sidebar-footer .nav-item:hover {
            color: var(--danger);
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 20px 30px 40px;
            min-height: 100vh;
        }

        /* ============================================
           TOP HEADER
           ============================================ */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0 20px;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .top-header .greeting h2 {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--text-primary);
        }
        .top-header .greeting p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .top-header .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .top-header .header-actions .search-box {
            display: flex;
            align-items: center;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-full);
            padding: 6px 16px;
            transition: all 0.3s ease;
        }
        .top-header .header-actions .search-box:focus-within {
            border-color: var(--primary-400);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
        }
        .top-header .header-actions .search-box input {
            background: transparent;
            border: none;
            padding: 6px 8px;
            color: var(--text-primary);
            font-size: 0.85rem;
            outline: none;
            min-width: 160px;
        }
        .top-header .header-actions .search-box input::placeholder {
            color: var(--text-muted);
        }
        .top-header .header-actions .search-box i {
            color: var(--text-muted);
        }
        .top-header .header-actions .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 12px 4px 4px;
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .top-header .header-actions .profile:hover {
            background: var(--glass-bg);
        }
        .top-header .header-actions .profile img {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-full);
            object-fit: cover;
            border: 2px solid var(--glass-border);
        }
        .top-header .header-actions .profile span {
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* ============================================
           STATS CARDS
           ============================================ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            background: rgba(255,255,255,0.06);
            border-color: var(--border-hover);
        }
        .stat-card .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-card .stat-number {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--text-primary);
        }
        .stat-card .stat-label {
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        .stat-card .stat-icon {
            font-size: 1.6rem;
            color: var(--primary-400);
            opacity: 0.6;
        }

        /* ============================================
           ANIMATION: PEOPLE LEARNING
           ============================================ */
        .learning-animation {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: 24px 30px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            overflow: hidden;
            position: relative;
        }
        .learning-animation .text-content h3 {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--text-primary);
        }
        .learning-animation .text-content p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 4px;
        }
        .learning-animation .text-content .btn-view {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 24px;
            border-radius: var(--radius-full);
            background: var(--gradient-primary);
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .learning-animation .text-content .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59,130,246,0.3);
        }

        /* People Animation */
        .people-animation {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .person {
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: floatPerson 3s ease-in-out infinite;
        }
        .person:nth-child(1) { animation-delay: 0s; }
        .person:nth-child(2) { animation-delay: 0.8s; }
        .person:nth-child(3) { animation-delay: 1.6s; }
        .person:nth-child(4) { animation-delay: 2.4s; }

        @keyframes floatPerson {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        .person .avatar {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            border: 2px solid var(--glass-border);
            transition: all 0.3s ease;
        }
        .person .avatar.purple { background: #8b5cf6; }
        .person .avatar.blue { background: #3b82f6; }
        .person .avatar.green { background: #22c55e; }
        .person .avatar.pink { background: #ec4899; }
        .person .avatar.orange { background: #f59e0b; }

        .person .avatar:hover {
            transform: scale(1.1);
            border-color: var(--primary-400);
        }

        .person .label {
            font-size: 0.6rem;
            color: var(--text-muted);
            margin-top: 2px;
            white-space: nowrap;
        }

        .learning-badge {
            background: rgba(34,197,94,0.15);
            color: var(--success);
            padding: 4px 14px;
            border-radius: var(--radius-full);
            font-size: 0.7rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            animation: pulseBadge 2s ease-in-out infinite;
        }
        @keyframes pulseBadge {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* ============================================
           SECTION HEADER
           ============================================ */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .section-header h3 {
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--text-primary);
        }
        .section-header a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .section-header a:hover {
            color: var(--primary-400);
        }

        /* ============================================
           FIELDS GRID
           ============================================ */
        .fields-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 28px;
        }
        .field-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 16px 14px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .field-card:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.06);
            border-color: var(--primary-400);
        }
        .field-card .icon {
            font-size: 1.8rem;
            color: var(--primary-400);
            margin-bottom: 6px;
            transition: all 0.3s ease;
        }
        .field-card:hover .icon {
            transform: scale(1.1) rotate(-5deg);
        }
        .field-card .name {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        .field-card .count {
            font-size: 0.65rem;
            color: var(--text-muted);
        }

        /* ============================================
           COURSES GRID
           ============================================ */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
        }
        .course-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 16px 18px;
            transition: all 0.3s ease;
        }
        .course-card:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.06);
            border-color: var(--border-hover);
        }
        .course-card .course-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .course-card .course-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            background: rgba(59,130,246,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: var(--primary-400);
        }
        .course-card .course-badge {
            font-size: 0.6rem;
            padding: 2px 10px;
            border-radius: var(--radius-full);
            background: rgba(59,130,246,0.12);
            color: var(--primary-400);
        }
        .course-card .course-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-primary);
            margin: 8px 0 2px;
        }
        .course-card .course-meta {
            color: var(--text-muted);
            font-size: 0.75rem;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 4px;
        }
        .course-card .course-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .course-card .course-btn {
            margin-top: 10px;
            padding: 6px 16px;
            border-radius: var(--radius-full);
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
        }
        .course-card .course-btn:hover {
            background: var(--gradient-primary);
            border-color: var(--primary-500);
            color: #fff;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                width: 220px;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            .menu-toggle {
                display: block !important;
            }
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.3rem;
            cursor: pointer;
            padding: 4px 8px;
        }

        @media (max-width: 768px) {
            .top-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            .top-header .header-actions {
                flex-wrap: wrap;
            }
            .top-header .header-actions .search-box input {
                min-width: 120px;
            }
            .learning-animation {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
            .people-animation {
                justify-content: center;
            }
            .person .avatar {
                width: 36px;
                height: 36px;
                font-size: 0.9rem;
            }
            .fields-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            .courses-grid {
                grid-template-columns: 1fr;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .fields-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }
            .field-card {
                padding: 10px 8px;
            }
            .field-card .icon {
                font-size: 1.3rem;
            }
            .field-card .name {
                font-size: 0.7rem;
            }
            .top-header .greeting h2 {
                font-size: 1.2rem;
            }
            .top-header .header-actions .profile span {
                display: none;
            }
            .learning-animation .text-content h3 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>

<!-- ============================================
   ANIMATED BACKGROUND
   ============================================ -->
<div class="bg-animated"></div>

<?php include '../../components/loader.php'; ?>

<!-- ============================================
   DASHBOARD
   ============================================ -->
<div class="dashboard">

    <!-- ============================================
       SIDEBAR
       ============================================ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap"></i>
            <h4>ShareSkill <span>Hub</span></h4>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item active">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="courses.php" class="nav-item">
                <i class="fas fa-book-open"></i> Courses
                <span class="badge">6</span>
            </a>
            <a href="mentors.php" class="nav-item">
                <i class="fas fa-user-tie"></i> Mentors
                <span class="badge">24</span>
            </a>
            <a href="academic-fields.php" class="nav-item">
                <i class="fas fa-layer-group"></i> Academic Fields
            </a>
            <a href="research.php" class="nav-item">
                <i class="fas fa-microscope"></i> Research
            </a>
            <a href="assignments.php" class="nav-item">
                <i class="fas fa-file-pen"></i> Assignments
            </a>
            <a href="messages.php" class="nav-item">
                <i class="fas fa-envelope"></i> Messages
                <span class="badge">4</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="settings.php" class="nav-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="../../login.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <!-- ============================================
       MAIN CONTENT
       ============================================ -->
    <main class="main-content">

        <!-- ============================================
           TOP HEADER
           ============================================ -->
        <header class="top-header">
            <div class="greeting">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h2>Welcome back, Alex! 👋</h2>
                        <p>Here's what's happening with your learning journey</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search courses...">
                </div>
                <div class="profile">
                    <img src="../../assets/images/profile/avatar-1.svg" alt="Profile">
                    <span>Alex</span>
                </div>
            </div>
        </header>

        <!-- ============================================
           STATS CARDS
           ============================================ -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-top">
                    <div>
                        <div class="stat-number">6</div>
                        <div class="stat-label">Enrolled Courses</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div>
                        <div class="stat-number">12</div>
                        <div class="stat-label">Completed Sessions</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-circle-check"></i></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div>
                        <div class="stat-number">3</div>
                        <div class="stat-label">Certificates</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-award"></i></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div>
                        <div class="stat-number">68%</div>
                        <div class="stat-label">Overall Progress</div>
                    </div>
                    <div class="stat-icon"><i class="fas fa-gauge-high"></i></div>
                </div>
            </div>
        </div>

        <!-- ============================================
           LEARNING ANIMATION
           ============================================ -->
        <div class="learning-animation">
            <div class="text-content">
                <h3>🎓 People are learning right now</h3>
                <p>Join 1,200+ students building their skills</p>
                <a href="courses.php" class="btn-view">
                    <i class="fas fa-arrow-right"></i> View Courses
                </a>
            </div>
            <div class="people-animation">
                <div class="person">
                    <div class="avatar purple">S</div>
                    <span class="label">Learning</span>
                </div>
                <div class="person">
                    <div class="avatar blue">J</div>
                    <span class="label">Coding</span>
                </div>
                <div class="person">
                    <div class="avatar green">M</div>
                    <span class="label">Design</span>
                </div>
                <div class="person">
                    <div class="avatar pink">R</div>
                    <span class="label">Research</span>
                </div>
                <div class="person">
                    <div class="avatar orange">K</div>
                    <span class="label">Mentoring</span>
                </div>
                <div class="learning-badge">
                    <i class="fas fa-circle"></i> 12 active now
                </div>
            </div>
        </div>

        <!-- ============================================
           ACADEMIC FIELDS
           ============================================ -->
        <div class="section-header">
            <h3><i class="fas fa-book" style="color:var(--primary-400);margin-right:8px;"></i>Academic Fields</h3>
            <a href="academic-fields.php">View All →</a>
        </div>
        <div class="fields-grid">
            <div class="field-card">
                <div class="icon"><i class="fas fa-robot"></i></div>
                <div class="name">Engineering</div>
                <div class="count">6 Courses</div>
            </div>
            <div class="field-card">
                <div class="icon"><i class="fas fa-laptop-code"></i></div>
                <div class="name">Information Technology</div>
                <div class="count">8 Courses</div>
           
