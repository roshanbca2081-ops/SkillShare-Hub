<?php
session_start();
$page_title = 'Academic Fields | SkillShare Hub';
$page_active = 'Academic Fields';
include 'frontend/components/platform-header.php';
include 'frontend/components/acad-db.php';

$skill_id = isset($_GET['skill_id']) ? (int)$_GET['skill_id'] : 0;
$skill = $skill_id ? acad_query("SELECT k.*, s.name AS subject_name, s.id AS subject_id, s.course_id, c.name AS course_name, c.field_id, f.name AS field_name FROM acad_skills k JOIN acad_subjects s ON s.id = k.subject_id JOIN acad_courses c ON c.id = s.course_id JOIN acad_fields f ON f.id = c.field_id WHERE k.id = ?", [$skill_id], true) : null;

$mentors = [];
if ($skill) {
    $mentors = acad_query("SELECT DISTINCT m.* FROM acad_mentors m
                    JOIN acad_mentor_skills ms ON ms.mentor_id = m.id
                    WHERE ms.skill_id = ? ORDER BY m.rating DESC", [$skill_id]);
}
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

    .acad-mentors-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .acad-mentor-card { background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: var(--radius-xl); padding: 24px; text-align: center; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .acad-mentor-card:hover { transform: translateY(-6px); background: rgba(255,255,255,0.06); border-color: var(--border-hover); box-shadow: var(--shadow-lg); }
    .acad-mentor-card .mc-avatar { width: 72px; height: 72px; border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: #fff; transition: transform 0.3s ease; }
    .acad-mentor-card:hover .mc-avatar { transform: scale(1.08); }
    .acad-mentor-card .mc-avatar.purple { background: #8b5cf6; }
    .acad-mentor-card .mc-avatar.blue { background: #3b82f6; }
    .acad-mentor-card .mc-avatar.green { background: #22c55e; }
    .acad-mentor-card .mc-avatar.pink { background: #ec4899; }
    .acad-mentor-card .mc-avatar.orange { background: #f59e0b; }
    .acad-mentor-card .mc-avatar.teal { background: #14b8a6; }
    .acad-mentor-card .mc-name { font-weight: 600; color: var(--text-primary); font-size: 1.05rem; font-family: var(--font-heading); }
    .acad-mentor-card .mc-title { color: var(--text-muted); font-size: 0.85rem; }
    .acad-mentor-card .mc-company { color: var(--primary-400); font-size: 0.8rem; margin-top: 2px; }
    .acad-mentor-card .mc-rating { color: #fbbf24; font-size: 0.9rem; margin: 6px 0; }
    .acad-mentor-card .mc-rating span { color: var(--text-muted); font-size: 0.75rem; }
    .acad-mentor-card .mc-stats { display: flex; justify-content: center; gap: 20px; margin: 8px 0; color: var(--text-muted); font-size: 0.8rem; }
    .acad-mentor-card .mc-stats span { display: inline-flex; align-items: center; gap: 4px; }
    .acad-mentor-card .mc-stats i { color: var(--primary-400); }
    .acad-mentor-card .mc-rate { color: var(--primary-400); font-weight: 600; font-size: 1.1rem; margin-top: 6px; }
    .acad-mentor-card .mc-rate span { color: var(--text-muted); font-weight: 400; font-size: 0.8rem; }
    .acad-mentor-card .mc-btn { margin-top: 12px; padding: 6px 20px; border-radius: var(--radius-full); background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; }
    .acad-mentor-card .mc-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
    .mc-online { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success); margin-left: 6px; }
    .mc-online.off { background: var(--text-muted); }
</style>

<div class="container">

    <nav class="acad-breadcrumb reveal">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <span class="sep">/</span>
        <a href="academic-fields.php">Academic Fields</a>
        <?php if ($skill): ?>
            <span class="sep">/</span>
            <a href="courses.php?field_id=<?php echo (int)$skill['field_id']; ?>"><?php echo htmlspecialchars($skill['field_name']); ?></a>
            <span class="sep">/</span>
            <a href="subjects.php?course_id=<?php echo (int)$skill['course_id']; ?>"><?php echo htmlspecialchars($skill['course_name']); ?></a>
            <span class="sep">/</span>
            <a href="skills.php?subject_id=<?php echo (int)$skill['subject_id']; ?>"><?php echo htmlspecialchars($skill['subject_name']); ?></a>
            <span class="sep">/</span>
            <span class="current"><?php echo htmlspecialchars($skill['name']); ?></span>
        <?php endif; ?>
    </nav>

    <a href="skills.php?subject_id=<?php echo $skill ? (int)$skill['subject_id'] : ''; ?>" class="acad-back-btn reveal">
        <i class="fa-solid fa-arrow-left"></i> Back to Skills
    </a>

    <?php if (!$skill): ?>
        <div class="acad-list-header reveal"><h1>Mentors</h1><p>Please select a skill to find expert mentors.</p></div>
        <div class="research-no-results">
            <i class="fa-solid fa-user-tie" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
            <p>No skill selected. <a href="academic-fields.php" style="color:var(--primary-400);">Choose a skill</a> to find mentors.</p>
        </div>
    <?php else: ?>
        <div class="acad-list-header reveal">
            <h1><?php echo htmlspecialchars($skill['name']); ?> Mentors</h1>
            <p>Expert mentors who can teach this skill</p>
        </div>

        <div class="acad-mentors-grid">
            <?php if (count($mentors) === 0): ?>
                <div class="research-no-results" style="grid-column:1/-1;"><p>No mentors found for this skill yet.</p></div>
            <?php else: foreach ($mentors as $m):
                $initials = '';
                foreach (explode(' ', $m['name']) as $w) { $initials .= strtoupper(substr($w, 0, 1)); }
                $initials = substr($initials, 0, 2);
                $color = $m['color'] ?: 'blue';
                $fullStars = floor($m['rating']);
                $stars = str_repeat('★', $fullStars) . ($m['rating'] - $fullStars >= 0.5 ? '★' : '') . str_repeat('☆', 5 - $fullStars - ($m['rating'] - $fullStars >= 0.5 ? 1 : 0));
            ?>
                <div class="acad-mentor-card reveal">
                    <div class="mc-avatar <?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($initials); ?></div>
                    <div class="mc-name"><?php echo htmlspecialchars($m['name']); ?>
                        <?php if ($m['verified']): ?><i class="fa-solid fa-badge-check" style="color:var(--primary-400);font-size:0.8rem;"></i><?php endif; ?>
                    </div>
                    <div class="mc-title"><?php echo htmlspecialchars($m['title']); ?></div>
                    <div class="mc-company"><?php echo htmlspecialchars($m['company']); ?></div>
                    <div class="mc-rating"><?php echo $stars; ?> <span>(<?php echo (int)$m['reviews']; ?> reviews)</span></div>
                    <div class="mc-stats">
                        <span><i class="fa-solid fa-user-graduate"></i> <?php echo (int)$m['students']; ?></span>
                        <span><i class="fa-solid fa-clock"></i> <?php echo htmlspecialchars($m['experience']); ?></span>
                    </div>
                    <div class="mc-rate">$<?php echo number_format($m['price'], 2); ?> <span>/ hour</span></div>
                    <button class="mc-btn" onclick="showToast('Success', 'Viewing <?php echo addslashes($m['name']); ?>\'s profile...', 'success')">
                        View Profile
                    </button>
                </div>
            <?php endforeach; endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'frontend/components/platform-footer.php'; ?>
