<?php
/*
 * Shared Discovery App - Academic Field -> Course -> Skill -> Mentor
 * Used by academic-filed.php and courses.php.
 * Requires the shared platform-header.php (navbar) and platform-footer.php (showToast).
 */
?>

<!-- ============================================
   DISCOVERY APP STYLES (scoped to this component)
   ============================================ -->
<style>
    /* ---------- Breadcrumb ---------- */
    .disc-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-size: 0.85rem;
        padding: 16px 0 24px;
        flex-wrap: wrap;
    }
    .disc-breadcrumb a { color: var(--text-muted); text-decoration: none; transition: all 0.3s ease; cursor: pointer; }
    .disc-breadcrumb a:hover { color: var(--primary-400); }
    .disc-breadcrumb .separator { color: var(--text-muted); }
    .disc-breadcrumb .current { color: var(--text-secondary); }

    /* ---------- Page Header ---------- */
    .disc-page-header { text-align: center; padding: 10px 0 30px; }
    .disc-page-header h1 {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 2.8rem;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .disc-page-header p { color: var(--text-secondary); font-size: 1.1rem; max-width: 600px; margin: 8px auto 0; }

    /* ---------- Search & Filters ---------- */
    .disc-search-filters { display: flex; gap: 12px; margin-bottom: 28px; flex-wrap: wrap; align-items: center; }
    .disc-search-box { flex: 1; min-width: 200px; display: flex; align-items: center; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-full); padding: 8px 16px; transition: all 0.3s ease; }
    .disc-search-box:focus-within { border-color: var(--primary-400); box-shadow: 0 0 0 4px rgba(59,130,246,0.1); }
    .disc-search-box input { flex: 1; background: transparent; border: none; padding: 8px 12px; color: var(--text-primary); font-size: 0.9rem; outline: none; }
    .disc-search-box input::placeholder { color: var(--text-muted); }
    .disc-search-box i { color: var(--text-muted); }

    .disc-filter-group { display: flex; gap: 8px; flex-wrap: wrap; }
    .disc-filter-btn { padding: 8px 18px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; }
    .disc-filter-btn:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .disc-filter-btn.active { background: rgba(59,130,246,0.12); border-color: var(--primary-400); color: var(--primary-400); }

    /* ---------- Step Indicator ---------- */
    .disc-step-indicator { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 30px; flex-wrap: wrap; }
    .disc-step { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 0.85rem; font-weight: 500; padding: 8px 16px; border-radius: var(--radius-full); background: var(--glass-bg); border: 1px solid var(--glass-border); transition: all 0.3s ease; }
    .disc-step .disc-step-num { width: 28px; height: 28px; border-radius: var(--radius-full); background: var(--glass-bg); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); transition: all 0.3s ease; }
    .disc-step.active { border-color: var(--primary-400); color: var(--text-primary); }
    .disc-step.active .disc-step-num { background: var(--gradient-primary); color: #fff; }
    .disc-step.done { border-color: var(--success); color: var(--success); }
    .disc-step.done .disc-step-num { background: var(--success); color: #fff; }
    .disc-step-arrow { color: var(--text-muted); font-size: 0.8rem; }

    /* ---------- Field Grid ---------- */
    .disc-fields-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .disc-field-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-2xl); padding: 24px 20px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; position: relative; overflow: hidden; }
    .disc-field-card::before { content: ''; position: absolute; inset: 0; background: var(--gradient-primary); opacity: 0; transition: all 0.4s ease; }
    .disc-field-card:hover { transform: translateY(-8px) scale(1.02); border-color: var(--primary-400); box-shadow: 0 15px 50px rgba(59,130,246,0.2); }
    .disc-field-card:hover::before { opacity: 0.05; }
    .disc-field-card .df-icon { font-size: 3rem; color: var(--primary-400); margin-bottom: 12px; transition: all 0.3s ease; position: relative; z-index: 1; }
    .disc-field-card:hover .df-icon { transform: scale(1.1) rotate(-5deg); }
    .disc-field-card .df-name { font-weight: 700; font-size: 1.1rem; color: var(--text-primary); position: relative; z-index: 1; }
    .disc-field-card .df-desc { color: var(--text-muted); font-size: 0.8rem; margin: 4px 0 10px; position: relative; z-index: 1; }
    .disc-field-card .df-stats { display: flex; gap: 16px; position: relative; z-index: 1; }
    .disc-field-card .df-stats span { display: flex; align-items: center; gap: 4px; color: var(--text-muted); font-size: 0.75rem; }
    .disc-field-card .df-stats span i { color: var(--primary-400); }
    .disc-field-card .df-stats span .value { color: var(--text-secondary); font-weight: 500; }
    .disc-field-card .df-arrow { position: absolute; bottom: 16px; right: 20px; color: var(--text-muted); transition: all 0.3s ease; z-index: 1; }
    .disc-field-card:hover .df-arrow { color: var(--primary-400); transform: translateX(4px); }

    /* ---------- Course Grid ---------- */
    .disc-courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
    .disc-course-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 20px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; text-align: center; }
    .disc-course-card:hover { transform: translateY(-6px); background: rgba(255,255,255,0.06); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .disc-course-card .dc-icon { width: 56px; height: 56px; border-radius: var(--radius-md); background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-size: 1.5rem; color: var(--primary-400); transition: all 0.3s ease; }
    .disc-course-card:hover .dc-icon { background: var(--gradient-primary); color: #fff; transform: scale(1.1) rotate(-5deg); }
    .disc-course-card .dc-name { font-weight: 600; font-size: 0.95rem; color: var(--text-primary); }
    .disc-course-card .dc-meta { display: flex; justify-content: center; gap: 12px; margin-top: 8px; font-size: 0.7rem; color: var(--text-muted); }
    .disc-course-card .dc-meta span i { color: var(--primary-400); margin-right: 2px; }

    /* ---------- Skills Grid ---------- */
    .disc-skills-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
    .disc-skill-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 14px 16px; transition: all 0.3s ease; cursor: pointer; display: flex; align-items: center; gap: 10px; }
    .disc-skill-card:hover { background: rgba(255,255,255,0.06); border-color: var(--primary-400); transform: translateY(-3px); }
    .disc-skill-card .ds-icon { width: 32px; height: 32px; border-radius: var(--radius-md); background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: var(--primary-400); flex-shrink: 0; }
    .disc-skill-card .ds-name { font-size: 0.85rem; color: var(--text-secondary); font-weight: 500; }
    .disc-skill-card .ds-count { margin-left: auto; font-size: 0.65rem; color: var(--text-muted); background: var(--glass-bg); padding: 2px 10px; border-radius: var(--radius-full); }

    /* ---------- Mentor Grid ---------- */
    .disc-mentors-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
    .disc-mentor-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-2xl); padding: 24px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; }
    .disc-mentor-card:hover { transform: translateY(-6px); background: rgba(255,255,255,0.06); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .disc-mentor-card .dm-top { display: flex; gap: 16px; align-items: flex-start; }
    .disc-mentor-card .dm-avatar { width: 64px; height: 64px; border-radius: var(--radius-full); overflow: hidden; border: 2px solid var(--glass-border); flex-shrink: 0; position: relative; }
    .disc-mentor-card .dm-avatar .avatar-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: #fff; }
    .disc-mentor-card .dm-avatar .verified-badge { position: absolute; bottom: -2px; right: -2px; width: 20px; height: 20px; border-radius: var(--radius-full); background: var(--success); display: flex; align-items: center; justify-content: center; font-size: 0.5rem; color: #fff; border: 2px solid rgba(0,0,0,0.8); }
    .disc-mentor-card .dm-avatar .avatar-placeholder.purple { background: #8b5cf6; }
    .disc-mentor-card .dm-avatar .avatar-placeholder.blue { background: #3b82f6; }
    .disc-mentor-card .dm-avatar .avatar-placeholder.green { background: #22c55e; }
    .disc-mentor-card .dm-avatar .avatar-placeholder.pink { background: #ec4899; }
    .disc-mentor-card .dm-avatar .avatar-placeholder.orange { background: #f59e0b; }
    .disc-mentor-card .dm-avatar .avatar-placeholder.teal { background: #14b8a6; }
    .disc-mentor-card .dm-info { flex: 1; }
    .disc-mentor-card .dm-info .dm-name { font-weight: 600; font-size: 1.05rem; color: var(--text-primary); display: flex; align-items: center; gap: 6px; }
    .disc-mentor-card .dm-info .dm-name .verified-icon { color: var(--success); font-size: 0.8rem; }
    .disc-mentor-card .dm-info .dm-title { color: var(--text-muted); font-size: 0.8rem; }
    .disc-mentor-card .dm-info .dm-company { color: var(--text-secondary); font-size: 0.75rem; }
    .disc-mentor-card .dm-info .dm-rating { color: #fbbf24; font-size: 0.85rem; margin-top: 2px; }
    .disc-mentor-card .dm-info .dm-rating span { color: var(--text-muted); font-size: 0.75rem; }
    .disc-mentor-card .dm-badges { display: flex; gap: 6px; flex-wrap: wrap; margin: 8px 0; }
    .disc-mentor-card .dm-badges .badge { padding: 2px 10px; border-radius: var(--radius-full); font-size: 0.6rem; font-weight: 500; background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-muted); }
    .disc-mentor-card .dm-badges .badge.experience { border-color: rgba(59,130,246,0.3); color: var(--primary-400); }
    .disc-mentor-card .dm-badges .badge.online { border-color: rgba(34,197,94,0.3); color: var(--success); }
    .disc-mentor-card .dm-skills { display: flex; gap: 4px; flex-wrap: wrap; margin: 8px 0; }
    .disc-mentor-card .dm-skills .skill-tag { padding: 2px 10px; border-radius: var(--radius-full); background: rgba(59,130,246,0.08); color: var(--text-secondary); font-size: 0.65rem; border: 1px solid rgba(59,130,246,0.1); }
    .disc-mentor-card .dm-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--glass-border); flex-wrap: wrap; gap: 8px; }
    .disc-mentor-card .dm-footer .dm-price { font-weight: 700; font-size: 1.1rem; color: var(--primary-400); }
    .disc-mentor-card .dm-footer .dm-price span { font-weight: 400; color: var(--text-muted); font-size: 0.8rem; }
    .disc-mentor-card .dm-footer .dm-actions { display: flex; gap: 6px; }
    .disc-mentor-card .dm-footer .dm-actions .btn { padding: 6px 14px; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 500; cursor: pointer; transition: all 0.3s ease; border: none; }
    .disc-mentor-card .dm-footer .dm-actions .btn-primary { background: var(--gradient-primary); color: #fff; }
    .disc-mentor-card .dm-footer .dm-actions .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(59,130,246,0.3); }
    .disc-mentor-card .dm-footer .dm-actions .btn-outline { background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); }
    .disc-mentor-card .dm-footer .dm-actions .btn-outline:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }

    /* ---------- Back Button ---------- */
    .disc-back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; margin-bottom: 16px; }
    .disc-back-btn:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }

    /* ---------- Section Title ---------- */
    .disc-section-title { font-family: var(--font-heading); font-weight: 700; font-size: 1.5rem; color: var(--text-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
    .disc-section-title i { color: var(--primary-400); }
    .disc-section-title .count { font-weight: 400; font-size: 1rem; color: var(--text-muted); }

    /* ---------- Empty State ---------- */
    .disc-empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .disc-empty-state i { font-size: 4rem; display: block; margin-bottom: 16px; opacity: 0.3; }
    .disc-empty-state h4 { color: var(--text-secondary); margin-bottom: 4px; }

    /* ---------- Responsive ---------- */
    @media (max-width: 992px) {
        .disc-fields-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); }
        .disc-mentors-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
        .disc-skills-grid { grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); }
    }
    @media (max-width: 768px) {
        .disc-page-header h1 { font-size: 2rem; }
        .disc-fields-grid { grid-template-columns: 1fr 1fr; }
        .disc-courses-grid { grid-template-columns: 1fr 1fr; }
        .disc-mentors-grid { grid-template-columns: 1fr; }
        .disc-skills-grid { grid-template-columns: 1fr 1fr; }
        .disc-step-indicator { gap: 6px; }
        .disc-step { font-size: 0.7rem; padding: 4px 10px; }
        .disc-step .disc-step-num { width: 22px; height: 22px; font-size: 0.65rem; }
        .disc-step-arrow { display: none; }
        .disc-search-filters { flex-direction: column; }
        .disc-search-box { width: 100%; }
        .disc-filter-group { justify-content: center; }
        .disc-mentor-card .dm-top { flex-direction: column; align-items: center; text-align: center; }
        .disc-mentor-card .dm-badges { justify-content: center; }
        .disc-mentor-card .dm-skills { justify-content: center; }
        .disc-mentor-card .dm-footer { flex-direction: column; align-items: stretch; }
        .disc-mentor-card .dm-footer .dm-actions { justify-content: center; }
    }
    @media (max-width: 480px) {
        .disc-fields-grid { grid-template-columns: 1fr; }
        .disc-courses-grid { grid-template-columns: 1fr; }
        .disc-skills-grid { grid-template-columns: 1fr; }
        .disc-page-header h1 { font-size: 1.6rem; }
        .disc-mentor-card .dm-footer .dm-actions .btn { width: 100%; text-align: center; }
    }
</style>

<!-- ============================================
   BREADCRUMB
   ============================================ -->
<div class="disc-breadcrumb reveal">
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
    <span class="separator">/</span>
    <span class="current" id="discBreadcrumbCurrent">Academic Fields</span>
</div>

<!-- ============================================
   STEP INDICATOR
   ============================================ -->
<div class="disc-step-indicator" id="discStepIndicator">
    <div class="disc-step active" data-step="1">
        <span class="disc-step-num">1</span><span>Field</span>
    </div>
    <span class="disc-step-arrow"><i class="fas fa-chevron-right"></i></span>
    <div class="disc-step" data-step="2">
        <span class="disc-step-num">2</span><span>Course</span>
    </div>
    <span class="disc-step-arrow"><i class="fas fa-chevron-right"></i></span>
    <div class="disc-step" data-step="3">
        <span class="disc-step-num">3</span><span>Skill</span>
    </div>
    <span class="disc-step-arrow"><i class="fas fa-chevron-right"></i></span>
    <div class="disc-step" data-step="4">
        <span class="disc-step-num">4</span><span>Mentors</span>
    </div>
</div>

<!-- ============================================
   PAGE HEADER
   ============================================ -->
<div class="disc-page-header" id="discPageHeader">
    <h1 id="discPageTitle">Discover Academic Fields</h1>
    <p id="discPageSubtitle">Choose your academic field to find the right courses and mentors</p>
</div>

<!-- ============================================
   SEARCH & FILTERS
   ============================================ -->
<div class="disc-search-filters" id="discSearchFilters">
    <div class="disc-search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="discSearchInput" placeholder="Search fields, courses, skills, mentors..." oninput="discHandleSearch()">
    </div>
    <div class="disc-filter-group" id="discFilterGroup">
        <button class="disc-filter-btn active" data-filter="all" onclick="discSetFilter('all')">All</button>
        <button class="disc-filter-btn" data-filter="it" onclick="discSetFilter('it')">IT</button>
        <button class="disc-filter-btn" data-filter="engineering" onclick="discSetFilter('engineering')">Engineering</button>
        <button class="disc-filter-btn" data-filter="management" onclick="discSetFilter('management')">Management</button>
        <button class="disc-filter-btn" data-filter="science" onclick="discSetFilter('science')">Science</button>
    </div>
</div>

<!-- ============================================
   CONTENT CONTAINER
   ============================================ -->
<div id="discContentContainer">
    <!-- Rendered by JavaScript -->
</div>

<script>
    // ============================================
    // COMPLETE DATA
    // ============================================
    const discFieldsData = {
        "information-technology": {
            id: "information-technology", name: "Information Technology", icon: "fa-laptop-code", color: "#60a5fa", description: "Learn programming, web development, and modern IT skills", mentors: 156, skills: 89,
            courses: [
                { id: "bca", name: "BCA", icon: "fa-graduation-cap", mentors: 45, skills: 32, rating: 4.8 },
                { id: "csit", name: "BSc CSIT", icon: "fa-code", mentors: 52, skills: 38, rating: 4.7 },
                { id: "bit", name: "BIT", icon: "fa-laptop", mentors: 38, skills: 28, rating: 4.6 },
                { id: "bim", name: "BIM", icon: "fa-database", mentors: 30, skills: 25, rating: 4.5 },
                { id: "software-engineering", name: "Software Engineering", icon: "fa-cogs", mentors: 65, skills: 45, rating: 4.9 },
                { id: "computer-engineering", name: "Computer Engineering", icon: "fa-microchip", mentors: 48, skills: 35, rating: 4.7 },
                { id: "information-systems", name: "Information Systems", icon: "fa-server", mentors: 28, skills: 22, rating: 4.4 },
                { id: "cyber-security", name: "Cyber Security", icon: "fa-shield-alt", mentors: 35, skills: 30, rating: 4.8 },
                { id: "data-science", name: "Data Science", icon: "fa-chart-bar", mentors: 42, skills: 36, rating: 4.8 },
                { id: "artificial-intelligence", name: "Artificial Intelligence", icon: "fa-brain", mentors: 40, skills: 34, rating: 4.9 }
            ],
            skills: ["HTML & CSS", "JavaScript", "PHP", "Laravel", "React", "Node.js", "Python", "Java", "C Programming", "C++", "Database Design", "MySQL", "MongoDB", "API Development", "Backend Development", "Frontend Development", "Full Stack Development", "Authentication", "CRUD Operations", "Git & GitHub", "Version Control", "Deployment", "Hosting", "Docker", "Linux", "Cloud Computing", "UI/UX Design", "Figma", "Mobile Responsive Design", "MVC Architecture", "Debugging", "Code Optimization", "Project Architecture", "Final Year Project", "Internship Preparation", "Interview Preparation", "Resume Building", "Portfolio Development", "Agile Workflow", "Cyber Security Basics", "Networking Basics", "AI Integration", "Data Structures", "Algorithms", "System Design", "DevOps", "Kubernetes"]
        },
        "computer-engineering": {
            id: "computer-engineering", name: "Computer Engineering", icon: "fa-microchip", color: "#8b5cf6", description: "Study computer architecture, hardware, and embedded systems", mentors: 98, skills: 56,
            courses: [
                { id: "be-computer", name: "BE Computer", icon: "fa-microchip", mentors: 48, skills: 32, rating: 4.7 },
                { id: "bct", name: "BCT", icon: "fa-cogs", mentors: 35, skills: 28, rating: 4.6 },
                { id: "computer-engineering-diploma", name: "Diploma Computer", icon: "fa-laptop", mentors: 15, skills: 20, rating: 4.3 },
                { id: "embedded-systems", name: "Embedded Systems", icon: "fa-robot", mentors: 20, skills: 18, rating: 4.5 }
            ],
            skills: ["C Programming", "C++", "Python", "Java", "Embedded Systems", "Microcontrollers", "Arduino", "Raspberry Pi", "IoT", "Computer Architecture", "Digital Logic", "VLSI Design", "Circuit Design", "PCB Design", "MATLAB", "Simulation", "Operating Systems", "Linux", "RTOS", "Networking", "Network Security", "Data Structures", "Algorithms", "System Design", "Hardware-Software Integration", "Debugging", "Testing", "Project Development", "Final Year Project", "Internship Preparation", "Interview Preparation", "Resume Building", "Technical Writing", "Research Methodology"]
        },
        "civil-engineering": {
            id: "civil-engineering", name: "Civil Engineering", icon: "fa-building", color: "#22c55e", description: "Design and build infrastructure, structures, and roads", mentors: 112, skills: 62,
            courses: [
                { id: "be-civil", name: "BE Civil", icon: "fa-building", mentors: 52, skills: 35, rating: 4.6 },
                { id: "diploma-civil", name: "Diploma Civil", icon: "fa-hard-hat", mentors: 28, skills: 22, rating: 4.3 },
                { id: "structural-engineering", name: "Structural Engineering", icon: "fa-cubes", mentors: 25, skills: 18, rating: 4.7 },
                { id: "transportation-engineering", name: "Transportation Engineering", icon: "fa-road", mentors: 18, skills: 15, rating: 4.4 },
                { id: "water-resources", name: "Water Resources Engineering", icon: "fa-water", mentors: 15, skills: 12, rating: 4.3 }
            ],
            skills: ["AutoCAD", "Civil 3D", "Revit", "Structural Analysis", "Surveying", "Quantity Estimation", "Cost Estimation", "STAAD Pro", "ETABS", "SAP2000", "Primavera", "MS Project", "Construction Management", "Site Management", "Project Planning", "Concrete Technology", "Soil Mechanics", "Geotechnical Engineering", "Hydrology", "Water Supply", "Sanitary Engineering", "Transportation Planning", "Highway Engineering", "Bridge Engineering", "Earthquake Engineering", "Environmental Engineering", "GIS", "Building Codes", "Estimation", "Valuation", "Contract Management", "Tendering", "Quality Control", "Safety Management", "Internship Preparation", "Final Year Project", "Technical Report Writing", "Research Methodology"]
        },
        "mechanical-engineering": {
            id: "mechanical-engineering", name: "Mechanical Engineering", icon: "fa-cog", color: "#f59e0b", description: "Design and manufacture mechanical systems and machines", mentors: 95, skills: 52,
            courses: [
                { id: "be-mechanical", name: "BE Mechanical", icon: "fa-cog", mentors: 48, skills: 30, rating: 4.6 },
                { id: "diploma-mechanical", name: "Diploma Mechanical", icon: "fa-tools", mentors: 22, skills: 18, rating: 4.3 },
                { id: "automobile-engineering", name: "Automobile Engineering", icon: "fa-car", mentors: 25, skills: 20, rating: 4.5 },
                { id: "mechatronics", name: "Mechatronics", icon: "fa-robot", mentors: 20, skills: 16, rating: 4.6 }
            ],
            skills: ["SolidWorks", "AutoCAD", "CATIA", "Pro/Engineer", "ANSYS", "MATLAB", "Simulink", "CNC Programming", "CAM", "FEA", "CFD", "Thermodynamics", "Heat Transfer", "Fluid Mechanics", "Machine Design", "Dynamics", "Vibrations", "Materials Science", "Manufacturing Processes", "Production Planning", "Supply Chain", "Quality Control", "Six Sigma", "Lean Manufacturing", "Robotics", "Hydraulics", "Pneumatics", "Control Systems", "Instrumentation", "Maintenance Engineering", "Project Management", "Internship Preparation", "Final Year Project", "Technical Writing", "Research Methodology"]
        },
        "business-administration": {
            id: "business-administration", name: "Business Administration", icon: "fa-chart-line", color: "#3b82f6", description: "Learn business management, leadership, and strategy", mentors: 145, skills: 72,
            courses: [
                { id: "bba", name: "BBA", icon: "fa-briefcase", mentors: 55, skills: 35, rating: 4.7 },
                { id: "bbm", name: "BBM", icon: "fa-bar-chart", mentors: 42, skills: 28, rating: 4.6 },
                { id: "mba", name: "MBA", icon: "fa-graduation-cap", mentors: 48, skills: 32, rating: 4.8 },
                { id: "bim", name: "BIM", icon: "fa-database", mentors: 28, skills: 22, rating: 4.5 },
                { id: "bbs", name: "BBS", icon: "fa-calculator", mentors: 30, skills: 20, rating: 4.4 }
            ],
            skills: ["Business Planning", "Strategic Management", "Leadership", "Team Management", "Organizational Behavior", "Human Resource Management", "Recruitment", "Performance Management", "Marketing Management", "Digital Marketing", "Social Media Marketing", "Content Marketing", "Brand Management", "Financial Management", "Financial Analysis", "Accounting", "Tally", "QuickBooks", "Taxation", "Auditing", "Business Law", "Corporate Law", "Business Communication", "Public Speaking", "Presentation Skills", "Negotiation", "Conflict Resolution", "Project Management", "Agile", "Scrum", "Data Analysis", "Excel", "Power BI", "Tableau", "Market Research", "Consumer Behavior", "E-commerce", "Entrepreneurship", "Startup Planning", "Investment", "Risk Management", "Business Ethics", "CSR", "Internship Preparation", "Final Year Project", "Case Study Analysis"]
        },
        "management": {
            id: "management", name: "Management", icon: "fa-tasks", color: "#8b5cf6", description: "Develop management skills for business and organizations", mentors: 98, skills: 52,
            courses: [
                { id: "bbs", name: "BBS", icon: "fa-calculator", mentors: 38, skills: 25, rating: 4.4 },
                { id: "bba", name: "BBA", icon: "fa-briefcase", mentors: 45, skills: 30, rating: 4.6 },
                { id: "mbm", name: "MBM", icon: "fa-chart-line", mentors: 15, skills: 15, rating: 4.5 }
            ],
            skills: ["Management Principles", "Organizational Behavior", "Leadership", "Strategic Management", "Operations Management", "Supply Chain", "Project Management", "Quality Management", "Human Resource Management", "Financial Management", "Marketing Management", "Business Communication", "Public Speaking", "Negotiation", "Conflict Resolution", "Decision Making", "Problem Solving", "Critical Thinking", "Team Building", "Coaching", "Mentoring", "Performance Management", "Recruitment", "Training & Development", "Business Ethics", "Corporate Social Responsibility", "Entrepreneurship", "Innovation", "Change Management", "Risk Management", "Business Law", "International Business", "Global Strategy", "Case Study Analysis", "Internship Preparation", "Final Year Project", "Research Methodology"]
        },
        "commerce": {
            id: "commerce", name: "Commerce", icon: "fa-coins", color: "#f59e0b", description: "Study accounting, finance, and business commerce", mentors: 112, skills: 58,
            courses: [
                { id: "bcom", name: "BCom", icon: "fa-coins", mentors: 45, skills: 28, rating: 4.5 },
                { id: "bbs", name: "BBS", icon: "fa-calculator", mentors: 35, skills: 22, rating: 4.4 },
                { id: "mbs", name: "MBS", icon: "fa-chart-line", mentors: 32, skills: 20, rating: 4.6 }
            ],
            skills: ["Accounting", "Financial Accounting", "Management Accounting", "Cost Accounting", "Taxation", "Auditing", "Financial Reporting", "Accounting Software", "Tally", "QuickBooks", "SAP", "Excel", "Financial Analysis", "Investment Analysis", "Portfolio Management", "Banking", "Insurance", "Financial Markets", "Business Law", "Corporate Law", "Contract Law", "Economics", "Microeconomics", "Macroeconomics", "Business Statistics", "Data Analysis", "SPSS", "Research Methodology", "Internship Preparation", "Final Year Project", "Portfolio Development", "Career Planning"]
        },
        "science": {
            id: "science", name: "Science", icon: "fa-flask", color: "#8b5cf6", description: "Study natural sciences, research, and laboratory techniques", mentors: 88, skills: 48,
            courses: [
                { id: "bsc-physics", name: "BSc Physics", icon: "fa-atom", mentors: 25, skills: 16, rating: 4.5 },
                { id: "bsc-chemistry", name: "BSc Chemistry", icon: "fa-flask", mentors: 22, skills: 14, rating: 4.4 },
                { id: "bsc-biology", name: "BSc Biology", icon: "fa-dna", mentors: 20, skills: 14, rating: 4.5 },
                { id: "bsc-biotech", name: "BSc Biotechnology", icon: "fa-microscope", mentors: 21, skills: 15, rating: 4.6 }
            ],
            skills: ["Laboratory Techniques", "Research Methodology", "Scientific Writing", "Data Analysis", "SPSS", "R Programming", "Python for Research", "Statistical Analysis", "Literature Review", "Thesis Preparation", "Presentation Skills", "Research Publication", "Microscopy", "Spectroscopy", "Chromatography", "PCR", "Gel Electrophoresis", "Cell Culture", "Microbiology", "Biochemistry", "Molecular Biology", "Genetics", "Bioinformatics", "Environmental Science", "Field Work", "Internship Preparation", "Final Year Project", "Technical Writing", "Research Ethics"]
        },
        "architecture": {
            id: "architecture", name: "Architecture", icon: "fa-drafting-compass", color: "#ec4899", description: "Design buildings, spaces, and urban environments", mentors: 78, skills: 44,
            courses: [
                { id: "bachelor-architecture", name: "Bachelor of Architecture", icon: "fa-building", mentors: 28, skills: 22, rating: 4.7 },
                { id: "bsc-architecture", name: "BSc Architecture", icon: "fa-landmark", mentors: 24, skills: 18, rating: 4.5 },
                { id: "interior-design", name: "Interior Design", icon: "fa-couch", mentors: 15, skills: 14, rating: 4.4 },
                { id: "architectural-engineering", name: "Architectural Engineering", icon: "fa-helmet-safety", mentors: 11, skills: 12, rating: 4.3 }
            ],
            skills: ["AutoCAD", "Revit Architecture", "SketchUp", "3ds Max", "Rhino", "Lumion", "V-Ray", "Architectural Drawing", "Interior Design", "Urban Planning", "Landscape Architecture", "Building Materials", "Construction Technology", "Structural Systems", "Environmental Design", "Sustainable Design", "Model Making", "Presentation Drawing", "Portfolio Development", "Site Analysis", "Space Planning", "CAD Modeling", "3D Rendering", "Design Studio", "Architectural History", "Building Codes", "Project Management", "Internship Preparation", "Final Year Project"]
        },
        "electrical-engineering": {
            id: "electrical-engineering", name: "Electrical Engineering", icon: "fa-bolt", color: "#f59e0b", description: "Study power systems, circuits, and electrical design", mentors: 105, skills: 58,
            courses: [
                { id: "be-electrical", name: "BE Electrical", icon: "fa-bolt", mentors: 45, skills: 30, rating: 4.6 },
                { id: "diploma-electrical", name: "Diploma Electrical", icon: "fa-plug", mentors: 25, skills: 20, rating: 4.3 },
                { id: "power-engineering", name: "Power Engineering", icon: "fa-battery-full", mentors: 20, skills: 16, rating: 4.5 },
                { id: "electronics-power", name: "Power & Electronics", icon: "fa-microchip", mentors: 15, skills: 12, rating: 4.4 }
            ],
            skills: ["Circuit Analysis", "Electrical Machines", "Power Systems", "Power Electronics", "Control Systems", "Electrical Design", "MATLAB", "Simulink", "ETAP", "AutoCAD Electrical", "PLC Programming", "SCADA", "Renewable Energy", "Solar Power", "Electrical Wiring", "Transformer Design", "Switchgear", "Protective Relaying", "Electric Drives", "Instrumentation", "Digital Electronics", "Analog Electronics", "Microprocessors", "Embedded Systems", "Electromagnetics", "Signal Processing", "Network Analysis", "Safety Standards", "Project Management", "Internship Preparation", "Final Year Project"]
        },
        "electronics-engineering": {
            id: "electronics-engineering", name: "Electronics & Communication", icon: "fa-satellite-dish", color: "#3b82f6", description: "Study electronic devices, communication systems, and telecom", mentors: 88, skills: 50,
            courses: [
                { id: "be-electronics", name: "BE Electronics", icon: "fa-broadcast-tower", mentors: 40, skills: 28, rating: 4.6 },
                { id: "be-communication", name: "BE Communication", icon: "fa-satellite-dish", mentors: 30, skills: 22, rating: 4.5 },
                { id: "telecom-engineering", name: "Telecom Engineering", icon: "fa-phone-volume", mentors: 18, skills: 15, rating: 4.4 }
            ],
            skills: ["Analog Electronics", "Digital Electronics", "Microprocessors", "Microcontrollers", "Communication Systems", "Signal Processing", "VLSI Design", "Embedded Systems", "IoT", "Circuit Design", "PCB Design", "RF Engineering", "Optical Communication", "Wireless Communication", "Antenna Design", "MATLAB", "LabVIEW", "Networking", "Operating Systems", "C Programming", "Python", "VHDL", "Verilog", "FPGA", "Arduino", "Raspberry Pi", "Instrumentation", "Control Systems", "Project Development", "Final Year Project", "Internship Preparation"]
        },
        "nursing": {
            id: "nursing", name: "Nursing", icon: "fa-user-nurse", color: "#ec4899", description: "Learn patient care, clinical skills, and healthcare", mentors: 120, skills: 64,
            courses: [
                { id: "bsc-nursing", name: "BSc Nursing", icon: "fa-user-nurse", mentors: 50, skills: 32, rating: 4.8 },
                { id: "bn", name: "BN", icon: "fa-notes-medical", mentors: 40, skills: 28, rating: 4.7 },
                { id: "midwifery", name: "Midwifery", icon: "fa-baby", mentors: 18, skills: 15, rating: 4.6 },
                { id: "community-nursing", name: "Community Nursing", icon: "fa-hands", mentors: 12, skills: 12, rating: 4.4 }
            ],
            skills: ["Patient Care", "Clinical Skills", "Nursing Assessment", "Medication Administration", "IV Therapy", "Wound Care", "Patient Monitoring", "Vital Signs", "Emergency Care", "First Aid", "CPR", "Maternal Health", "Child Health", "Community Health", "Mental Health Nursing", "Medical-Surgical Nursing", "Pediatric Nursing", "Geriatric Care", "Infection Control", "Hygiene Procedures", "Documentation", "Patient Education", "Communication Skills", "Ethics in Nursing", "Anatomy", "Physiology", "Pharmacology", "Microbiology", "Nutrition", "Clinical Placement", "Internship Preparation", "Licensure Preparation"]
        },
        "pharmacy": {
            id: "pharmacy", name: "Pharmacy", icon: "fa-pills", color: "#22c55e", description: "Study medicines, pharmacology, and drug management", mentors: 78, skills: 46,
            courses: [
                { id: "bpharm", name: "BPharm", icon: "fa-pills", mentors: 38, skills: 26, rating: 4.7 },
                { id: "pharmd", name: "PharmD", icon: "fa-file-prescription", mentors: 22, skills: 18, rating: 4.8 },
                { id: "pharmaceutical-science", name: "Pharmaceutical Science", icon: "fa-flask", mentors: 18, skills: 14, rating: 4.5 }
            ],
            skills: ["Pharmacology", "Pharmaceutical Chemistry", "Pharmacognosy", "Pharmaceutics", "Drug Formulation", "Clinical Pharmacy", "Drug Interactions", "Dosage Forms", "Pharmacokinetics", "Pharmacodynamics", "Drug Safety", "Hospital Pharmacy", "Community Pharmacy", "Drug Dispensing", "Pharmaceutical Analysis", "Quality Control", "GMP", "Medicinal Chemistry", "Toxicology", "Regulatory Affairs", "Pharmacovigilance", "Biopharmaceutics", "Research Methodology", "Laboratory Techniques", "Communication Skills", "Ethics", "Internship Preparation", "Licensure Preparation"]
        },
        "medicine-health": {
            id: "medicine-health", name: "Medicine & Health Sciences", icon: "fa-stethoscope", color: "#ef4444", description: "Study medicine, public health, and clinical sciences", mentors: 165, skills: 80,
            courses: [
                { id: "mbbs", name: "MBBS", icon: "fa-user-doctor", mentors: 60, skills: 45, rating: 4.9 },
                { id: "bds", name: "BDS", icon: "fa-tooth", mentors: 30, skills: 22, rating: 4.8 },
                { id: "bph", name: "BPH", icon: "fa-heart-pulse", mentors: 25, skills: 18, rating: 4.6 },
                { id: "bmlt", name: "BMLT", icon: "fa-microscope", mentors: 20, skills: 15, rating: 4.5 },
                { id: "physiotherapy", name: "BSc Physiotherapy", icon: "fa-person-walking", mentors: 18, skills: 14, rating: 4.6 }
            ],
            skills: ["Anatomy", "Physiology", "Biochemistry", "Pathology", "Microbiology", "Pharmacology", "Clinical Skills", "Patient Examination", "Diagnosis", "History Taking", "Emergency Medicine", "Surgery", "Internal Medicine", "Pediatrics", "Public Health", "Epidemiology", "Community Medicine", "Health Education", "Medical Ethics", "Medical Research", "Clinical Placement", "Laboratory Techniques", "Diagnostic Skills", "Medical Imaging", "Patient Care", "Communication Skills", "Anatomy Dissection", "SEM", "Case Presentation", "Internship Preparation", "Licensure Preparation"]
        },
        "law":
        {
            id: "law", name: "Law", icon: "fa-scale-balanced", color: "#8b5cf6", description: "Study legal systems, justice, and advocacy", mentors: 95, skills: 52,
            courses: [
                { id: "llb", name: "LLB", icon: "fa-scale-balanced", mentors: 40, skills: 28, rating: 4.7 },
                { id: "ba-llb", name: "BA LLB", icon: "fa-gavel", mentors: 30, skills: 22, rating: 4.6 },
                { id: "bbl", name: "BBL", icon: "fa-balance-scale", mentors: 15, skills: 12, rating: 4.4 },
                { id: "llm", name: "LLM", icon: "fa-landmark", mentors: 10, skills: 10, rating: 4.8 }
            ],
            skills: ["Constitutional Law", "Criminal Law", "Civil Law", "Contract Law", "Corporate Law", "Labor Law", "Property Law", "Family Law", "Tax Law", "Human Rights", "Legal Writing", "Drafting", "Legal Research", "Case Analysis", "Advocacy", "Mooting", "Negotiation", "Mediation", "Legal Ethics", "Jurisprudence", "Evidence Law", "Procedural Law", "International Law", "Cyber Law", "Intellectual Property", "Legal Aid", "Court Procedures", "Client Counseling", "Legal Documentation", "Internship Preparation", "Bar Exam Preparation"]
        },
        "education-teaching":
        {
            id: "education-teaching", name: "Education & Teaching", icon: "fa-chalkboard-user", color: "#3b82f6", description: "Prepare for a career in teaching and education", mentors: 85, skills: 48,
            courses: [
                { id: "bed", name: "BEd", icon: "fa-chalkboard-user", mentors: 35, skills: 24, rating: 4.5 },
                { id: "ba-ed", name: "BA Education", icon: "fa-book-open-reader", mentors: 20, skills: 16, rating: 4.4 },
                { id: "mad", name: "MEd", icon: "fa-graduation-cap", mentors: 15, skills: 12, rating: 4.6 },
                { id: "eln", name: "ELT", icon: "fa-language", mentors: 15, skills: 12, rating: 4.5 }
            ],
            skills: ["Teaching Methods", "Curriculum Design", "Lesson Planning", "Classroom Management", "Educational Psychology", "Child Development", "Learning Theories", "Assessment & Evaluation", "Educational Technology", "Instructional Design", "Special Education", "Inclusive Education", "Educational Research", "Communication Skills", "Public Speaking", "Student Counseling", "Educational Leadership", "Education Policy", "Online Teaching", "Digital Learning", "Micro Teaching", "Practicum", "Portfolio Development", "Internship Preparation"]
        },
        "humanities": {
            id: "humanities", name: "Humanities & Social Sciences", icon: "fa-user-graduate", color: "#14b8a6", description: "Study society, culture, language, and history", mentors: 72, skills: 40,
            courses: [
                { id: "ba-english", name: "BA English", icon: "fa-book", mentors: 22, skills: 15, rating: 4.4 },
                { id: "ba-socialwork", name: "BA Social Work", icon: "fa-hand-holding-heart", mentors: 18, skills: 13, rating: 4.5 },
                { id: "ba-sociology", name: "BA Sociology", icon: "fa-people-roof", mentors: 15, skills: 11, rating: 4.3 },
                { id: "ba-psychology", name: "BA Psychology", icon: "fa-brain", mentors: 17, skills: 12, rating: 4.6 }
            ],
            skills: ["Critical Thinking", "Academic Writing", "Communication", "Research Methods", "Literary Analysis", "Creative Writing", "Public Speaking", "Sociology", "Psychology", "History", "Political Science", "Anthropology", "Philosophy", "Ethics", "Cultural Studies", "Social Research", "Interviewing", "Counseling Skills", "Report Writing", "Presentation Skills", "Data Analysis", "Case Study", "Essay Writing", "Internship Preparation", "Final Project"]
        },
        "agriculture": {
            id: "agriculture", name: "Agriculture & Forestry", icon: "fa-seedling", color: "#22c55e", description: "Study crop science, forestry, and agribusiness", mentors: 82, skills: 48,
            courses: [
                { id: "bsc-agri", name: "BSc Agriculture", icon: "fa-seedling", mentors: 38, skills: 26, rating: 4.6 },
                { id: "bsc-forestry", name: "BSc Forestry", icon: "fa-tree", mentors: 20, skills: 15, rating: 4.5 },
                { id: "bsc-horticulture", name: "BSc Horticulture", icon: "fa-leaf", mentors: 14, skills: 12, rating: 4.4 },
                { id: "agri-business", name: "Agricultural Business", icon: "fa-tractor", mentors: 10, skills: 10, rating: 4.3 }
            ],
            skills: ["Crop Science", "Agronomy", "Soil Science", "Horticulture", "Plant Pathology", "Entomology", "Agricultural Economics", "Farming Systems", "Irrigation", "Water Management", "Greenhouse Farming", "Fertilizer Management", "Pest Management", "Organic Farming", "Agribusiness", "Forest Management", "Forestry", "Wildlife Management", "Agroforestry", "Farm Machinery", "Agricultural Marketing", "Supply Chain", "Data Analysis", "Field Research", "Report Writing", "Internship Preparation", "Final Year Project"]
        },
        "veterinary": {
            id: "veterinary", name: "Veterinary Science", icon: "fa-paw", color: "#f59e0b", description: "Study animal health, medicine, and care", mentors: 65, skills: 42,
            courses: [
                { id: "bvs", name: "BVSc & AH", icon: "fa-paw", mentors: 35, skills: 24, rating: 4.7 },
                { id: "animal-science", name: "Animal Science", icon: "fa-cow", mentors: 18, skills: 14, rating: 4.5 },
                { id: "veterinary-tech", name: "Veterinary Technology", icon: "fa-stethoscope", mentors: 12, skills: 10, rating: 4.4 }
            ],
            skills: ["Veterinary Anatomy", "Animal Physiology", "Veterinary Pathology", "Veterinary Pharmacology", "Animal Nutrition", "Livestock Management", "Animal Husbandry", "Surgery", "Disease Diagnosis", "Vaccination", "Animal Reproduction", "Poultry Science", "Dairy Science", "Veterinary Microbiology", "Parasitology", "Zoonotic Diseases", "Lab Diagnosis", "Clinical Skills", "Animal Welfare", "Ethics", "Field Practice", "Final Year Project", "Internship Preparation"]
        },
        "mathematics": {
            id: "mathematics", name: "Mathematics & Statistics", icon: "fa-square-root-variable", color: "#8b5cf6", description: "Study pure and applied mathematics and data analysis", mentors: 70, skills: 44,
            courses: [
                { id: "bsc-math", name: "BSc Mathematics", icon: "fa-square-root-variable", mentors: 30, skills: 22, rating: 4.6 },
                { id: "bsc-stats", name: "BSc Statistics", icon: "fa-chart-pie", mentors: 22, skills: 16, rating: 4.5 },
                { id: "actuarial", name: "Actuarial Science", icon: "fa-coins", mentors: 10, skills: 8, rating: 4.7 },
                { id: "math-computing", name: "Math & Computing", icon: "fa-calculator", mentors: 16, skills: 12, rating: 4.4 }
            ],
            skills: ["Calculus", "Algebra", "Geometry", "Trigonometry", "Linear Algebra", "Probability", "Statistics", "Data Analysis", "SPSS", "Python", "R Programming", "Mathematical Modeling", "Number Theory", "Discrete Mathematics", "Differential Equations", "Real Analysis", "Complex Analysis", "Optimization", "Econometrics", "Excel", "Research Methods", "Problem Solving", "Logical Reasoning", "Final Year Project", "Internship Preparation"]
        },
        "arts-design": {
            id: "arts-design", name: "Arts & Design", icon: "fa-palette", color: "#ec4899", description: "Study visual arts, graphic design, and creative media", mentors: 75, skills: 46,
            courses: [
                { id: "bfa", name: "BFA", icon: "fa-palette", mentors: 25, skills: 18, rating: 4.5 },
                { id: "graphic-design", name: "Graphic Design", icon: "fa-pen-nib", mentors: 20, skills: 16, rating: 4.6 },
                { id: "multimedia", name: "Multimedia", icon: "fa-clapperboard", mentors: 15, skills: 12, rating: 4.4 },
                { id: "fashion-design", name: "Fashion Design", icon: "fa-shirt", mentors: 15, skills: 12, rating: 4.5 }
            ],
            skills: ["Drawing", "Painting", "Color Theory", "Composition", "Graphic Design", "Photoshop", "Illustrator", "InDesign", "Branding", "Logo Design", "Typography", "UI/UX Design", "Figma", "Digital Art", "Illustration", "Photography", "Video Editing", "Premiere Pro", "After Effects", "Animation", "Web Design", "Sketching", "Print Design", "Portfolio Development", "Creative Thinking", "Final Project"]
        },
        "economics": {
            id: "economics", name: "Economics", icon: "fa-chart-column", color: "#3b82f6", description: "Study economic theory, policy, and analysis", mentors: 68, skills: 42,
            courses: [
                { id: "ba-economics", name: "BA Economics", icon: "fa-chart-column", mentors: 28, skills: 20, rating: 4.6 },
                { id: "bsc-economics", name: "BSc Economics", icon: "fa-chart-line", mentors: 22, skills: 16, rating: 4.5 },
                { id: "masters-econ", name: "MSc Economics", icon: "fa-chart-area", mentors: 12, skills: 10, rating: 4.7 },
                { id: "development-econ", name: "Development Economics", icon: "fa-arrow-trend-up", mentors: 8, skills: 8, rating: 4.4 }
            ],
            skills: ["Microeconomics", "Macroeconomics", "Econometrics", "Economic Analysis", "Statistics", "Data Analysis", "Excel", "STATA", "EViews", "R Programming", "Economic Policy", "Development Economics", "International Economics", "Monetary Economics", "Public Finance", "Labor Economics", "Behavioral Economics", "Mathematical Economics", "Research Methods", "Report Writing", "Critical Thinking", "Quantitative Analysis", "Final Year Project", "Internship Preparation"]
        },
        "tourism-hospitality": {
            id: "tourism-hospitality", name: "Tourism & Hospitality", icon: "fa-umbrella-beach", color: "#14b8a6", description: "Study travel, hospitality management, and services", mentors: 88, skills: 50,
            courses: [
                { id: "bhm", name: "BHM", icon: "fa-hotel", mentors: 35, skills: 24, rating: 4.6 },
                { id: "bts", name: "BTS", icon: "fa-plane-departure", mentors: 25, skills: 18, rating: 4.4 },
                { id: "ttm", name: "Travel & Tourism Management", icon: "fa-compass", mentors: 18, skills: 14, rating: 4.5 },
                { id: "culinary-arts", name: "Culinary Arts", icon: "fa-utensils", mentors: 10, skills: 10, rating: 4.6 }
            ],
            skills: ["Front Office Management", "Housekeeping", "Food & Beverage Service", "Culinary Skills", "Hotel Management", "Tourism Planning", "Travel Operations", "Tour Guiding", "Airline Management", "Reservation Systems", "Customer Service", "Communication Skills", "Event Management", "Crash Course", "Hospitality Marketing", "Revenue Management", "Food Safety", "Beverage Knowledge", "Cross-Cultural Skills", "Language Skills", "Internship Preparation", "Final Project"]
        },
        "journalism-media": {
            id: "journalism-media", name: "Journalism & Media", icon: "fa-newspaper", color: "#f59e0b", description: "Study reporting, digital media, and broadcasting", mentors: 62, skills: 40,
            courses: [
                { id: "bjmc", name: "BJMC", icon: "fa-newspaper", mentors: 28, skills: 20, rating: 4.5 },
                { id: "ba-journalism", name: "BA Journalism", icon: "fa-microphone", mentors: 18, skills: 14, rating: 4.4 },
                { id: "mass-comm", name: "Mass Communication", icon: "fa-bullhorn", mentors: 16, skills: 12, rating: 4.5 }
            ],
            skills: ["Journalism", "Reporting", "Editing", "News Writing", "Feature Writing", "Broadcasting", "Photography", "Video Production", "Social Media Management", "Content Writing", "Copywriting", "Media Ethics", "Public Relations", "Press Release", "Interviewing", "Investigative Reporting", "Data Journalism", "Podcasting", "Camera Handling", "Voice & Accent", "Digital Media", "Media Law", "Typography", "Final Project", "Internship Preparation"]
        },
        "psychology": {
            id: "psychology", name: "Psychology", icon: "fa-brain", color: "#8b5cf6", description: "Study human mind, behavior, and counseling", mentors: 70, skills: 44,
            courses: [
                { id: "bsc-psychology", name: "BSc Psychology", icon: "fa-brain", mentors: 32, skills: 24, rating: 4.7 },
                { id: "ba-psychology", name: "BA Psychology", icon: "fa-head-side-virus", mentors: 28, skills: 20, rating: 4.6 },
                { id: "clinical-psych", name: "MA Clinical Psychology", icon: "fa-user-doctor", mentors: 10, skills: 12, rating: 4.8 }
            ],
            skills: ["Cognitive Psychology", "Developmental Psychology", "Social Psychology", "Abnormal Psychology", "Clinical Psychology", "Counseling", "Psychological Assessment", "Behavioral Therapy", "Research Methods", "Statistics", "SPSS", "Psychometrics", "Human Behavior", "Motivation", "Emotional Intelligence", "Stress Management", "Interviewing", "Testing", "Observation", "Case Study", "Psychotherapy", "Ethics", "Internship Preparation", "Final Project"]
        },
        "engineering-architecture": {
            id: "engineering-architecture", name: "Engineering & Technology", icon: "fa-gears", color: "#3b82f6", description: "Broad engineering and technology disciplines", mentors: 150, skills: 72,
            courses: [
                { id: "aerospace-engineering", name: "Aerospace Engineering", icon: "fa-rocket", mentors: 25, skills: 20, rating: 4.8 },
                { id: "chemical-engineering", name: "Chemical Engineering", icon: "fa-flask", mentors: 25, skills: 18, rating: 4.6 },
                { id: "petroleum-engineering", name: "Petroleum Engineering", icon: "fa-oil-can", mentors: 15, skills: 14, rating: 4.7 },
                { id: "production-engineering", name: "Production Engineering", icon: "fa-industry", mentors: 18, skills: 14, rating: 4.4 }
            ],
            skills: ["Engineering Design", "CAD", "Simulation", "Project Management", "Thermodynamics", "Fluid Mechanics", "Materials Science", "Manufacturing", "Automation", "Robotics", "Matlab", "Programming", "Data Analysis", "Systems Engineering", "Safety Engineering", "Quality Control", "Technical Report", "Teamwork", "Problem Solving", "Final Year Project", "Internship Preparation"]
        }
    };

    // ============================================
    // MENTOR DATA (Sample)
    // ============================================
    const discMentorsData = [
        { id: 1, name: "John Doe", title: "Senior Software Engineer", company: "Google", avatar: "JD", color: "purple", rating: 4.9, reviews: 156, students: 342, experience: "6 years", skills: ["JavaScript", "React", "Node.js", "Python"], price: 45, online: true, verified: true, field: "information-technology", course: "software-engineering" },
        { id: 2, name: "Sarah Miller", title: "Full Stack Developer", company: "Microsoft", avatar: "SM", color: "blue", rating: 4.8, reviews: 128, students: 289, experience: "5 years", skills: ["PHP", "Laravel", "Vue.js", "MySQL"], price: 40, online: true, verified: true, field: "information-technology", course: "bca" },
        { id: 3, name: "Alex Kumar", title: "Data Scientist", company: "Amazon", avatar: "AK", color: "green", rating: 4.9, reviews: 142, students: 310, experience: "7 years", skills: ["Python", "Machine Learning", "Data Science", "SQL"], price: 55, online: false, verified: true, field: "information-technology", course: "data-science" },
        { id: 4, name: "Priya Sharma", title: "DevOps Engineer", company: "Netflix", avatar: "PS", color: "pink", rating: 4.7, reviews: 98, students: 210, experience: "4 years", skills: ["Docker", "Kubernetes", "AWS", "Linux"], price: 50, online: true, verified: true, field: "information-technology", course: "computer-engineering" },
        { id: 5, name: "Mike Roberts", title: "Civil Engineer", company: "AECOM", avatar: "MR", color: "orange", rating: 4.6, reviews: 85, students: 175, experience: "8 years", skills: ["AutoCAD", "Revit", "Structural Analysis", "STAAD Pro"], price: 35, online: false, verified: true, field: "civil-engineering", course: "be-civil" },
        { id: 6, name: "Lisa Thompson", title: "Architect", company: "Foster + Partners", avatar: "LT", color: "teal", rating: 4.8, reviews: 112, students: 230, experience: "6 years", skills: ["AutoCAD", "SketchUp", "Revit", "3ds Max"], price: 42, online: true, verified: true, field: "architecture", course: "bachelor-architecture" }
    ];

    // ============================================
    // STATE
    // ============================================
    let discCurrentStep = 1;
    let discSelectedField = null;
    let discSelectedCourse = null;
    let discSelectedSkill = null;
    let discCurrentFilter = 'all';
    let discSearchQuery = '';

    // ============================================
    // RENDER FUNCTIONS
    // ============================================
    function discRenderFields() {
        const container = document.getElementById('discContentContainer');
        const fields = Object.values(discFieldsData);

        let filtered = fields;
        if (discCurrentFilter !== 'all') {
            filtered = filtered.filter(function (f) {
                if (discCurrentFilter === 'it') return f.id === 'information-technology' || f.id === 'engineering-architecture';
                if (discCurrentFilter === 'engineering') return ['computer-engineering', 'civil-engineering', 'mechanical-engineering', 'electrical-engineering', 'electronics-engineering', 'architecture'].indexOf(f.id) !== -1;
                if (discCurrentFilter === 'management') return ['business-administration', 'management', 'commerce', 'economics', 'tourism-hospitality'].indexOf(f.id) !== -1;
                if (discCurrentFilter === 'science') return f.id === 'science' || f.id === 'mathematics' || f.id === 'agriculture' || f.id === 'veterinary' || f.id === 'pharmacy';
                return true;
            });
        }
        if (discSearchQuery) {
            const q = discSearchQuery.toLowerCase();
            filtered = filtered.filter(function (f) {
                return f.name.toLowerCase().indexOf(q) !== -1 || f.description.toLowerCase().indexOf(q) !== -1;
            });
        }

        if (filtered.length === 0) {
            container.innerHTML = '<div class="disc-empty-state"><i class="fas fa-search"></i><h4>No fields found</h4><p>Try adjusting your search or filters</p></div>';
            return;
        }

        container.innerHTML = '<div class="disc-fields-grid">' + filtered.map(function (field) {
            return '<div class="disc-field-card" onclick="discSelectField(\'' + field.id + '\')">' +
                '<div class="df-icon" style="color:' + field.color + '"><i class="fas ' + field.icon + '"></i></div>' +
                '<div class="df-name">' + field.name + '</div>' +
                '<div class="df-desc">' + field.description + '</div>' +
                '<div class="df-stats"><span><i class="fas fa-users"></i> <span class="value">' + field.mentors + '</span> Mentors</span><span><i class="fas fa-tools"></i> <span class="value">' + field.skills + '</span> Skills</span></div>' +
                '<div class="df-arrow"><i class="fas fa-chevron-right"></i></div></div>';
        }).join('') + '</div>';

        discUpdateBreadcrumb('Academic Fields');
        discUpdateStep(1);
        document.getElementById('discPageTitle').textContent = 'Discover Academic Fields';
        document.getElementById('discPageSubtitle').textContent = 'Choose your academic field to find the right courses and mentors';
    }

    function discRenderCourses(fieldId) {
        const field = discFieldsData[fieldId];
        if (!field) return;
        const container = document.getElementById('discContentContainer');

        container.innerHTML = '<button class="disc-back-btn" onclick="discResetApp()"><i class="fas fa-arrow-left"></i> Back to Fields</button>' +
            '<div class="disc-section-title"><i class="fas ' + field.icon + '" style="color:' + field.color + '"></i> ' + field.name + ' <span class="count">(' + field.courses.length + ' courses)</span></div>' +
            '<div class="disc-courses-grid">' + field.courses.map(function (course) {
                return '<div class="disc-course-card" onclick="discSelectCourse(\'' + field.id + '\', \'' + course.id + '\')">' +
                    '<div class="dc-icon" style="color:' + field.color + '"><i class="fas ' + course.icon + '"></i></div>' +
                    '<div class="dc-name">' + course.name + '</div>' +
                    '<div class="dc-meta"><span><i class="fas fa-users"></i> ' + course.mentors + ' Mentors</span><span><i class="fas fa-tools"></i> ' + course.skills + ' Skills</span><span><i class="fas fa-star" style="color:#fbbf24;"></i> ' + course.rating + '</span></div></div>';
            }).join('') + '</div>';

        discUpdateBreadcrumb(field.name);
        discUpdateStep(2);
        document.getElementById('discPageTitle').textContent = field.name + ' Courses';
        document.getElementById('discPageSubtitle').textContent = 'Select a course to find the right skills and mentors';
    }

    function discRenderSkills(fieldId, courseId) {
        const field = discFieldsData[fieldId];
        if (!field) return;
        const course = field.courses.find(function (c) { return c.id === courseId; });
        if (!course) return;
        const container = document.getElementById('discContentContainer');

        let skills = field.skills || [];
        if (discSearchQuery) {
            const q = discSearchQuery.toLowerCase();
            skills = skills.filter(function (s) { return s.toLowerCase().indexOf(q) !== -1; });
        }

        container.innerHTML = '<button class="disc-back-btn" onclick="discSelectField(\'' + fieldId + '\')"><i class="fas fa-arrow-left"></i> Back to Courses</button>' +
            '<div class="disc-section-title"><i class="fas fa-tools" style="color:' + field.color + '"></i> ' + course.name + ' - Skills <span class="count">(' + skills.length + ' skills)</span></div>' +
            '<p style="color:var(--text-secondary);margin-bottom:16px;">What do you need help with today?</p>' +
            '<div class="disc-search-box" style="margin-bottom:16px;"><i class="fas fa-search"></i><input type="text" placeholder="Search skills..." oninput="discSearchSkills(\'' + fieldId + '\', \'' + courseId + '\')" id="discSkillSearch"></div>' +
            '<div class="disc-skills-grid">' + skills.map(function (skill) {
                return '<div class="disc-skill-card" onclick="discSelectSkill(\'' + fieldId + '\', \'' + courseId + '\', \'' + skill.replace(/'/g, "\\'") + '\')">' +
                    '<div class="ds-icon" style="color:' + field.color + '"><i class="fas fa-code"></i></div>' +
                    '<div class="ds-name">' + skill + '</div><span class="ds-count">12 Mentors</span></div>';
            }).join('') + '</div>';

        discUpdateBreadcrumb(field.name + ' > ' + course.name);
        discUpdateStep(3);
        document.getElementById('discPageTitle').textContent = course.name + ' - Skills';
        document.getElementById('discPageSubtitle').textContent = 'Select a skill to find the right mentors';
    }

    function discRenderMentors(fieldId, courseId, skill) {
        const field = discFieldsData[fieldId];
        if (!field) return;
        const course = field.courses.find(function (c) { return c.id === courseId; });
        if (!course) return;
        const container = document.getElementById('discContentContainer');

        let mentors = discMentorsData.filter(function (m) {
            return m.field === fieldId && m.course === courseId && m.skills.some(function (s) { return s.toLowerCase().indexOf(skill.toLowerCase()) !== -1; });
        });
        if (mentors.length === 0) {
            mentors = discMentorsData.filter(function (m) { return m.field === fieldId && m.course === courseId; });
        }

        if (mentors.length === 0) {
            container.innerHTML = '<button class="disc-back-btn" onclick="discSelectCourse(\'' + fieldId + '\', \'' + courseId + '\')"><i class="fas fa-arrow-left"></i> Back to Skills</button>' +
                '<div class="disc-empty-state"><i class="fas fa-users"></i><h4>No mentors available</h4><p>We\'re adding more mentors for this skill. Please check back later.</p></div>';
            return;
        }

        const stars = function (r) {
            return '★'.repeat(Math.floor(r)) + (r % 1 >= 0.5 ? '½' : '');
        };

        container.innerHTML = '<button class="disc-back-btn" onclick="discSelectCourse(\'' + fieldId + '\', \'' + courseId + '\')"><i class="fas fa-arrow-left"></i> Back to Skills</button>' +
            '<div class="disc-section-title"><i class="fas fa-users" style="color:' + field.color + '"></i> Mentors for "' + skill + '" <span class="count">(' + mentors.length + ' mentors)</span></div>' +
            '<div class="disc-mentors-grid">' + mentors.map(function (mentor) {
                return '<div class="disc-mentor-card" onclick="discShowMentorDetail(' + mentor.id + ')">' +
                    '<div class="dm-top"><div class="dm-avatar"><div class="avatar-placeholder ' + mentor.color + '">' + mentor.avatar + '</div>' + (mentor.verified ? '<div class="verified-badge"><i class="fas fa-check"></i></div>' : '') + '</div>' +
                    '<div class="dm-info"><div class="dm-name">' + mentor.name + (mentor.verified ? '<span class="verified-icon"><i class="fas fa-check-circle"></i></span>' : '') + '</div>' +
                    '<div class="dm-title">' + mentor.title + '</div><div class="dm-company"><i class="fas fa-building"></i> ' + mentor.company + '</div>' +
                    '<div class="dm-rating">' + stars(mentor.rating) + ' <span>(' + mentor.reviews + ' reviews)</span></div></div></div>' +
                    '<div class="dm-badges"><span class="badge experience"><i class="fas fa-briefcase"></i> ' + mentor.experience + '</span><span class="badge"><i class="fas fa-users"></i> ' + mentor.students + ' students</span><span class="badge online"><i class="fas fa-circle"></i> ' + (mentor.online ? 'Online' : 'Offline') + '</span></div>' +
                    '<div class="dm-skills">' + mentor.skills.map(function (s) { return '<span class="skill-tag">' + s + '</span>'; }).join('') + '</div>' +
                    '<div class="dm-footer"><div class="dm-price">$' + mentor.price + ' <span>/ hour</span></div>' +
                    '<div class="dm-actions"><button class="btn btn-primary" onclick="event.stopPropagation();discBookSession(\'' + mentor.name + '\')"><i class="fas fa-calendar-check"></i> Book</button>' +
                    '<button class="btn btn-outline" onclick="event.stopPropagation();showToast(\'Info\', \'Message sent to ' + mentor.name + '\', \'info\')"><i class="fas fa-envelope"></i></button>' +
                    '<button class="btn btn-outline" onclick="event.stopPropagation();showToast(\'Info\', \'' + mentor.name + ' saved!\', \'success\')"><i class="fas fa-heart"></i></button></div></div></div>';
            }).join('') + '</div>';

        discUpdateBreadcrumb(field.name + ' > ' + course.name + ' > ' + skill);
        discUpdateStep(4);
        document.getElementById('discPageTitle').textContent = 'Mentors for "' + skill + '"';
        document.getElementById('discPageSubtitle').textContent = 'Connect with verified mentors who can help you';
    }

    // ============================================
    // NAVIGATION
    // ============================================
    function discSelectField(fieldId) {
        discSelectedField = fieldId;
        discSelectedCourse = null;
        discSelectedSkill = null;
        discRenderCourses(fieldId);
    }

    function discSelectCourse(fieldId, courseId) {
        discSelectedCourse = courseId;
        discSelectedSkill = null;
        discRenderSkills(fieldId, courseId);
    }

    function discSelectSkill(fieldId, courseId, skill) {
        discSelectedSkill = skill;
        discRenderMentors(fieldId, courseId, skill);
    }

    function discResetApp() {
        discSelectedField = null;
        discSelectedCourse = null;
        discSelectedSkill = null;
        discCurrentFilter = 'all';
        discSearchQuery = '';
        const input = document.getElementById('discSearchInput');
        if (input) input.value = '';
        discRenderFields();
        document.querySelectorAll('.disc-filter-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.filter === 'all');
        });
    }

    // ============================================
    // HELPERS
    // ============================================
    function discUpdateBreadcrumb(text) {
        document.getElementById('discBreadcrumbCurrent').textContent = text;
    }

    function discUpdateStep(step) {
        discCurrentStep = step;
        document.querySelectorAll('.disc-step').forEach(function (el) {
            const num = parseInt(el.dataset.step);
            el.classList.remove('active', 'done');
            if (num === step) el.classList.add('active');
            else if (num < step) el.classList.add('done');
        });
    }

    function discHandleSearch() {
        const input = document.getElementById('discSearchInput');
        discSearchQuery = input.value.trim();
        if (discCurrentStep === 1) discRenderFields();
        else if (discCurrentStep === 3 && discSelectedField && discSelectedCourse) discRenderSkills(discSelectedField, discSelectedCourse);
        else if (discCurrentStep === 4 && discSelectedField && discSelectedCourse && discSelectedSkill) discRenderMentors(discSelectedField, discSelectedCourse, discSelectedSkill);
    }

    function discSetFilter(filter) {
        discCurrentFilter = filter;
        document.querySelectorAll('.disc-filter-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.filter === filter);
        });
        if (discCurrentStep === 1) discRenderFields();
    }

    function discSearchSkills(fieldId, courseId) {
        const input = document.getElementById('discSkillSearch');
        discSearchQuery = input.value.trim();
        discRenderSkills(fieldId, courseId);
    }

    function discShowMentorDetail(mentorId) {
        const mentor = discMentorsData.find(function (m) { return m.id === mentorId; });
        if (!mentor) return;
        showToast('👤 Mentor Profile', mentor.name + ' - ' + mentor.title + ' at ' + mentor.company, 'info', 5000);
    }

    function discBookSession(mentorName) {
        showToast('📅 Session Booked!', 'You\'ve booked a session with ' + mentorName + '. Check your email for details.', 'success', 5000);
    }

    // ============================================
    // INIT
    // ============================================
document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.disc-filter-btn[data-filter="all"]').classList.add('active');

        // If coming from academic-filed.php with a selected field + course,
        // auto-select the course so the user can pick a subject (skill) then a mentor.
        const params = new URLSearchParams(window.location.search);
        const presetField = params.get('afield');
        const presetCourse = params.get('acourse');

        if (presetField && presetCourse) {
            const field = Object.values(discFieldsData).find(function (f) { return f.name === presetField; });
            if (field) {
                const course = field.courses.find(function (c) { return c.name === presetCourse; });
                if (course) {
                    // Auto-select field + course -> show skills/subjects step
                    discSelectedField = field.id;
                    discSelectedCourse = course.id;
                    discRenderSkills(field.id, course.id);
                    return;
                }
            }
        }

        discRenderFields();
    });

    console.log('🚀 SkillShare Hub - Academic Field, Course & Mentor Discovery');
</script>
