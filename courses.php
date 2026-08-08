<?php
session_start();
$page_title = 'Courses | SkillShare Hub';
$page_active = 'Academic Fields';
include 'frontend/components/platform-header.php';
include 'frontend/components/acad-db.php';

$field_id = isset($_GET['field_id']) ? (int)$_GET['field_id'] : 0;
$field = $field_id ? acad_query("SELECT * FROM acad_fields WHERE id = ? AND status = 'active'", [$field_id], true) : null;
$courses = $field ? acad_query("SELECT c.*,
                        (SELECT COUNT(*) FROM acad_subjects s WHERE s.course_id = c.id AND s.status='active') AS subject_count
                        FROM acad_courses c
                        WHERE c.field_id = ? AND c.status='active'
                        ORDER BY c.name ASC", [$field_id]) : [];
?>

<style>
    .acad-breadcrumb { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; font-size: 0.85rem; }
    .acad-breadcrumb a { color: var(--text-muted); text-decoration: none; transition: all 0.3s ease; }
    .acad-breadcrumb a:hover { color: var(--primary-400); }
    .acad-breadcrumb .sep { color: var(--text-muted); }
    .acad-breadcrumb .current { color: var(--primary-400); font-weight: 500; }

    .acad-back-btn { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 16px; padding: 8px 20px; border-radius: var(--radius-full); background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); font-size: 0.85rem; cursor: pointer; text-decoration: none; transition: all 0.3s ease; }
    .acad-back-btn:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); border-color: var(--border-hover); }

    .acad-list-header { text-align: center; padding: 10px 0 24px; }
    .acad-list-header h1 { font-family: var(--font-heading); font-weight: 800; font-size: 2.4rem; background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .acad-list-header p { color: var(--text-muted); font-size: 0.95rem; }

    .acad-courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .acad-course-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 20px; text-align: center; text-decoration: none; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: flex; flex-direction: column; align-items: center; }
    .acad-course-card:hover { transform: translateY(-6px); background: rgba(255,255,255,0.06); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .acad-course-card .cc-icon { width: 56px; height: 56px; border-radius: var(--radius-md); background: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--primary-400); margin-bottom: 12px; transition: all 0.3s ease; }
    .acad-course-card:hover .cc-icon { background: var(--gradient-primary); color: #fff; transform: scale(1.1) rotate(-5deg); }
    .acad-course-card .cc-name { font-weight: 600; color: var(--text-primary); font-size: 0.95rem; font-family: var(--font-heading); }
    .acad-course-card .cc-desc { color: var(--text-secondary); font-size: 0.8rem; margin: 6px 0 12px; line-height: 1.4; flex: 1; }
    .acad-course-card .cc-subjects { display: inline-flex; align-items: center; gap: 6px; padding: 5px 16px; border-radius: var(--radius-full); background: rgba(139,92,246,0.12); color: var(--secondary-400); font-size: 0.75rem; font-weight: 500; }
</style>

<div class="container">

    <nav class="acad-breadcrumb reveal">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <span class="sep">/</span>
        <a href="academic-fields.php">Academic Fields</a>
        <?php if ($field): ?>
            <span class="sep">/</span>
            <span class="current"><?php echo htmlspecialchars($field['name']); ?></span>
            <span class="sep">/</span>
            <span>Courses</span>
        <?php endif; ?>
    </nav>

    <a href="academic-fields.php" class="acad-back-btn reveal">
        <i class="fa-solid fa-arrow-left"></i> Back to Academic Fields
    </a>

    <?php if (!$field): ?>
        <div class="acad-list-header reveal"><h1>Courses</h1><p>Please select an academic field to view its courses.</p></div>
        <div class="research-no-results">
            <i class="fa-solid fa-graduation-cap" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
            <p>No academic field selected. <a href="academic-fields.php" style="color:var(--primary-400);">Choose a field</a> to continue.</p>
        </div>
    <?php else: ?>
        <div class="acad-list-header reveal">
            <h1>Courses in <?php echo htmlspecialchars($field['name']); ?></h1>
            <p>Select a course to explore its subjects</p>
        </div>

        <div class="acad-courses-grid">
            <?php if (count($courses) === 0): ?>
                <div class="research-no-results" style="grid-column:1/-1;">
                    <i class="fa-solid fa-book-open" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
                    <p>No courses available for this academic field.</p>
                </div>
            <?php else: foreach ($courses as $c): ?>
                <a href="subjects.php?course_id=<?php echo (int)$c['id']; ?>" class="acad-course-card reveal">
                    <div class="cc-icon"><i class="fa-solid <?php echo htmlspecialchars($c['icon'] ?: 'fa-graduation-cap'); ?>"></i></div>
                    <div class="cc-name"><?php echo htmlspecialchars($c['name']); ?></div>
                    <div class="cc-desc"><?php echo htmlspecialchars($c['description']); ?></div>
                    <span class="cc-subjects"><i class="fa-solid fa-book"></i> <?php echo (int)$c['subject_count']; ?> Subjects</span>
                </a>
            <?php endforeach; endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'frontend/components/platform-footer.php'; ?>

