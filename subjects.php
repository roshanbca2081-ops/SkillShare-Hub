<?php
session_start();
$page_title = 'Academic Fields | SkillShare Hub';
$page_active = 'Academic Fields';
include 'frontend/components/platform-header.php';
include 'frontend/components/acad-db.php';

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$course = $course_id ? acad_query("SELECT c.*, f.name AS field_name FROM acad_courses c JOIN acad_fields f ON f.id = c.field_id WHERE c.id = ? AND c.status='active'", [$course_id], true) : null;
$subjects = $course ? acad_query("SELECT s.*, (SELECT COUNT(*) FROM acad_skills k WHERE k.subject_id = s.id) AS skill_count FROM acad_subjects s WHERE s.course_id = ? AND s.status='active' ORDER BY s.name ASC", [$course_id]) : [];
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

    .acad-subjects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .acad-subject-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 20px; text-align: center; text-decoration: none; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: flex; flex-direction: column; align-items: center; }
    .acad-subject-card:hover { transform: translateY(-6px); background: rgba(255,255,255,0.06); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .acad-subject-card .sc-icon { width: 56px; height: 56px; border-radius: var(--radius-md); background: rgba(139,92,246,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--secondary-400); margin-bottom: 12px; transition: all 0.3s ease; }
    .acad-subject-card:hover .sc-icon { background: var(--gradient-primary); color: #fff; transform: scale(1.1) rotate(-5deg); }
    .acad-subject-card .sc-name { font-weight: 600; color: var(--text-primary); font-size: 0.95rem; font-family: var(--font-heading); }
    .acad-subject-card .sc-desc { color: var(--text-secondary); font-size: 0.8rem; margin: 6px 0 12px; line-height: 1.4; flex: 1; }
    .acad-subject-card .sc-skills { display: inline-flex; align-items: center; gap: 6px; padding: 5px 16px; border-radius: var(--radius-full); background: rgba(34,197,94,0.12); color: var(--success); font-size: 0.75rem; font-weight: 500; }
</style>

<div class="container">

    <nav class="acad-breadcrumb reveal">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <span class="sep">/</span>
        <a href="academic-fields.php">Academic Fields</a>
        <?php if ($course): ?>
            <span class="sep">/</span>
            <a href="courses.php?field_id=<?php echo (int)$course['field_id']; ?>"><?php echo htmlspecialchars($course['field_name']); ?></a>
            <span class="sep">/</span>
            <span class="current"><?php echo htmlspecialchars($course['name']); ?></span>
        <?php endif; ?>
    </nav>

    <a href="courses.php?field_id=<?php echo $course ? (int)$course['field_id'] : ''; ?>" class="acad-back-btn reveal">
        <i class="fa-solid fa-arrow-left"></i> Back to Courses
    </a>

    <?php if (!$course): ?>
        <div class="acad-list-header reveal"><h1>Subjects</h1><p>Please select a course to view its subjects.</p></div>
        <div class="research-no-results">
            <i class="fa-solid fa-book-open" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
            <p>No course selected. <a href="academic-fields.php" style="color:var(--primary-400);">Choose a course</a> to continue.</p>
        </div>
    <?php else: ?>
        <div class="acad-list-header reveal">
            <h1><?php echo htmlspecialchars($course['name']); ?></h1>
            <p>Select a subject to explore its skills</p>
        </div>

        <div class="acad-subjects-grid">
            <?php if (count($subjects) === 0): ?>
                <div class="research-no-results" style="grid-column:1/-1;"><p>No subjects available for this course yet.</p></div>
            <?php else: foreach ($subjects as $s): ?>
                <a href="skills.php?subject_id=<?php echo (int)$s['id']; ?>" class="acad-subject-card reveal">
                    <div class="sc-icon"><i class="fa-solid fa-book"></i></div>
                    <div class="sc-name"><?php echo htmlspecialchars($s['name']); ?></div>
                    <div class="sc-desc"><?php echo htmlspecialchars($s['description']); ?></div>
                    <span class="sc-skills"><i class="fa-solid fa-star"></i> <?php echo (int)$s['skill_count']; ?> Skills</span>
                </a>
            <?php endforeach; endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'frontend/components/platform-footer.php'; ?>
