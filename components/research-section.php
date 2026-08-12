<?php
// Research Sections for the homepage
// Matches the exact dark glassmorphism design of the dashboard.php code
$research = isset($research) && is_array($research) ? $research : [
    ['icon' => 'fa-file-lines', 'tag' => 'Publication', 'title' => 'Paper Publication Guidance', 'desc' => 'Get expert help to structure, write, and publish your research paper in recognized journals.'],
    ['icon' => 'fa-flask-vial', 'tag' => 'Lab Work', 'title' => 'Lab & Field Research', 'desc' => 'Hands-on guidance for experiments, data collection, and field studies across disciplines.'],
    ['icon' => 'fa-magnifying-glass-chart', 'tag' => 'Analytics', 'title' => 'Data Analysis & Tools', 'desc' => 'Master statistical tools like SPSS, Python, R, and MATLAB for robust data interpretation.'],
    ['icon' => 'fa-lightbulb', 'tag' => 'Thesis', 'title' => 'Thesis & Dissertation', 'desc' => 'Comprehensive support for your thesis topics, literature review, and final defense.'],
];
?>
<section class="section-padding research-dash-section" id="research">
    <div class="container-max">
        <div class="research-dash-head">
            <span class="research-eyebrow"><i class="fa-solid fa-flask"></i> Research &amp; Innovation</span>
            <h2>Drive Your <span>Research Forward</span></h2>
            <p>From idea to publication, get the mentorship and tools you need for impactful academic research.</p>
        </div>

        <div class="research-dash-grid">
            <?php foreach ($research as $r): ?>
                <div class="research-dash-card">
                    <div class="rd-top">
                        <div class="rd-icon"><i class="fa-solid <?php echo $r['icon']; ?>"></i></div>
                        <span class="rd-badge"><?php echo $r['tag']; ?></span>
                    </div>
                    <div class="rd-name"><?php echo $r['title']; ?></div>
                    <div class="rd-desc"><?php echo $r['desc']; ?></div>
                    <a href="research.php" class="rd-btn"><i class="fas fa-arrow-right"></i> Read More</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    /* ============================================
       RESEARCH SECTION - matches dashboard.php design
       ============================================ */
    .research-dash-section {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #1a1a2e 100%);
        background-attachment: fixed;
        color: #ffffff;
    }

    .research-dash-head {
        text-align: center;
        max-width: 640px;
        margin: 0 auto var(--spacing-5);
    }

    .research-dash-head .research-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1.2rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #a78bfa;
        margin-bottom: var(--spacing-4);
    }

    .research-dash-head h2 {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-size: 2.25rem;
        color: #ffffff;
        margin-bottom: var(--spacing-3);
    }

    .research-dash-head h2 span {
        color: #60a5fa;
    }

    .research-dash-head p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1rem;
        line-height: 1.7;
        margin: 0;
    }

    /* ============================================
       RESEARCH GRID + CARDS (same as dashboard)
       ============================================ */
    .research-dash-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    .research-dash-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 18px 20px;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .research-dash-card:hover {
        transform: translateY(-4px);
        background: rgba(255, 255, 255, 0.06);
        border-color: #60a5fa;
    }

    .research-dash-card .rd-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 10px;
    }

    .research-dash-card .rd-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(59, 130, 246, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #60a5fa;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .research-dash-card:hover .rd-icon {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: #fff;
        transform: scale(1.05) rotate(-5deg);
    }

    .research-dash-card .rd-badge {
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 2px 10px;
        border-radius: 999px;
        background: rgba(59, 130, 246, 0.12);
        color: #60a5fa;
        white-space: nowrap;
    }

    .research-dash-card .rd-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: #ffffff;
        margin: 8px 0 4px;
        line-height: 1.4;
    }

    .research-dash-card .rd-desc {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.85rem;
        line-height: 1.6;
        flex: 1;
    }

    .research-dash-card .rd-btn {
        margin-top: 14px;
        padding: 6px 18px;
        border-radius: 999px;
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.78rem;
        cursor: pointer;
        transition: all 0.3s ease;
        width: fit-content;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .research-dash-card .rd-btn {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border-color: #3b82f6;
        color: #fff;
    }

    .research-dash-card .rd-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }

    .research-dash-card .rd-btn i {
        font-size: 0.7rem;
    }

    /* ============================================
       RESPONSIVE
       ============================================ */
    @media (max-width: 991px) {
        .research-dash-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 575px) {
        .research-dash-grid { grid-template-columns: 1fr; }
    }
</style>
