<?php
// Statistics / Counter Section
$stats = isset($stats) && is_array($stats) ? $stats : [
    ['icon' => 'fa-graduation-cap', 'value' => 10000, 'suffix' => '+', 'label' => 'Active Learners'],
    ['icon' => 'fa-user-tie', 'value' => 500, 'suffix' => '+', 'label' => 'Expert Mentors'],
    ['icon' => 'fa-book-open', 'value' => 120, 'suffix' => '+', 'label' => 'Premium Courses'],
    ['icon' => 'fa-flask', 'value' => 800, 'suffix' => '+', 'label' => 'Research Projects'],
    ['icon' => 'fa-award', 'value' => 95, 'suffix' => '%', 'label' => 'Success Rate'],
];
?>
<section class="section-padding" id="statistics" style="background:var(--gradient-dark);">
    <div class="container-max">
        <div class="section-heading reveal">
            <span class="eyebrow" style="background:rgba(255,255,255,.1);color:var(--secondary);"><i class="fa-solid fa-chart-simple"></i> Our Impact</span>
            <h2 style="color:#fff;">Numbers That <span class="text-gradient-secondary">Speak Loud</span></h2>
            <p style="color:rgba(255,255,255,.6);">We are proud of the community we have built and the impact we create every day.</p>
        </div>

        <div class="grid-5 stats-grid">
            <?php foreach ($stats as $stat): ?>
                <div class="stat-box reveal">
                    <div class="stat-box-icon"><i class="fa-solid <?php echo $stat['icon']; ?>"></i></div>
                    <h3>
                        <span class="counter" data-target="<?php echo $stat['value']; ?>">0</span>
                        <span class="counter-suffix"><?php echo $stat['suffix']; ?></span>
                    </h3>
                    <p><?php echo $stat['label']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .grid-5 { display: grid; grid-template-columns: repeat(5, 1fr); gap: var(--spacing-5); }
    .stat-box { text-align: center; padding: var(--spacing-5); background: rgba(255, 255, 255, 0.05); border-radius: var(--border-radius-lg); transition: var(--transition-base); }
    .stat-box:hover { background: rgba(255, 255, 255, 0.1); transform: translateY(-6px); }
    .stat-box-icon { width: 64px; height: 64px; margin: 0 auto var(--spacing-4); border-radius: var(--border-radius-lg); background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #fff; box-shadow: var(--shadow-primary); }
    .stat-box:nth-child(2n) .stat-box-icon { background: var(--gradient-secondary); color: var(--dark); }
    .stat-box:nth-child(3n) .stat-box-icon { background: var(--gradient-accent); }
    .stat-box h3 { color: #fff; font-size: var(--font-size-3xl); margin-bottom: 0.3rem; }
    .stat-box p { color: rgba(255, 255, 255, 0.6); font-size: var(--font-size-sm); margin: 0; }
    @media (max-width: 991px) { .grid-5 { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 575px) { .grid-5 { grid-template-columns: repeat(2, 1fr); } }
</style>
