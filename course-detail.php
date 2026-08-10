<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillShare Hub - Course Details</title>

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
            --border-hover: rgba(96,165,250,0.35);
            --shadow-lg: 0 8px 40px rgba(0,0,0,0.4);
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-full: 50px;
            --transition-bounce: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --gradient-primary: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --font-heading: 'Poppins', sans-serif;
            --font-primary: 'Inter', sans-serif;
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
            line-height: 1.6;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
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
                radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.12) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.12) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(6,182,212,0.06) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite alternate;
        }
        @keyframes bgShift {
            0% { transform: translate(0,0) scale(1) rotate(0deg); }
            50% { transform: translate(5%,-5%) scale(1.05) rotate(2deg); }
            100% { transform: translate(-5%,5%) scale(0.95) rotate(-2deg); }
        }

        .floating-logo {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 12rem;
            font-weight: 900;
            font-family: var(--font-heading);
            color: rgba(255,255,255,0.02);
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

        /* ============================================
           NAVBAR
           ============================================ */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 12px 0;
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            transition: all 0.3s ease;
        }
        .navbar.scrolled {
            background: rgba(0,0,0,0.85);
        }
        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 1.3rem;
            color: var(--text-primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand i {
            color: var(--primary-400);
            font-size: 1.6rem;
        }
        .navbar-brand span { color: var(--primary-400); }

        .nav-links {
            display: flex;
            gap: 4px;
            align-items: center;
        }
        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .nav-links a:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-primary);
        }
        .nav-links a.active {
            background: rgba(59,130,246,0.12);
            color: var(--primary-400);
        }
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Course nav for switching */
        .course-nav {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 4px 0 16px;
            margin-bottom: 20px;
            flex-wrap: nowrap;
            scrollbar-width: none;
        }
        .course-nav::-webkit-scrollbar { display: none; }
        .course-nav .course-link {
            padding: 10px 20px;
            border-radius: var(--radius-full);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            flex-shrink: 0;
            text-decoration: none;
        }
        .course-nav .course-link:hover {
            background: rgba(255,255,255,0.06);
            color: var(--text-primary);
        }
        .course-nav .course-link.active {
            background: var(--gradient-primary);
            border-color: var(--primary-500);
            color: #fff;
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */
        .main-content {
            padding-top: 80px;
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 20px;
            padding-right: 20px;
            position: relative;
            z-index: 1;
            padding-bottom: 60px;
        }

        /* ============================================
           BREADCRUMB
           ============================================ */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 0.85rem;
            padding: 16px 0;
            flex-wrap: wrap;
        }
        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .breadcrumb a:hover {
            color: var(--primary-400);
        }
        .breadcrumb .separator {
            color: var(--text-muted);
        }
        .breadcrumb .current {
            color: var(--text-secondary);
        }

        /* ============================================
           COURSE HEADER
           ============================================ */
        .course-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: 30px 40px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 20px;
        }
        .course-header .course-info h1 {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .course-header .course-info .field-tag {
            color: var(--primary-400);
            font-size: 0.9rem;
            font-weight: 500;
        }
        .course-header .course-info .field-tag i {
            margin-right: 6px;
        }
        .course-header .course-info p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            max-width: 600px;
            margin-top: 8px;
        }
        .course-header .course-meta {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            margin-top: 12px;
        }
        .course-header .course-meta span {
            color: var(--text-muted);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .course-header .course-meta span i {
            color: var(--primary-400);
        }
        .course-header .course-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .course-header .course-actions .btn {
            padding: 10px 24px;
            border-radius: var(--radius-full);
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: var(--gradient-primary);
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59,130,246,0.3);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
        }
        .btn-outline:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-primary);
        }

        /* ============================================
           SEMESTER TABS
           ============================================ */
        .semester-tabs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 4px 0 16px;
            margin-bottom: 20px;
            flex-wrap: nowrap;
            scrollbar-width: none;
        }
        .semester-tabs::-webkit-scrollbar {
            display: none;
        }
        .semester-tabs .tab {
            padding: 10px 24px;
            border-radius: var(--radius-full);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .semester-tabs .tab:hover {
            background: rgba(255,255,255,0.06);
            color: var(--text-primary);
        }
        .semester-tabs .tab.active {
            background: var(--gradient-primary);
            border-color: var(--primary-500);
            color: #fff;
        }

        /* ============================================
           SUBJECTS GRID
           ============================================ */
        .subjects-container {
            animation: fadeInUp 0.4s ease;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .subject-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 20px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: flex-start;
            gap: 14px;
            opacity: 0;
            animation: fadeInUp 0.4s ease forwards;
        }
        .subject-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-hover);
            box-shadow: var(--shadow-lg);
            background: rgba(255,255,255,0.06);
        }
        .subject-card .subject-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: rgba(59,130,246,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--primary-400);
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        .subject-card:hover .subject-icon {
            background: var(--gradient-primary);
            color: #fff;
            transform: scale(1.1) rotate(-5deg);
        }
        .subject-card .subject-info {
            flex: 1;
        }
        .subject-card .subject-info .subject-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.95rem;
        }
        .subject-card .subject-info .subject-code {
            color: var(--text-muted);
            font-size: 0.75rem;
        }
        .subject-card .subject-info .subject-credits {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: var(--text-muted);
            font-size: 0.7rem;
            background: var(--glass-bg);
            padding: 2px 10px;
            border-radius: var(--radius-full);
            margin-top: 4px;
        }
        .subject-card .subject-info .subject-credits i {
            color: var(--warning);
        }

        /* ============================================
           SEMESTER SUMMARY
           ============================================ */
        .semester-summary {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .semester-summary .summary-item {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .semester-summary .summary-item:hover {
            background: rgba(255,255,255,0.06);
            border-color: var(--border-hover);
        }
        .semester-summary .summary-item.active {
            border-color: var(--primary-400);
            background: rgba(59,130,246,0.08);
        }
        .semester-summary .summary-item .semester-number {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-primary);
        }
        .semester-summary .summary-item .semester-count {
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        /* ============================================
           TOAST
           ============================================ */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
            width: 100%;
        }
        .toast {
            background: rgba(20,20,40,0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            color: var(--text-primary);
            box-shadow: var(--shadow-lg);
            animation: slideInRight 0.5s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .toast.success { border-left: 4px solid var(--success); }
        .toast.error { border-left: 4px solid var(--danger); }
        .toast.info { border-left: 4px solid var(--primary-500); }
        .toast .icon { font-size: 1.3rem; flex-shrink: 0; }
        .toast .content { flex: 1; }
        .toast .title { font-weight: 600; font-size: 0.9rem; }
        .toast .message { font-size: 0.8rem; color: var(--text-secondary); }
        .toast .close {
            cursor: pointer;
            color: var(--text-muted);
            background: none;
            border: none;
            font-size: 1.1rem;
            padding: 4px;
        }
        .toast .close:hover { color: var(--text-primary); }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 992px) {
            .course-header { padding: 24px; }
            .course-header .course-info h1 { font-size: 1.6rem; }
            .subjects-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); }
        }

        @media (max-width: 768px) {
            .menu-toggle { display: block; }
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(0,0,0,0.95);
                backdrop-filter: blur(20px);
                padding: 20px;
                gap: 8px;
                border-bottom: 1px solid var(--glass-border);
            }
            .nav-links.open { display: flex; }
            .nav-links a { width: 100%; text-align: center; }

            .course-header { flex-direction: column; padding: 20px; }
            .course-header .course-actions { width: 100%; }
            .course-header .course-actions .btn { flex: 1; justify-content: center; }

            .semester-summary { grid-template-columns: repeat(4, 1fr); }
            .subjects-grid { grid-template-columns: 1fr; }
            .floating-logo { font-size: 6rem; }
        }

        @media (max-width: 480px) {
            .course-header .course-info h1 { font-size: 1.3rem; }
            .semester-summary { grid-template-columns: repeat(2, 1fr); }
            .semester-tabs .tab { padding: 8px 16px; font-size: 0.75rem; }
            .subject-card { padding: 14px; }
            .course-header .course-meta { gap: 12px; }
            .course-header .course-meta span { font-size: 0.75rem; }
            .toast-container { right: 10px; left: 10px; max-width: 100%; }
        }
    </style>
</head>
<body>

<!-- ============================================
   ANIMATED BACKGROUND
   ============================================ -->
<div class="bg-animated"></div>
<div class="floating-logo">SkillShare Hub</div>

<!-- ============================================
   NAVBAR
   ============================================ -->
<nav class="navbar" id="navbar">
    <div class="container">
        <a href="index.php" class="navbar-brand">
            <i class="fas fa-graduation-cap"></i>
            SkillShsre <span>Hub</span>
        </a>
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="nav-links" id="navLinks">
            <a href="index.php">Home</a>
            <a href="academic-filed.php">Academic Fields</a>
            <a href="mentor.php">Mentors</a>
            <a href="research.php">Research</a>
            <a href="about.php">About</a>
            <a href="login.php" class="btn-login">Login</a>
            <a href="register.php" class="btn-register">Register</a>
        </div>
    </div>
</nav>

<!-- ============================================
   TOAST CONTAINER
   ============================================ -->
<div class="toast-container" id="toastContainer"></div>

<!-- ============================================
   MAIN CONTENT
   ============================================ -->
<div class="main-content" id="mainContent">

    <!-- ============================================
       BREADCRUMB
       ============================================ -->
    <div class="breadcrumb">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <span class="separator">/</span>
        <a href="academic-filed.php">Academic Fields</a>
        <span class="separator">/</span>
        <a href="#" id="fieldBreadcrumb">Information Technology</a>
        <span class="separator">/</span>
        <span class="current" id="courseBreadcrumb">Software Engineering</span>
    </div>

    <!-- ============================================
       COURSE NAVIGATION
       ============================================ -->
    <div class="course-nav" id="courseNav">
        <!-- Generated by JavaScript -->
    </div>

    <!-- ============================================
       COURSE HEADER
       ============================================ -->
    <div class="course-header" id="courseHeader">
        <div class="course-info">
            <div class="field-tag" id="fieldTag">
                <i class="fas fa-laptop-code"></i> Information Technology
            </div>
            <h1 id="courseTitle">Software Engineering</h1>
            <p id="courseDescription">Learn software development lifecycle, agile methodologies, and modern programming practices. Master the art of building scalable, maintainable, and high-quality software applications.</p>
            <div class="course-meta">
                <span><i class="far fa-clock"></i> <span id="courseDuration">4 Years</span></span>
                <span><i class="fas fa-signal"></i> <span id="courseLevel">Bachelor</span></span>
                <span><i class="fas fa-users"></i> <span id="courseStudents">100+ Students</span></span>
                <span><i class="fas fa-book"></i> <span id="courseSubjects">42 Subjects</span></span>
            </div>
        </div>
        <div class="course-actions">
            <button class="btn btn-primary" onclick="enrollCourse()">
                <i class="fas fa-user-plus"></i> Enroll Now
            </button>
            <button class="btn btn-outline" onclick="showToast('Info', 'Course bookmarked!', 'info')">
                <i class="fas fa-bookmark"></i> Bookmark
            </button>
        </div>
    </div>

    <!-- ============================================
       SEMESTER SUMMARY
       ============================================ -->
    <div class="semester-summary" id="semesterSummary">
        <!-- Generated by JavaScript -->
    </div>

    <!-- ============================================
       SEMESTER TABS
       ============================================ -->
    <div class="semester-tabs" id="semesterTabs">
        <!-- Generated by JavaScript -->
    </div>

    <!-- ============================================
       SUBJECTS GRID
       ============================================ -->
    <div class="subjects-container" id="subjectsContainer">
        <div class="subjects-grid" id="subjectsGrid">
            <!-- Generated by JavaScript -->
        </div>
    </div>

</div>

<!-- ============================================
   JAVASCRIPT
   ============================================ -->
<script src="frontend/assets/js/course-data.js"></script>
<script>
    // ============================================
    // CURRENT STATE
    // ============================================
    let currentCourseId = "software-engineering";
    let currentSemester = 1;

    // ============================================
    // RENDER FUNCTIONS
    // ============================================
    function renderCourseNav() {
        const container = document.getElementById('courseNav');
        container.innerHTML = courseList.map(c => `
            <a href="?course=${c.id}" class="course-link ${c.id === currentCourseId ? 'active' : ''}">
                ${c.name}
            </a>
        `).join('');
    }

    function loadCourse(courseId) {
        const course = courseData[courseId];
        if (!course) {
            showToast('Error', 'Course not found!', 'error');
            return;
        }

        currentCourseId = courseId;
        currentSemester = 1;

        // Update header
        document.getElementById('fieldTag').innerHTML = `<i class="fas ${course.fieldIcon}"></i> ${course.field}`;
        document.getElementById('courseTitle').textContent = course.name;
        document.getElementById('courseDescription').textContent = course.description;
        document.getElementById('courseDuration').textContent = course.duration;
        document.getElementById('courseLevel').textContent = course.level;
        document.getElementById('courseStudents').textContent = course.students;
        document.getElementById('courseSubjects').textContent = course.semesters.reduce((acc, s) => acc + s.subjects.length, 0) + ' Subjects';

        // Update breadcrumb
        document.getElementById('fieldBreadcrumb').textContent = course.field;
        document.getElementById('courseBreadcrumb').textContent = course.name;

        // Render course nav
        renderCourseNav();

        // Render semester summary
        renderSemesterSummary(course);

        // Render semester tabs
        renderSemesterTabs(course);

        // Render subjects
        renderSubjects(course, 1);

        // Update URL
        history.pushState({ courseId: courseId }, '', `?course=${courseId}`);
    }

    function renderSemesterSummary(course) {
        const container = document.getElementById('semesterSummary');
        container.innerHTML = course.semesters.map(s => `
            <div class="summary-item ${s.number === currentSemester ? 'active' : ''}"
                 onclick="selectSemester(${s.number})">
                <div class="semester-number">Semester ${s.number}</div>
                <div class="semester-count">${s.subjects.length} Subjects</div>
            </div>
        `).join('');
    }

    function renderSemesterTabs(course) {
        const container = document.getElementById('semesterTabs');
        container.innerHTML = course.semesters.map(s => `
            <div class="tab ${s.number === currentSemester ? 'active' : ''}"
                 onclick="selectSemester(${s.number})">
                <i class="fas fa-book"></i> Semester ${s.number}
            </div>
        `).join('');
    }

    function renderSubjects(course, semesterNumber) {
        const semester = course.semesters.find(s => s.number === semesterNumber);
        if (!semester) return;

        const grid = document.getElementById('subjectsGrid');
        const container = document.getElementById('subjectsContainer');

        // Animate container
        container.style.animation = 'none';
        setTimeout(() => {
            container.style.animation = 'fadeInUp 0.4s ease';
        }, 10);

        const subjectIcons = {
            'Programming': 'fa-code',
            'Mathematics': 'fa-square-root-variable',
            'English': 'fa-language',
            'Digital': 'fa-microchip',
            'Data': 'fa-database',
            'Web': 'fa-globe',
            'Software': 'fa-laptop-code',
            'Operating': 'fa-server',
            'Computer': 'fa-desktop',
            'Algorithms': 'fa-brain',
            'Network': 'fa-network-wired',
            'Security': 'fa-shield-alt',
            'Cloud': 'fa-cloud',
            'AI': 'fa-robot',
            'Database': 'fa-database',
            'Machine': 'fa-brain',
            'System': 'fa-cogs',
            'Mobile': 'fa-mobile-alt',
            'Testing': 'fa-vial',
            'Project': 'fa-project-diagram',
            'Management': 'fa-tasks',
            'DevOps': 'fa-code-branch',
            'UI/UX': 'fa-paint-brush',
            'Research': 'fa-microscope',
            'Professional': 'fa-briefcase',
            'Business': 'fa-chart-line',
            'Ethics': 'fa-balance-scale',
            'Leadership': 'fa-user-tie',
            'Career': 'fa-rocket',
            'Entrepreneurship': 'fa-lightbulb',
            'Finance': 'fa-coins',
            'Marketing': 'fa-bullhorn',
            'Strategy': 'fa-chess-queen',
            'Analytics': 'fa-chart-pie',
            'Communication': 'fa-comments'
        };

        function getSubjectIcon(name) {
            for (const [key, icon] of Object.entries(subjectIcons)) {
                if (name.toLowerCase().includes(key.toLowerCase())) {
                    return icon;
                }
            }
            return 'fa-graduation-cap';
        }

        grid.innerHTML = semester.subjects.map((subject, index) => `
            <div class="subject-card" style="animation-delay:${index * 0.05}s">
                <div class="subject-icon">
                    <i class="fas ${getSubjectIcon(subject.name)}"></i>
                </div>
                <div class="subject-info">
                    <div class="subject-name">${subject.name}</div>
                    <div class="subject-code">${subject.code}</div>
                    <span class="subject-credits">
                        <i class="fas fa-star"></i> ${subject.credits} Credits
                    </span>
                </div>
            </div>
        `).join('');
    }

    // ============================================
    // SELECT SEMESTER
    // ============================================
    function selectSemester(semesterNumber) {
        const course = courseData[currentCourseId];
        if (!course) return;

        currentSemester = semesterNumber;
        renderSemesterSummary(course);
        renderSemesterTabs(course);
        renderSubjects(course, semesterNumber);

        document.getElementById('subjectsContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ============================================
    // ENROLL COURSE
    // ============================================
    function enrollCourse() {
        const course = courseData[currentCourseId];
        if (!course) return;
        showToast('Enrolled!', `You have successfully enrolled in "${course.name}"`, 'success', 3000);
    }

    // ============================================
    // TOAST SYSTEM
    // ============================================
    function showToast(title, message, type = 'info', duration = 5000) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
        const colors = { success: '#22c55e', error: '#ef4444', info: '#3b82f6' };

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

        toast.querySelector('.close').addEventListener('click', () => closeToast(toast));
        container.appendChild(toast);

        if (duration > 0) {
            setTimeout(() => closeToast(toast), duration);
        }
    }

    function closeToast(toast) {
        if (!toast) return;
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        setTimeout(() => { if (toast.parentNode) toast.remove(); }, 300);
    }

    // ============================================
    // NAVBAR SCROLL & MOBILE MENU
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        if (menuToggle && navLinks) {
            menuToggle.addEventListener('click', function() {
                navLinks.classList.toggle('open');
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            });
        }

        // Load course from URL or default
        const urlParams = new URLSearchParams(window.location.search);
        const courseParam = urlParams.get('course');

        if (courseParam && courseData[courseParam]) {
            loadCourse(courseParam);
        } else {
            loadCourse('software-engineering');
        }
    });

    // ============================================
    // CONSOLE
    // ============================================
    console.log('📚 ShareSkill Hub - Course Detail Loaded');
    console.log(`📊 ${Object.keys(courseData).length} Courses Available`);
    console.log('🎓 Select a course to view semester-wise subjects');
</script>

</body>
</html>
