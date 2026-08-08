<?php
session_start();
$page_title = 'Academic Fields | SkillShare Hub';
$page_active = 'Academic Fields';
include 'frontend/components/platform-header.php';
include 'frontend/components/acad-db.php';

$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$subject = $subject_id ? acad_query("SELECT s.*, c.name AS course_name, c.id AS course_id, c.field_id, f.name AS field_name FROM acad_subjects s JOIN acad_courses c ON c.id = s.course_id JOIN acad_fields f ON f.id = c.field_id WHERE s.id = ? AND s.status='active'", [$subject_id], true) : null;
$skills = $subject ? acad_query("SELECT * FROM acad_skills WHERE subject_id = ? ORDER BY name ASC", [$subject_id]) : [];
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

    .acad-skills-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-bottom: 30px; }
    .acad-skill-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); padding: 18px; text-align: center; text-decoration: none; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); display: flex; flex-direction: column; align-items: center; }
    .acad-skill-card:hover { transform: translateY(-6px); background: rgba(255,255,255,0.06); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .acad-skill-card .sk-icon { width: 48px; height: 48px; border-radius: var(--radius-md); background: rgba(34,197,94,0.1); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--success); margin-bottom: 10px; transition: all 0.3s ease; }
    .acad-skill-card:hover .sk-icon { background: var(--gradient-primary); color: #fff; transform: scale(1.1) rotate(-5deg); }
    .acad-skill-card .sk-name { font-weight: 600; color: var(--text-primary); font-size: 0.9rem; font-family: var(--font-heading); }
    .acad-skill-card .sk-desc { color: var(--text-muted); font-size: 0.75rem; margin: 4px 0 10px; line-height: 1.4; flex: 1; }
    .acad-skill-card .sk-mentors { display: inline-flex; align-items: center; gap: 6px; padding: 5px 16px; border-radius: var(--radius-full); background: rgba(96,165,250,0.12); color: var(--primary-400); font-size: 0.75rem; font-weight: 500; }
</style>

<div class="container">

    <nav class="acad-breadcrumb reveal">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <span class="sep">/</span>
        <a href="academic-fields.php">Academic Fields</a>
        <?php if ($subject): ?>
            <span class="sep">/</span>
            <a href="courses.php?field_id=<?php echo (int)$subject['field_id']; ?>"><?php echo htmlspecialchars($subject['field_name']); ?></a>
            <span class="sep">/</span>
            <a href="subjects.php?course_id=<?php echo (int)$subject['course_id']; ?>"><?php echo htmlspecialchars($subject['course_name']); ?></a>
            <span class="sep">/</span>
            <span class="current"><?php echo htmlspecialchars($subject['name']); ?></span>
        <?php endif; ?>
    </nav>

    <a href="subjects.php?course_id=<?php echo $subject ? (int)$subject['course_id'] : ''; ?>" class="acad-back-btn reveal">
        <i class="fa-solid fa-arrow-left"></i> Back to Subjects
    </a>

    <?php if (!$subject): ?>
        <div class="acad-list-header reveal"><h1>Skills</h1><p>Please select a subject to view its skills.</p></div>
        <div class="research-no-results">
            <i class="fa-solid fa-star" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
            <p>No subject selected. <a href="academic-fields.php" style="color:var(--primary-400);">Choose a subject</a> to continue.</p>
        </div>
    <?php else: ?>
        <div class="acad-list-header reveal">
            <h1><?php echo htmlspecialchars($subject['name']); ?></h1>
            <p>Select a skill to find expert mentors</p>
        </div>

        <div class="acad-skills-grid">
            <?php if (count($skills) === 0): ?>
                <div class="research-no-results" style="grid-column:1/-1;"><p>No skills available for this subject yet.</p></div>
            <?php else: foreach ($skills as $k): ?>
                <a href="mentors.php?skill_id=<?php echo (int)$k['id']; ?>" class="acad-skill-card reveal">
                    <div class="sk-icon"><i class="fa-solid fa-star"></i></div>
                    <div class="sk-name"><?php echo htmlspecialchars($k['name']); ?></div>
                    <div class="sk-desc"><?php echo htmlspecialchars($k['description']); ?></div>
                    <span class="sk-mentors"><i class="fa-solid fa-user-tie"></i> Find Mentors</span>
                </a>
            <?php endforeach; endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'frontend/components/platform-footer.php'; ?>
