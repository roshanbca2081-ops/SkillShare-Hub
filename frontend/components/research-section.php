<?php
// Research Section for homepage
$research = isset($research) && is_array($research) ? $research : [
    ['icon' => 'fa-file-lines', 'title' => 'Paper Publication Guidance', 'desc' => 'Get expert help to structure, write, and publish your research paper in recognized journals.', 'tag' => 'Publication'],
    ['icon' => 'fa-flask-vial', 'title' => 'Lab & Field Research', 'desc' => 'Hands-on guidance for experiments, data collection, and field studies across disciplines.', 'tag' => 'Lab Work'],
    ['icon' => 'fa-magnifying-glass-chart', 'title' => 'Data Analysis & Tools', 'desc' => 'Master statistical tools like SPSS, Python, R, and MATLAB for robust data interpretation.', 'tag' => 'Analytics'],
    ['icon' => 'fa-lightbulb', 'title' => 'Thesis & Dissertation', 'desc' => 'Comprehensive support for your thesis topics, literature review, and final defense.', 'tag' => 'Thesis'],
];
?>
<section class="section-padding" id="research">
    <div class="container-max">
        <div class="section-heading reveal">
            <span class="eyebrow"><i class="fa-solid fa-flask"></i> Research & Innovation</span>
            <h2>Drive Your <span class="text-gradient">Research Forward</span></h2>
            <p>From idea to publication, get the mentorship and tools you need for impactful academic research.</p>
        </div>

        <div class="grid-4 research-grid">
            <?php foreach ($research as $r): ?>
                <div class="research-card card-base reveal">
                    <div class="research-icon"><i class="fa-solid <?php echo $r['icon']; ?>"></i></div>
                    <span class="badge badge-primary"><?php echo $r['tag']; ?></span>
                    <h5><?php echo $r['title']; ?></h5>
                    <p><?php echo $r['desc']; ?></p>
                    <a href="research.php" class="btn btn-ghost btn-sm" style="color:var(--primary);border-color:var(--primary);">
                        Read More <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--spacing-5); }
    .research-card { padding: var(--spacing-5); }
    .research-icon {
        width: 56px;
        height: 56px;
        border-radius: var(--border-radius-md);
        background: var(--gradient-secondary);
        color: var(--dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        margin-bottom: var(--spacing-4);
    }
    .research-card h5 { margin: var(--spacing-3) 0 0.6rem; }
    .research-card p { font-size: var(--font-size-sm); color: var(--gray-500); margin-bottom: var(--spacing-4); }
    @media (max-width: 991px) { .grid-4 { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .grid-4 { grid-template-columns: 1fr; } }
</style>
