<?php
session_start();
$page_title = 'Research | SkillShare Hub';
$page_active = 'Research';
include 'frontend/components/platform-header.php';
?>

<!-- ============================================
   RESEARCH PAGE STYLES (styled like academic-field page)
   ============================================ -->
<style>
    /* ---------- Breadcrumb ---------- */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-size: 0.85rem;
        padding: 16px 0 20px;
        flex-wrap: wrap;
    }
    .breadcrumb a { color: var(--text-muted); text-decoration: none; transition: all 0.3s ease; cursor: pointer; }
    .breadcrumb a:hover { color: var(--primary-400); }
    .breadcrumb .separator { color: var(--text-muted); }
    .breadcrumb .current { color: var(--text-secondary); }

    /* ---------- Page Header ---------- */
    .page-header { text-align: center; padding: 10px 0 30px; }
    .page-header h1 {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 2.8rem;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .page-header p { color: var(--text-secondary); font-size: 1.1rem; max-width: 600px; margin: 8px auto 0; }

    /* ---------- Stats Cards (academic style) ---------- */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }
    .stat-card-acad {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-2xl);
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .stat-card-acad:hover { transform: translateY(-6px); background: rgba(255,255,255,0.06); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .stat-card-acad .sc-number { font-family: var(--font-heading); font-weight: 700; font-size: 1.8rem; color: var(--text-primary); line-height: 1.2; }
    .stat-card-acad .sc-label { color: var(--text-muted); font-size: 0.8rem; }
    .stat-card-acad .sc-icon { font-size: 1.6rem; color: var(--primary-400); opacity: 0.6; }

    /* ---------- Search & Filters ---------- */
    .search-filters { display: flex; gap: 12px; margin-bottom: 28px; flex-wrap: wrap; align-items: center; }
    .search-box { flex: 1; min-width: 200px; display: flex; align-items: center; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-full); padding: 8px 16px; transition: all 0.3s ease; }
    .search-box:focus-within { border-color: var(--primary-400); box-shadow: 0 0 0 4px rgba(59,130,246,0.1); }
    .search-box input { flex: 1; background: transparent; border: none; padding: 8px 12px; color: var(--text-primary); font-size: 0.9rem; outline: none; }
    .search-box input::placeholder { color: var(--text-muted); }
    .search-box i { color: var(--text-muted); }

    .filter-group { display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-btn { padding: 8px 18px; border-radius: var(--radius-full); border: 1px solid var(--glass-border); background: transparent; color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; }
    .filter-btn:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .filter-btn.active { background: rgba(59,130,246,0.12); border-color: var(--primary-400); color: var(--primary-400); }

    /* ---------- Research Cards (academic card style) ---------- */
    .research-card-acad {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-2xl);
        padding: 24px 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .research-card-acad::before { content: ''; position: absolute; inset: 0; background: var(--gradient-primary); opacity: 0; transition: all 0.4s ease; }
    .research-card-acad:hover { transform: translateY(-8px) scale(1.02); border-color: var(--primary-400); box-shadow: 0 15px 50px rgba(59,130,246,0.2); }
    .research-card-acad:hover::before { opacity: 0.05; }

    .research-card-acad .rc-top { display: flex; justify-content: space-between; align-items: flex-start; position: relative; z-index: 1; }
    .research-card-acad .rc-icon { width: 48px; height: 48px; border-radius: var(--radius-md); background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--primary-400); transition: all 0.3s ease; }
    .research-card-acad:hover .rc-icon { background: var(--gradient-primary); color: #fff; transform: scale(1.1) rotate(-5deg); }
    .research-card-acad .rc-badge { font-size: 0.6rem; padding: 3px 12px; border-radius: var(--radius-full); background: rgba(59,130,246,0.12); color: var(--primary-400); font-weight: 500; white-space: nowrap; }
    .research-card-acad .rc-badge.science { background: rgba(34,197,94,0.12); color: var(--success); }
    .research-card-acad .rc-badge.health { background: rgba(236,72,153,0.12); color: #ec4899; }
    .research-card-acad .rc-badge.technology { background: rgba(139,92,246,0.12); color: var(--secondary-400); }
    .research-card-acad .rc-badge.business { background: rgba(245,158,11,0.12); color: var(--warning); }

    .research-card-acad .rc-title { font-weight: 700; font-size: 1.05rem; color: var(--text-primary); margin: 10px 0 6px; line-height: 1.4; position: relative; z-index: 1; }
    .research-card-acad .rc-desc { color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; position: relative; z-index: 1; }
    .research-card-acad .rc-authors { display: flex; align-items: center; gap: 8px; margin: 12px 0; flex-wrap: wrap; position: relative; z-index: 1; }
    .research-card-acad .rc-avatar { width: 28px; height: 28px; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 700; color: #fff; flex-shrink: 0; }
    .research-card-acad .rc-avatar.purple { background: #8b5cf6; }
    .research-card-acad .rc-avatar.blue { background: #3b82f6; }
    .research-card-acad .rc-avatar.green { background: #22c55e; }
    .research-card-acad .rc-avatar.pink { background: #ec4899; }
    .research-card-acad .rc-avatar.orange { background: #f59e0b; }
    .research-card-acad .rc-avatar.teal { background: #14b8a6; }
    .research-card-acad .rc-more { color: var(--text-muted); font-size: 0.78rem; }

    .research-card-acad .rc-stats { display: flex; gap: 14px; flex-wrap: wrap; padding: 12px 0; border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border); margin-bottom: 12px; position: relative; z-index: 1; }
    .research-card-acad .rc-stats span { display: inline-flex; align-items: center; gap: 4px; color: var(--text-muted); font-size: 0.75rem; }
    .research-card-acad .rc-stats span i { color: var(--primary-400); }
    .research-card-acad .rc-stats span b { color: var(--text-secondary); font-weight: 500; }

    .research-card-acad .rc-footer { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: auto; position: relative; z-index: 1; }
    .research-card-acad .rc-status { font-size: 0.7rem; color: var(--success); }
    .research-card-acad .rc-btn { padding: 6px 20px; border-radius: var(--radius-full); background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; }
    .research-card-acad .rc-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }

    /* ---------- Empty State ---------- */
    .empty-state { grid-column: 1/-1; text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .empty-state i { font-size: 4rem; display: block; margin-bottom: 16px; opacity: 0.3; }
    .empty-state h4 { color: var(--text-secondary); margin-bottom: 4px; }

    /* ---------- Detail Overlay ---------- */
    .research-overlay-hub { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(20px); z-index: 9999; padding: 40px 20px; overflow-y: auto; align-items: center; justify-content: center; }
    .research-overlay-hub.active { display: flex; animation: rhFadeIn 0.3s ease; }
    @keyframes rhFadeIn { from { opacity: 0; } to { opacity: 1; } }

    .research-detail-box { max-width: 720px; width: 100%; background: rgba(20,20,40,0.95); backdrop-filter: blur(30px); border: 1px solid var(--glass-border); border-radius: var(--radius-2xl); padding: 40px; position: relative; max-height: 90vh; overflow-y: auto; animation: rhSlideUp 0.4s ease; }
    @keyframes rhSlideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    .research-detail-box .rh-close-btn { position: absolute; top: 16px; right: 20px; background: none; border: none; color: var(--text-muted); font-size: 1.4rem; cursor: pointer; transition: all 0.3s ease; }
    .research-detail-box .rh-close-btn:hover { color: var(--text-primary); transform: rotate(90deg); }
    .rh-detail-header { display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 16px; }
    .rh-detail-header .rh-detail-icon { width: 64px; height: 64px; border-radius: var(--radius-md); background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: var(--primary-400); flex-shrink: 0; }
    .rh-detail-header .rh-detail-title { font-family: var(--font-heading); font-weight: 700; font-size: 1.4rem; color: var(--text-primary); }
    .rh-detail-header .rh-detail-type { color: var(--text-secondary); font-size: 0.9rem; }
    .rh-detail-header .rh-detail-type i { color: var(--primary-400); }

    .rh-detail-meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; padding: 16px 0; border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border); margin-bottom: 16px; }
    .rh-detail-meta .rh-meta-item { display: flex; flex-direction: column; gap: 2px; }
    .rh-detail-meta .rh-meta-item .rh-m-label { color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .rh-detail-meta .rh-meta-item .rh-m-value { color: var(--text-primary); font-weight: 500; font-size: 0.9rem; }
    .rh-detail-meta .rh-meta-item .rh-m-value i { color: var(--primary-400); margin-right: 4px; }

    .rh-detail-abstract { color: var(--text-secondary); font-size: 0.95rem; line-height: 1.8; margin-bottom: 16px; }
    .rh-detail-authors { padding: 12px 0; border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border); margin-bottom: 16px; }
    .rh-detail-authors .rh-authors-label { color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .rh-detail-authors .rh-author-list { display: flex; flex-wrap: wrap; gap: 8px; }
    .rh-detail-authors .rh-author-list .rh-author-chip { display: flex; align-items: center; gap: 8px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-full); padding: 4px 14px 4px 4px; font-size: 0.8rem; color: var(--text-secondary); }
    .rh-detail-authors .rh-author-list .rh-author-chip .rh-chip-avatar { width: 24px; height: 24px; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-size: 0.55rem; font-weight: 700; color: #fff; }

    .rh-detail-actions { display: flex; gap: 12px; flex-wrap: wrap; }
    .rh-detail-actions .rh-act-btn { padding: 10px 28px; border-radius: var(--radius-full); font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.3s ease; border: none; }
    .rh-detail-actions .rh-act-primary { background: var(--gradient-primary); color: #fff; }
    .rh-detail-actions .rh-act-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(59,130,246,0.3); }
    .rh-detail-actions .rh-act-outline { background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); }
    .rh-detail-actions .rh-act-outline:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }

    /* ---------- Responsive ---------- */
    @media (max-width: 768px) {
        .search-filters { flex-direction: column; }
        .search-box { width: 100%; }
        .filter-group { justify-content: center; }
        .page-header h1 { font-size: 2rem; }
        .rh-detail-actions { flex-direction: column; }
        .rh-detail-actions .rh-act-btn { width: 100%; text-align: center; }
        .research-detail-box { padding: 24px; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .page-header h1 { font-size: 1.6rem; }
        .research-detail-box { padding: 16px; }
        .rh-detail-header .rh-detail-title { font-size: 1.2rem; }
        .rh-detail-meta { grid-template-columns: 1fr; }
    }
</style>

<div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb reveal">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <span class="separator">/</span>
        <span class="current">Research</span>
    </div>

    <!-- Page Header -->
    <div class="page-header reveal">
        <h1>🔬 Research Hub</h1>
        <p>Explore research papers, articles, and publications from experts</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card-acad">
            <div>
                <div class="sc-number">18</div>
                <div class="sc-label">Research Papers</div>
            </div>
            <div class="sc-icon"><i class="fas fa-file-alt"></i></div>
        </div>
        <div class="stat-card-acad">
            <div>
                <div class="sc-number">6</div>
                <div class="sc-label">Categories</div>
            </div>
            <div class="sc-icon"><i class="fas fa-tags"></i></div>
        </div>
        <div class="stat-card-acad">
            <div>
                <div class="sc-number">2.4k</div>
                <div class="sc-label">Total Downloads</div>
            </div>
            <div class="sc-icon"><i class="fas fa-download"></i></div>
        </div>
        <div class="stat-card-acad">
            <div>
                <div class="sc-number">45</div>
                <div class="sc-label">Active Researchers</div>
            </div>
            <div class="sc-icon"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="search-filters reveal">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="researchSearch" placeholder="Search research..." oninput="handleSearch()">
        </div>
        <div class="filter-group">
            <button class="filter-btn active" data-filter="all" onclick="setFilter('all')">All</button>
            <button class="filter-btn" data-filter="engineering" onclick="setFilter('engineering')">Engineering</button>
            <button class="filter-btn" data-filter="science" onclick="setFilter('science')">Science</button>
            <button class="filter-btn" data-filter="health" onclick="setFilter('health')">Health</button>
            <button class="filter-btn" data-filter="technology" onclick="setFilter('technology')">Technology</button>
            <button class="filter-btn" data-filter="business" onclick="setFilter('business')">Business</button>
            <button class="filter-btn" data-filter="education" onclick="setFilter('education')">Education</button>
        </div>
    </div>

    <!-- Research Grid -->
    <div class="research-grid" id="researchGrid">
        <!-- Rendered by JavaScript -->
    </div>

</div>

<!-- Research Detail Overlay -->
<div class="research-overlay-hub" id="researchOverlay">
    <div class="research-detail-box" id="researchDetail">
        <button class="rh-close-btn" onclick="closeResearchDetail()"><i class="fas fa-times"></i></button>
        <div id="researchDetailContent"></div>
    </div>
</div>

<script>
    // ============================================
    // RESEARCH DATA
    // ============================================
    const researchData = [
        { id: 1, title: "Artificial Intelligence in Education: Transforming Learning Experiences", type: "Research Paper", category: "technology", categoryLabel: "Technology", icon: "fa-brain", description: "This paper explores the impact of AI on modern education, including personalized learning, intelligent tutoring systems, and automated assessment.", abstract: "Artificial Intelligence is revolutionizing education by enabling personalized learning paths, intelligent tutoring systems, and automated assessment. This research paper examines the current state of AI in education, its benefits, challenges, and future potential.", authors: ["Dr. Sarah Miller", "Prof. John Anderson", "Dr. Emily Chen"], publishedDate: "2026-01-15", views: 342, downloads: 189, doi: "10.1234/ai-edu.2026.001", status: "Published" },
        { id: 2, title: "Sustainable Engineering: Green Technologies for Future Cities", type: "Research Article", category: "engineering", categoryLabel: "Engineering", icon: "fa-leaf", description: "A comprehensive study on sustainable engineering practices and green technologies for urban development.", abstract: "This research article presents a comprehensive analysis of sustainable engineering practices and green technologies for urban development. It covers renewable energy systems, waste management, sustainable transportation, and green building design.", authors: ["Prof. Michael Roberts", "Dr. Lisa Thompson", "Eng. David Park"], publishedDate: "2026-02-10", views: 287, downloads: 156, doi: "10.1234/sus-eng.2026.002", status: "Published" },
        { id: 3, title: "Machine Learning for Medical Diagnosis: A Systematic Review", type: "Review Paper", category: "health", categoryLabel: "Health", icon: "fa-heartbeat", description: "A systematic review of machine learning applications in medical diagnosis and healthcare systems.", abstract: "This systematic review examines the applications of machine learning in medical diagnosis and healthcare systems. It covers various ML algorithms used for disease detection, medical image analysis, and predictive healthcare.", authors: ["Dr. Priya Sharma", "Prof. Robert Chen", "Dr. Maria Garcia"], publishedDate: "2026-01-28", views: 421, downloads: 234, doi: "10.1234/ml-med.2026.003", status: "Published" },
        { id: 4, title: "Blockchain Technology in Supply Chain Management", type: "Research Paper", category: "technology", categoryLabel: "Technology", icon: "fa-link", description: "Exploring the implementation of blockchain technology for transparent and secure supply chain management.", abstract: "This research paper explores the implementation of blockchain technology for transparent and secure supply chain management. It discusses the benefits of blockchain for traceability, authentication, and fraud prevention in supply chains.", authors: ["Dr. James Wilson", "Prof. Anna Martinez", "Eng. Tom Lee"], publishedDate: "2026-02-05", views: 198, downloads: 102, doi: "10.1234/blockchain.2026.004", status: "Published" },
        { id: 5, title: "Climate Change and Agricultural Sustainability", type: "Research Article", category: "science", categoryLabel: "Science", icon: "fa-globe", description: "Analyzing the impact of climate change on agricultural sustainability and food security.", abstract: "This research article analyzes the impact of climate change on agricultural sustainability and food security. It examines the effects of rising temperatures, changing precipitation patterns, and extreme weather events on crop production.", authors: ["Dr. Emma Brown", "Prof. Richard Green", "Dr. Sophia Kim"], publishedDate: "2026-01-20", views: 256, downloads: 143, doi: "10.1234/climate-agri.2026.005", status: "Published" },
        { id: 6, title: "Digital Marketing Strategies for Small Businesses", type: "Research Paper", category: "business", categoryLabel: "Business", icon: "fa-chart-line", description: "A comprehensive analysis of effective digital marketing strategies for small businesses.", abstract: "This research paper provides a comprehensive analysis of effective digital marketing strategies for small businesses. It covers social media marketing, content marketing, SEO, and digital advertising strategies.", authors: ["Prof. David Johnson", "Dr. Michelle Williams", "Eng. Sarah Brown"], publishedDate: "2026-02-18", views: 312, downloads: 178, doi: "10.1234/digital-marketing.2026.006", status: "Published" },
        { id: 7, title: "Innovative Teaching Methods in Higher Education", type: "Research Article", category: "education", categoryLabel: "Education", icon: "fa-graduation-cap", description: "Exploring innovative teaching methods and their effectiveness in higher education.", abstract: "This research article explores innovative teaching methods and their effectiveness in higher education. It examines active learning, flipped classrooms, project-based learning, and technology-enhanced teaching approaches.", authors: ["Dr. Laura Adams", "Prof. Thomas Moore", "Dr. Rebecca Taylor"], publishedDate: "2026-01-08", views: 178, downloads: 95, doi: "10.1234/teaching.2026.007", status: "Published" },
        { id: 8, title: "Quantum Computing: Current State and Future Prospects", type: "Review Paper", category: "technology", categoryLabel: "Technology", icon: "fa-atom", description: "A comprehensive review of quantum computing technology, its current capabilities, and future applications.", abstract: "This review paper provides a comprehensive overview of quantum computing technology, its current capabilities, and future applications. It covers quantum algorithms, hardware development, and potential use cases.", authors: ["Dr. Alex Johnson", "Prof. William Chen", "Dr. Maria Lopez"], publishedDate: "2026-02-22", views: 534, downloads: 312, doi: "10.1234/quantum.2026.008", status: "Published" },
        { id: 9, title: "Renewable Energy Systems for Developing Countries", type: "Research Paper", category: "engineering", categoryLabel: "Engineering", icon: "fa-solar-panel", description: "Analyzing the implementation of renewable energy systems in developing countries.", abstract: "This research paper analyzes the implementation of renewable energy systems in developing countries. It examines the challenges, opportunities, and best practices for deploying solar, wind, and hydro power systems.", authors: ["Eng. Robert Park", "Dr. Susan Wang", "Prof. Carlos Gomez"], publishedDate: "2026-01-25", views: 245, downloads: 134, doi: "10.1234/renewable.2026.009", status: "Published" },
        { id: 10, title: "Healthcare Analytics: Big Data for Better Patient Outcomes", type: "Research Article", category: "health", categoryLabel: "Health", icon: "fa-chart-pie", description: "Exploring the use of big data analytics in healthcare for improved patient outcomes.", abstract: "This research article explores the use of big data analytics in healthcare for improved patient outcomes. It discusses predictive analytics, population health management, and clinical decision support systems.", authors: ["Dr. Jane Smith", "Prof. Mark Davis", "Dr. Anna Kim"], publishedDate: "2026-02-14", views: 367, downloads: 201, doi: "10.1234/health-analytics.2026.010", status: "Published" }
    ];

    // ============================================
    // STATE
    // ============================================
    let currentFilter = 'all';
    let searchQuery = '';
    const avatarColors = ['purple', 'blue', 'green', 'pink', 'orange', 'teal'];

    // ============================================
    // HELPERS
    // ============================================
    function formatDate(dateString) {
        const date = new Date(dateString);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
    }

    function initials(name) {
        return name.split(' ').map(function (w) { return w[0]; }).join('');
    }

    // ============================================
    // RENDER
    // ============================================
    function renderResearch() {
        const grid = document.getElementById('researchGrid');
        let filtered = researchData;

        if (currentFilter !== 'all') {
            filtered = filtered.filter(function (r) { return r.category === currentFilter; });
        }
        if (searchQuery) {
            const q = searchQuery.toLowerCase();
            filtered = filtered.filter(function (r) {
                return r.title.toLowerCase().indexOf(q) !== -1 ||
                    r.description.toLowerCase().indexOf(q) !== -1 ||
                    r.authors.some(function (a) { return a.toLowerCase().indexOf(q) !== -1; }) ||
                    r.categoryLabel.toLowerCase().indexOf(q) !== -1;
            });
        }

        if (filtered.length === 0) {
            grid.innerHTML = '<div class="empty-state"><i class="fas fa-search"></i><h4>No research found</h4><p>Try adjusting your search or filters</p></div>';
            return;
        }

        grid.innerHTML = filtered.map(function (research, index) {
            return '<div class="research-card-acad" onclick="openResearchDetail(' + research.id + ')">' +
                '<div class="rc-top"><div class="rc-icon"><i class="fas ' + research.icon + '"></i></div><span class="rc-badge ' + research.category + '">' + research.categoryLabel + '</span></div>' +
                '<div class="rc-title">' + research.title + '</div>' +
                '<div class="rc-desc">' + research.description + '</div>' +
                '<div class="rc-authors">' +
                research.authors.slice(0, 3).map(function (a, i) { return '<div class="rc-avatar ' + avatarColors[i % avatarColors.length] + '">' + initials(a) + '</div>'; }).join('') +
                (research.authors.length > 3 ? '<span class="rc-more">+' + (research.authors.length - 3) + ' more</span>' : '') +
                '</div>' +
                '<div class="rc-stats">' +
                '<span><i class="far fa-calendar-alt"></i> <b>' + formatDate(research.publishedDate) + '</b></span>' +
                '<span><i class="fas fa-eye"></i> <b>' + research.views + '</b></span>' +
                '<span><i class="fas fa-download"></i> <b>' + research.downloads + '</b></span>' +
                '<span><i class="fas fa-link"></i> <b>' + research.doi + '</b></span>' +
                '</div>' +
                '<div class="rc-footer">' +
                '<span class="rc-status"><i class="fas fa-check-circle"></i> ' + research.status + '</span>' +
                '<button class="rc-btn" onclick="event.stopPropagation();openResearchDetail(' + research.id + ')"><i class="fas fa-arrow-right"></i> Read More</button>' +
                '</div></div>';
        }).join('');
    }

    // ============================================
    // SEARCH & FILTER
    // ============================================
    function handleSearch() {
        const input = document.getElementById('researchSearch');
        searchQuery = input.value.trim();
        renderResearch();
    }

    function setFilter(filter) {
        currentFilter = filter;
        document.querySelectorAll('.filter-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.filter === filter);
        });
        renderResearch();
    }

    // ============================================
    // DETAIL
    // ============================================
    function openResearchDetail(researchId) {
        const research = researchData.find(function (r) { return r.id === researchId; });
        if (!research) return;

        const overlay = document.getElementById('researchOverlay');
        const content = document.getElementById('researchDetailContent');

        content.innerHTML = '<div class="rh-detail-header">' +
            '<div class="rh-detail-icon"><i class="fas ' + research.icon + '"></i></div>' +
            '<div><div class="rh-detail-title">' + research.title + '</div>' +
            '<div class="rh-detail-type"><i class="fas fa-tag"></i> ' + research.type + ' • ' + research.categoryLabel + '</div></div>' +
            '</div>' +
            '<div class="rh-detail-meta">' +
            '<div class="rh-meta-item"><span class="rh-m-label">Published</span><span class="rh-m-value"><i class="far fa-calendar-alt"></i> ' + formatDate(research.publishedDate) + '</span></div>' +
            '<div class="rh-meta-item"><span class="rh-m-label">Views</span><span class="rh-m-value"><i class="fas fa-eye"></i> ' + research.views + '</span></div>' +
            '<div class="rh-meta-item"><span class="rh-m-label">Downloads</span><span class="rh-m-value"><i class="fas fa-download"></i> ' + research.downloads + '</span></div>' +
            '<div class="rh-meta-item"><span class="rh-m-label">DOI</span><span class="rh-m-value"><i class="fas fa-link"></i> ' + research.doi + '</span></div>' +
            '<div class="rh-meta-item"><span class="rh-m-label">Status</span><span class="rh-m-value" style="color:var(--success);"><i class="fas fa-check-circle"></i> ' + research.status + '</span></div>' +
            '<div class="rh-meta-item"><span class="rh-m-label">Type</span><span class="rh-m-value"><i class="fas fa-file-alt"></i> ' + research.type + '</span></div>' +
            '</div>' +
            '<div class="rh-detail-abstract">' + research.abstract + '</div>' +
            '<div class="rh-detail-authors"><div class="rh-authors-label"><i class="fas fa-users"></i> Authors</div><div class="rh-author-list">' +
            research.authors.map(function (author, i) { return '<div class="rh-author-chip"><div class="rh-chip-avatar ' + avatarColors[i % avatarColors.length] + '">' + initials(author) + '</div>' + author + '</div>'; }).join('') +
            '</div></div>' +
            '<div class="rh-detail-actions">' +
            '<button class="rh-act-btn rh-act-primary" onclick="downloadResearch(\'' + research.title + '\')"><i class="fas fa-download"></i> Download PDF</button>' +
            '<button class="rh-act-btn rh-act-outline" onclick="citeResearch(\'' + research.title + '\')"><i class="fas fa-quote-right"></i> Cite</button>' +
            '<button class="rh-act-btn rh-act-outline" onclick="showToast(\'Info\', \'Research shared!\', \'info\')"><i class="fas fa-share-alt"></i> Share</button>' +
            '<button class="rh-act-btn rh-act-outline" onclick="showToast(\'Info\', \'Research bookmarked!\', \'info\')"><i class="fas fa-bookmark"></i> Bookmark</button>' +
            '</div>';

        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeResearchDetail() {
        const overlay = document.getElementById('researchOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // ============================================
    // ACTIONS
    // ============================================
    function downloadResearch(title) {
        showToast('📄 Downloading', 'Downloading "' + title + '"...', 'success', 3000);
    }

    function citeResearch(title) {
        showToast('📝 Cite', 'Citation for "' + title + '" copied to clipboard!', 'info', 3000);
    }

    // ============================================
    // INIT
    // ============================================
    document.addEventListener('DOMContentLoaded', function () {
        renderResearch();

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeResearchDetail();
        });

        const overlay = document.getElementById('researchOverlay');
        if (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === this) closeResearchDetail();
            });
        }
    });

    console.log('🔬 ShareSkill Hub - Research Hub Page Loaded');
    console.log('📊 ' + researchData.length + ' Research Papers Available');
</script>

<?php include 'frontend/components/platform-footer.php'; ?>

