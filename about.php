<?php
session_start();
$page_title = 'About Us | SkillShare Hub';
$page_active = 'About';
include 'frontend/components/platform-header.php';
?>

<!-- ============================================
   ABOUT PAGE STYLES (scoped, full-width, attractive)
   ============================================ -->
<style>
.about-shell {
        width: 100%;
        max-width: none;
        box-sizing: border-box;
        padding: 0 35px 20px;
        margin: 0;
    }

    .about-shell > * { margin-bottom: 0; }

    /* ---------- About Hero ---------- */
    .about-hero {
        position: relative;
        margin: 2px 0 8px;
        padding: 24px 36px;
        border-radius: var(--radius-2xl);
        background:
            radial-gradient(ellipse at 85% 20%, rgba(139,92,246,0.25) 0%, transparent 55%),
            radial-gradient(ellipse at 10% 90%, rgba(59,130,246,0.2) 0%, transparent 55%),
            linear-gradient(135deg, rgba(59,130,246,0.12), rgba(139,92,246,0.12));
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(20px);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 30px;
        align-items: center;
        box-shadow: var(--shadow-lg);
    }
    .about-hero::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        border: 30px solid rgba(139,92,246,0.08);
    }
    .about-hero::after {
        content: '';
        position: absolute;
        bottom: -60px;
        left: 30%;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        border: 24px solid rgba(59,130,246,0.08);
    }
    .about-hero .ah-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(59,130,246,0.12);
        border: 1px solid rgba(59,130,246,0.25);
        color: var(--primary-400);
        padding: 5px 16px;
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 14px;
    }
    .about-hero h1 {
        font-family: var(--font-heading);
        font-weight: 900;
        font-size: 2.4rem;
        line-height: 1.12;
        background: linear-gradient(135deg, #ffffff 0%, #60a5fa 60%, #a78bfa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 12px;
    }
    .about-hero p {
        color: var(--text-secondary);
        font-size: 1.05rem;
        line-height: 1.7;
        max-width: 560px;
        margin: 0;
    }
    .about-hero .ah-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 14px;
    }
    .about-hero .ah-stat {
        text-align: center;
        padding: 16px 10px;
        border-radius: var(--radius-lg);
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        transition: all 0.3s ease;
    }
    .about-hero .ah-stat:hover { transform: translateY(-4px); border-color: var(--border-hover); }
    .about-hero .ah-stat .num {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 1.7rem;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .about-hero .ah-stat .lbl { color: var(--text-muted); font-size: 0.75rem; }
    .about-hero .ah-visual {
        position: relative;
        z-index: 2;
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-2xl);
        padding: 18px;
        text-align: center;
        animation: ahFloat 6s ease-in-out infinite;
    }
    .about-hero .ah-visual .vf-icon {
        width: 90px;
        height: 90px;
        margin: 0 auto 16px;
        border-radius: var(--radius-full);
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.4rem;
        color: #fff;
        box-shadow: 0 18px 40px rgba(59,130,246,0.4);
    }
    .about-hero .ah-visual h4 { color: var(--text-primary); font-family: var(--font-heading); font-weight: 700; font-size: 1.2rem; }
    .about-hero .ah-visual p { color: var(--text-muted); font-size: 0.9rem; }
    @keyframes ahFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-14px); } }

    /* ---------- Section Heading ---------- */
    .about-sect-head { margin: 8px 0 10px; }
    .about-sect-head h2 {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 1.9rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .about-sect-head h2 .h-ico {
        width: 40px; height: 40px;
        border-radius: var(--radius-md);
        background: var(--gradient-primary);
        display: inline-flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.1rem;
        box-shadow: 0 8px 24px rgba(59,130,246,0.35);
    }
    .about-sect-head p { color: var(--text-muted); font-size: 0.95rem; margin: 6px 0 0; }
    .about-sect-head .h-line { width: 60px; height: 4px; background: var(--gradient-primary); border-radius: var(--radius-full); margin-top: 10px; }

    /* ---------- MVV Cards (3 col) ---------- */
    .mvv-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 2px;
    }
    .mvv-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        padding: 28px 24px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .mvv-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 4px; background: var(--gradient-primary);
        opacity: 0; transition: all 0.3s ease;
    }
    .mvv-card:hover { transform: translateY(-8px); background: rgba(255,255,255,0.07); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .mvv-card:hover::before { opacity: 1; }
    .mvv-card .mv-icon {
        width: 64px; height: 64px; margin: 0 auto 16px;
        border-radius: var(--radius-lg);
        background: rgba(59,130,246,0.12);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; color: var(--primary-400);
        transition: all 0.3s ease;
    }
    .mvv-card:hover .mv-icon { background: var(--gradient-primary); color: #fff; transform: scale(1.1) rotate(-5deg); }
    .mvv-card h5 { color: var(--text-primary); font-family: var(--font-heading); font-weight: 700; font-size: 1.15rem; margin-bottom: 8px; }
    .mvv-card p { color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin: 0; }

    /* ---------- Generic cards (What we do / Why choose / How it works) ---------- */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 2px;
    }
    .info-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        padding: 26px 22px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .info-card:hover { transform: translateY(-8px); background: rgba(255,255,255,0.07); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .info-card .ic-icon {
        width: 58px; height: 58px; margin-bottom: 14px;
        border-radius: var(--radius-lg);
        background: rgba(139,92,246,0.12);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; color: var(--secondary-400);
        transition: all 0.3s ease;
    }
    .info-card:hover .ic-icon { background: var(--gradient-primary); color: #fff; transform: scale(1.1) rotate(-5deg); }
    .info-card h5 { color: var(--text-primary); font-family: var(--font-heading); font-weight: 700; font-size: 1.05rem; margin-bottom: 8px; }
    .info-card p { color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin: 0; flex: 1; }

    /* ---------- How it works step cards ---------- */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 2px;
    }
    .step-card {
        position: relative;
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        padding: 28px 22px 22px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
    }
    .step-card:hover { transform: translateY(-8px); background: rgba(255,255,255,0.07); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .step-card .step-num {
        width: 52px; height: 52px; margin: 0 auto 14px;
        border-radius: var(--radius-full);
        background: var(--gradient-primary);
        color: #fff; font-family: var(--font-heading);
        font-weight: 800; font-size: 1.4rem;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 10px 26px rgba(59,130,246,0.36);
    }
    .step-card .sc-ico { color: var(--primary-400); font-size: 1.2rem; margin-bottom: 10px; }
    .step-card h5 { color: var(--text-primary); font-family: var(--font-heading); font-weight: 700; font-size: 1.05rem; margin-bottom: 8px; }
    .step-card p { color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin: 0; }
    .step-card::after {
        content: '\f054';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: -14px; top: 50%; transform: translateY(-50%);
        color: var(--primary-400); font-size: 0.9rem; z-index: 3;
    }
    .step-card:last-child::after { display: none; }

    /* ---------- Team ---------- */
    .team-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; margin-bottom: 2px; }
    .team-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        padding: 26px 24px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .team-card:hover { transform: translateY(-6px); background: rgba(255,255,255,0.07); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .team-card .tm-avatar {
        width: 80px; height: 80px; margin: 0 auto 14px;
        border-radius: var(--radius-full);
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-heading); font-weight: 700; font-size: 1.6rem; color: #fff;
        box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    }
    .team-card h5 { color: var(--text-primary); font-family: var(--font-heading); font-weight: 700; }
    .team-card .tm-role { color: var(--primary-400); font-size: 0.85rem; margin-bottom: 14px; }
    .team-card .tm-btn {
        padding: 7px 22px; border-radius: var(--radius-full);
        background: transparent; border: 1px solid var(--glass-border);
        color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease;
    }
    .team-card .tm-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }

    /* ---------- CTA ---------- */
    .about-cta { margin-top: 0; }
    .about-cta .cta-section { margin-top: 0; }

    /* ---------- Responsive ---------- */
    @media (max-width: 1199px) {
        .about-shell { padding: 0 24px 30px; }
        .info-grid, .steps-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .about-hero { grid-template-columns: 1fr; text-align: center; }
        .about-hero p { margin: 0 auto; }
        .about-hero .ah-stats { max-width: 480px; margin-left: auto; margin-right: auto; }
        .about-hero .ah-visual { max-width: 420px; margin: 0 auto; }
    }
    @media (max-width: 767px) {
        .about-shell { padding: 0 16px 24px; }
        .mvv-grid, .info-grid, .steps-grid, .team-grid { grid-template-columns: 1fr; }
        .about-hero { padding: 30px 20px; }
        .about-hero h1 { font-size: 2.1rem; }
        .about-hero .ah-stats { grid-template-columns: repeat(3, 1fr); }
        .about-hero .ah-stat .num { font-size: 1.3rem; }
        .about-sect-head h2 { font-size: 1.5rem; }
        .step-card::after { display: none; }
    }
    @media (max-width: 480px) {
        .about-hero .ah-stats { grid-template-columns: 1fr; }
    }
</style>

<div class="about-shell">

    <!-- ============================================
       ABOUT HERO
       ============================================ -->
    <section class="about-hero reveal">
        <div>
            <span class="ah-badge"><i class="fa-solid fa-graduation-cap"></i> About SkillShare Hub</span>
            <h1>Bridging Education with Industry Through Real Mentorship</h1>
            <p>SkillShare Hub connects passionate graduates with aspiring freshers, delivering practical learning through one-on-one mentorship, hands-on projects, and expert guidance across every academic field.</p>
            <div class="ah-stats">
                <div class="ah-stat"><div class="num">30+</div><div class="lbl">Academic Fields</div></div>
                <div class="ah-stat"><div class="num">200+</div><div class="lbl">Courses</div></div>
                <div class="ah-stat"><div class="num">2.4k+</div><div class="lbl">Learners</div></div>
            </div>
        </div>
        <div class="ah-visual">
            <div class="vf-icon"><i class="fa-solid fa-rocket"></i></div>
            <h4>Learn. Connect. Grow.</h4>
            <p>Your journey from student to industry-ready professional</p>
        </div>
    </section>

    <!-- ============================================
       MISSION / VISION / VALUES (3 columns)
       ============================================ -->
    <div class="about-sect-head reveal">
        <h2><span class="h-ico"><i class="fa-solid fa-compass"></i></span> Our Direction</h2>
        <div class="h-line"></div>
        <p>What drives everything we do at SkillShare Hub</p>
    </div>
    <div class="mvv-grid">
        <div class="mvv-card reveal">
            <div class="mv-icon"><i class="fa-solid fa-rocket"></i></div>
            <h5>Our Mission</h5>
            <p>To bridge the gap between theoretical education and industry skills by connecting graduates with freshers through practical learning and mentorship.</p>
        </div>
        <div class="mvv-card reveal">
            <div class="mv-icon"><i class="fa-solid fa-eye"></i></div>
            <h5>Our Vision</h5>
            <p>To create a world where every student has access to practical learning and mentorship, becoming industry-ready before graduation.</p>
        </div>
        <div class="mvv-card reveal">
            <div class="mv-icon"><i class="fa-solid fa-handshake"></i></div>
            <h5>Our Values</h5>
            <p>We believe in Learn, Connect, Grow. We value practical knowledge, community building, and empowering students to build successful careers.</p>
        </div>
    </div>

    <!-- ============================================
       HOW IT WORKS (4 steps)
       ============================================ -->
    <div class="about-sect-head reveal">
        <h2><span class="h-ico"><i class="fa-solid fa-route"></i></span> How SkillShare Hub Works</h2>
        <div class="h-line"></div>
        <p>Your guided pathway from choosing a field to mastering a skill with a mentor</p>
    </div>
    <div class="steps-grid">
        <div class="step-card reveal">
            <div class="step-num">1</div>
            <div class="sc-ico"><i class="fa-solid fa-layer-group"></i></div>
            <h5>Academic Field</h5>
            <p>Explore 30+ academic fields and choose the one that matches your passion and career goals.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">2</div>
            <div class="sc-ico"><i class="fa-solid fa-graduation-cap"></i></div>
            <h5>Course</h5>
            <p>Drill into the right course within your field — from BCA to Civil Engineering and beyond.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">3</div>
            <div class="sc-ico"><i class="fa-solid fa-book-open"></i></div>
            <h5>Subject / Skill</h5>
            <p>Pick a subject and dive into the specific skills you need to build real-world competency.</p>
        </div>
        <div class="step-card reveal">
            <div class="step-num">4</div>
            <div class="sc-ico"><i class="fa-solid fa-user-tie"></i></div>
            <h5>Mentor</h5>
            <p>Connect with an expert mentor, book sessions, and learn through practical guidance.</p>
        </div>
    </div>

    <!-- ============================================
       WHY CHOOSE US (4 columns)
       ============================================ -->
    <div class="about-sect-head reveal">
        <h2><span class="h-ico"><i class="fa-solid fa-star"></i></span> Why Choose SkillShare Hub?</h2>
        <div class="h-line"></div>
        <p>Built to give you a real edge over traditional classroom learning</p>
    </div>
    <div class="info-grid">
        <div class="info-card reveal">
            <div class="ic-icon"><i class="fa-solid fa-chalkboard-teacher"></i></div>
            <h5>Expert Mentorship</h5>
            <p>Learn from experienced graduates with real-world industry knowledge and insights.</p>
        </div>
        <div class="info-card reveal">
            <div class="ic-icon"><i class="fa-solid fa-laptop-code"></i></div>
            <h5>Practical Skills</h5>
            <p>Access hands-on projects, assignments, and real-world case studies that prepare you for work.</p>
        </div>
        <div class="info-card reveal">
            <div class="ic-icon"><i class="fa-solid fa-video"></i></div>
            <h5>Video Sessions</h5>
            <p>One-on-one mentorship through live video calls and screen sharing, at your pace.</p>
        </div>
        <div class="info-card reveal">
            <div class="ic-icon"><i class="fa-solid fa-briefcase"></i></div>
            <h5>Career Ready</h5>
            <p>Interview prep, placement materials, and career guidance for your dream job.</p>
        </div>
    </div>

    <!-- ============================================
       WHAT WE DO (4 columns)
       ============================================ -->
    <div class="about-sect-head reveal">
        <h2><span class="h-ico"><i class="fa-solid fa-toolbox"></i></span> What We Do</h2>
        <div class="h-line"></div>
        <p>Everything you need to bridge the gap between education and industry</p>
    </div>
    <div class="info-grid">
        <div class="info-card reveal">
            <div class="ic-icon"><i class="fa-solid fa-book"></i></div>
            <h5>Structured Learning</h5>
            <p>A clear pathway from academic field to course to the exact skills you need.</p>
        </div>
        <div class="info-card reveal">
            <div class="ic-icon"><i class="fa-solid fa-user-group"></i></div>
            <h5>Community</h5>
            <p>Join a growing network of learners and mentors supporting each other's growth.</p>
        </div>
        <div class="info-card reveal">
            <div class="ic-icon"><i class="fa-solid fa-file-circle-check"></i></div>
            <h5>Verified Mentors</h5>
            <p>Every mentor is verified with ratings, reviews, and proven industry experience.</p>
        </div>
        <div class="info-card reveal">
            <div class="ic-icon"><i class="fa-solid fa-hand-holding-heart"></i></div>
            <h5>Affordable Access</h5>
            <p>Quality mentorship at fair prices, making expert guidance accessible to every student.</p>
        </div>
    </div>

    <!-- ============================================
       OUR TEAM
       ============================================ -->
    <div class="about-sect-head reveal">
        <h2><span class="h-ico"><i class="fa-solid fa-users"></i></span> Our Team</h2>
        <div class="h-line"></div>
        <p>The people behind SkillShare Hub</p>
    </div>
    <div class="team-grid">
        <div class="team-card reveal">
            <div class="tm-avatar" style="background:linear-gradient(135deg,#8b5cf6,#3b82f6);">RT</div>
            <h5>Roshan Timalsina</h5>
            <div class="tm-role">Frontend &amp; Backend Developer</div>
            <button class="tm-btn" onclick="showToast('Info', 'Profile coming soon!', 'info')">View Profile</button>
        </div>
        <div class="team-card reveal">
            <div class="tm-avatar" style="background:linear-gradient(135deg,#3b82f6,#22c55e);">AR</div>
            <h5>Abiral Rai</h5>
            <div class="tm-role">Frontend Developer &amp; UI/UX Designer</div>
            <button class="tm-btn" onclick="showToast('Info', 'Profile coming soon!', 'info')">View Profile</button>
        </div>
    </div>

    <!-- ============================================
       CTA
       ============================================ -->
<div class="section about-cta reveal" style="padding:0;">
        <div class="cta-section">
            <h2>Want to Be Part of Our Story?</h2>
            <p>Join us as a learner, mentor, or partner and help us shape the future of education. Together, let's bridge the gap between education and industry, one skill at a time.</p>
            <div class="cta-buttons">
                <a href="register.php" class="btn-cta btn-cta-primary"><i class="fa-solid fa-user-plus"></i> Join Us</a>
                <a href="contact.php" class="btn-cta btn-cta-outline"><i class="fa-solid fa-envelope"></i> Get in Touch</a>
            </div>
        </div>
    </div>

</div>

<?php include 'frontend/components/platform-footer.php'; ?>

