<?php
session_start();
$page_title = 'Academic Fields | SkillShare Hub';
$page_active = 'Academic Fields';
include 'frontend/components/platform-header.php';
include 'frontend/components/acad-db.php';
?>

<!-- ============================================
   ACADEMIC FIELDS PAGE STYLES (scoped)
   ============================================ -->
<style>
    .acad-page-header { text-align: center; padding: 20px 0 30px; }
    .acad-page-header h1 {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 2.8rem;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .acad-page-header p { color: var(--text-secondary); font-size: 1.1rem; max-width: 620px; margin: 0 auto; }

    .acad-fields-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }
    .acad-field-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        padding: 30px 24px;
        text-align: center;
        text-decoration: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .acad-field-card:hover {
        transform: translateY(-8px);
        background: rgba(255,255,255,0.06);
        border-color: var(--border-hover);
        box-shadow: var(--shadow-lg);
    }
    .acad-field-card .af-icon {
        width: 70px;
        height: 70px;
        border-radius: var(--radius-lg);
        background: rgba(59,130,246,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 2rem;
        color: var(--primary-400);
        transition: all 0.3s ease;
    }
    .acad-field-card:hover .af-icon {
        background: var(--gradient-primary);
        color: #fff;
        transform: scale(1.1) rotate(-5deg);
    }
    .acad-field-card .af-name { font-weight: 700; color: var(--text-primary); font-size: 1.1rem; font-family: var(--font-heading); }
    .acad-field-card .af-desc { color: var(--text-secondary); font-size: 0.85rem; margin: 8px 0; line-height: 1.5; }
    .acad-field-card .af-courses {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        padding: 6px 18px;
        border-radius: var(--radius-full);
        background: rgba(59,130,246,0.12);
        color: var(--primary-400);
        font-size: 0.8rem;
        font-weight: 500;
    }
    .acad-field-card .af-courses i { font-size: 0.7rem; }
</style>

<div class="container">

    <div class="acad-page-header reveal">
        <h1>Academic Fields</h1>
        <p>Explore diverse academic fields, their courses, subjects, skills, and expert mentors</p>
    </div>

    <div class="acad-fields-grid">
        <?php
        $fields = acad_query("SELECT af.*,
                        (SELECT COUNT(*) FROM acad_courses c WHERE c.field_id = af.id AND c.status='active') AS course_count
                        FROM acad_fields af
                        WHERE af.status='active'
                        ORDER BY af.name ASC");

        if (!$fields || count($fields) === 0): ?>
            <div class="research-no-results" style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted);">
                <i class="fa-solid fa-layer-group" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
                <p>No academic fields found. Please run the installer: <code>database/academic_install.php</code></p>
            </div>
        <?php else: foreach ($fields as $f): ?>
            <a href="courses.php?field_id=<?php echo (int)$f['id']; ?>" class="acad-field-card reveal">
                <div class="af-icon"><i class="fa-solid <?php echo htmlspecialchars($f['icon']); ?>"></i></div>
                <div class="af-name"><?php echo htmlspecialchars($f['name']); ?></div>
                <div class="af-desc"><?php echo htmlspecialchars($f['description']); ?></div>
                <span class="af-courses"><i class="fa-solid fa-graduation-cap"></i> <?php echo (int)$f['course_count']; ?> Courses</span>
            </a>
        <?php endforeach; endif; ?>
    </div>

</div>

<?php include 'frontend/components/platform-footer.php'; ?>

