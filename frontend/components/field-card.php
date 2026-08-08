<?php
// Academic Field Cards
// Accepts optional $fields array (each: icon, title, desc, link, count)
$fields = isset($fields) && is_array($fields) ? $fields : [
    ['icon' => 'fa-code', 'title' => 'Computer Science', 'desc' => 'Programming, AI, Data Science & Software Development.', 'link' => 'courses.php?category=computer-science', 'count' => 32],
    ['icon' => 'fa-heart-pulse', 'title' => 'Medical Sciences', 'desc' => 'Medicine, Nursing, Pharmacy & Healthcare studies.', 'link' => 'courses.php?category=medical', 'count' => 18],
    ['icon' => 'fa-chart-line', 'title' => 'Business & Finance', 'desc' => 'Management, Marketing, Accounting & Entrepreneurship.', 'link' => 'courses.php?category=business', 'count' => 25],
    ['icon' => 'fa-gears', 'title' => 'Engineering', 'desc' => 'Civil, Mechanical, Electrical & Mechatronics.', 'link' => 'courses.php?category=engineering', 'count' => 21],
    ['icon' => 'fa-palette', 'title' => 'Arts & Design', 'desc' => 'Graphic Design, Animation, Music & Creative Arts.', 'link' => 'courses.php?category=arts', 'count' => 15],
    ['icon' => 'fa-scale-balanced', 'title' => 'Law & Humanities', 'desc' => 'Law, History, Political Science & Social Studies.', 'link' => 'courses.php?category=law', 'count' => 12],
];
?>
<section class="section-padding" id="academic-fields">
    <div class="container-max">
        <div class="section-heading reveal">
            <span class="eyebrow"><i class="fa-solid fa-layer-group"></i> Academic Fields</span>
            <h2>Explore Diverse <span class="text-gradient">Academic Fields</span></h2>
            <p>Discover the right path for your career across multiple disciplines guided by expert mentors.</p>
        </div>

        <div class="field-grid">
            <?php foreach ($fields as $field): ?>
                <div class="field-card reveal">
                    <div class="field-card-top">
                        <div class="field-card-icon"><i class="fa-solid <?php echo $field['icon']; ?>"></i></div>
                        <div class="field-meta">
                            <span>Program</span>
                            <h3><?php echo $field['title']; ?></h3>
                        </div>
                    </div>
                    <p class="field-card-content"><?php echo $field['desc']; ?></p>
                    <div class="field-card-footer">
                        <div class="badge-pill"><i class="fa-solid fa-arrow-trend-up"></i> <?php echo $field['count']; ?> Courses</div>
                        <a href="<?php echo $field['link']; ?>">Explore <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
