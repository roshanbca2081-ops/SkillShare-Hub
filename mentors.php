<?php
session_start();
$page_title = 'Mentors | SkillShare Hub';
$page_active = 'Mentors';
include 'config.php';
include 'frontend/components/platform-header.php';
include 'frontend/components/acad-db.php';

$skill_id = isset($_GET['skill_id']) ? (int)$_GET['skill_id'] : 0;
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$field_id = isset($_GET['field_id']) ? (int)$_GET['field_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$pdo = getDB();
$mentors = [];
$skill = null;
$course = null;
$field = null;

// Build mentor query based on filters
$sql = "SELECT u.id, u.full_name, u.email, u.profile_picture, u.bio, u.hourly_rate,
               u.academic_field_id, u.course_id,
               m.specialization, m.experience_years, m.current_company, m.current_position,
               m.qualification, m.is_verified, m.rating, m.reviews_count,
               m.total_sessions, m.total_students, f.name as field_name, c.name as course_name
        FROM users u
        JOIN mentors m ON u.id = m.user_id
        LEFT JOIN academic_fields f ON u.academic_field_id = f.id
        LEFT JOIN courses c ON u.course_id = c.id
        WHERE u.role = 'mentor' AND u.status = 'active' AND m.is_verified = 1";
$params = [];

if ($skill_id) {
    $skill = acad_query("SELECT s.*, c.name AS course_name, c.id AS course_id, c.field_id, f.name AS field_name FROM acad_skills s JOIN acad_subjects sub ON sub.id = s.subject_id JOIN acad_courses c ON c.id = sub.course_id JOIN acad_fields f ON f.id = c.field_id WHERE s.id = ?", [$skill_id], true);
    if ($skill) {
        $sql .= " AND u.course_id = ?";
        $params[] = (int)$skill['course_id'];
    }
}

if ($course_id) {
    $course = acad_query("SELECT c.*, f.name AS field_name FROM courses c JOIN academic_fields f ON f.id = c.academic_field_id WHERE c.id = ?", [$course_id], true);
    $sql .= " AND u.course_id = ?";
    $params[] = $course_id;
}

if ($field_id) {
    $field = acad_query("SELECT * FROM academic_fields WHERE id = ?", [$field_id], true);
    $sql .= " AND u.academic_field_id = ?";
    $params[] = $field_id;
}

if ($search) {
    $sql .= " AND (u.full_name LIKE ? OR m.specialization LIKE ? OR u.bio LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY m.rating DESC, m.reviews_count DESC LIMIT 50";
$mentors = acad_query($sql, $params);

// Fallback: if no mentors found via acad_* skill, try real skills table
if (!$skill_id && !$course_id && !$field_id && count($mentors) === 0) {
    $mentors = $pdo->query("SELECT u.id, u.full_name, u.email, u.profile_picture, u.bio, u.hourly_rate, u.academic_field_id, u.course_id, m.specialization, m.experience_years, m.current_company, m.current_position, m.qualification, m.is_verified, m.rating, m.reviews_count, m.total_sessions, m.total_students, f.name as field_name, c.name as course_name FROM users u JOIN mentors m ON u.id = m.user_id LEFT JOIN academic_fields f ON u.academic_field_id = f.id LEFT JOIN courses c ON u.course_id = c.id WHERE u.role = 'mentor' AND u.status = 'active' AND m.is_verified = 1 ORDER BY m.rating DESC LIMIT 20")->fetchAll();
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
    .acad-mentor-card .mc-avatar { width: 72px; height: 72px; border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: #fff; transition: transform 0.3s ease; object-fit: cover; }
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
    .acad-mentor-card .mc-btn { margin-top: 12px; padding: 6px 20px; border-radius: var(--radius-full); background: transparent; border: 1px solid var(--glass-border); color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
    .acad-mentor-card .mc-btn:hover { background: var(--gradient-primary); border-color: var(--primary-500); color: #fff; }
    .mc-online { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success); margin-left: 6px; }
    .mc-online.off { background: var(--text-muted); }
</style>

<div class="container">

    <?php if ($skill): ?>
    <nav class="acad-breadcrumb reveal">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <span class="sep">/</span>
        <a href="academic-fields.php">Academic Fields</a>
        <span class="sep">/</span>
        <a href="courses.php?field_id=<?php echo (int)$skill['field_id']; ?>"><?php echo htmlspecialchars($skill['field_name']); ?></a>
        <span class="sep">/</span>
        <a href="subjects.php?course_id=<?php echo (int)$skill['course_id']; ?>"><?php echo htmlspecialchars($skill['course_name']); ?></a>
        <span class="sep">/</span>
        <a href="skills.php?subject_id=<?php echo (int)$skill['subject_id'] ?? 0; ?>"><?php echo htmlspecialchars($skill['name']); ?></a>
        <span class="sep">/</span>
        <span class="current">Mentors</span>
    </nav>
    <a href="skills.php?subject_id=<?php echo (int)($skill['subject_id'] ?? 0); ?>" class="acad-back-btn reveal"><i class="fa-solid fa-arrow-left"></i> Back to Skills</a>
    <?php elseif ($course): ?>
    <nav class="acad-breadcrumb reveal">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <span class="sep">/</span>
        <a href="academic-fields.php">Academic Fields</a>
        <span class="sep">/</span>
        <a href="courses.php?field_id=<?php echo (int)$course['field_id']; ?>"><?php echo htmlspecialchars($course['field_name']); ?></a>
        <span class="sep">/</span>
        <span class="current">Mentors</span>
    </nav>
    <a href="courses.php?field_id=<?php echo (int)$course['field_id']; ?>" class="acad-back-btn reveal"><i class="fa-solid fa-arrow-left"></i> Back to Courses</a>
    <?php elseif ($field): ?>
    <nav class="acad-breadcrumb reveal">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <span class="sep">/</span>
        <a href="academic-fields.php">Academic Fields</a>
        <span class="sep">/</span>
        <span class="current">Mentors</span>
    </nav>
    <a href="academic-fields.php" class="acad-back-btn reveal"><i class="fa-solid fa-arrow-left"></i> Back to Academic Fields</a>
    <?php endif; ?>

    <?php if (!$skill && !$course_id && !$field_id): ?>
        <div class="acad-list-header reveal"><h1>Our Mentors</h1><p>Please select a skill, course, or field to find expert mentors.</p></div>
        <div class="research-no-results">
            <i class="fa-solid fa-user-tie" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
            <p>No filter selected. <a href="academic-fields.php" style="color:var(--primary-400);">Choose a field</a> to find mentors.</p>
        </div>
    <?php else: ?>
        <div class="acad-list-header reveal">
            <h1>
                <?php if ($skill): echo htmlspecialchars($skill['name']) . ' Mentors'; ?>
                <?php elseif ($course): echo 'Mentors in ' . htmlspecialchars($course['name']); ?>
                <?php elseif ($field): echo 'Mentors in ' . htmlspecialchars($field['name']); ?>
                <?php else: echo 'Our Mentors'; endif; ?>
            </h1>
            <p>
                <?php if ($skill): echo 'Expert mentors for ' . htmlspecialchars($skill['name']); ?>
                <?php elseif ($course): echo 'Mentors teaching courses in ' . htmlspecialchars($course['name']); ?>
                <?php elseif ($field): echo 'Mentors specializing in ' . htmlspecialchars($field['name']); ?>
                <?php else: echo 'Learn from experienced industry experts'; endif; ?>
            </p>
        </div>

        <div class="acad-mentors-grid">
            <?php if (count($mentors) === 0): ?>
                <div class="research-no-results" style="grid-column:1/-1;">
                    <i class="fa-solid fa-user-slash" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
                    <p>No registered mentors found for this selection yet. Please check back later.</p>
                </div>
            <?php else: foreach ($mentors as $m):
                $initials = '';
                foreach (explode(' ', $m['full_name']) as $w) { $initials .= strtoupper(substr($w, 0, 1)); }
                $initials = substr($initials, 0, 2);
                $colors = ['blue','purple','green','pink','orange','teal'];
                $color = $colors[array_search($color, $colors) ?? 0] ?? 'blue';
                if (!empty($m['color'])) $color = $m['color'];
                $fullStars = floor($m['rating'] ?? 0);
                $halfStar = ($m['rating'] - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                $stars = str_repeat('★', $fullStars) . ($halfStar ? '★' : '') . str_repeat('☆', $emptyStars);
            ?>
                <div class="acad-mentor-card reveal">
                    <div class="mc-avatar <?php echo htmlspecialchars($color); ?>">
                        <?php if ($m['profile_picture']): ?>
                            <img src="<?php echo BASE_URL; ?>frontend/assets/images/profile/<?php echo htmlspecialchars($m['profile_picture']); ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        <?php else: ?>
                            <?php echo htmlspecialchars($initials); ?>
                        <?php endif; ?>
                    </div>
                    <div class="mc-name">
                        <?php echo htmlspecialchars($m['full_name']); ?>
                        <?php if ($m['is_verified']): ?><i class="fa-solid fa-circle-check" style="color:var(--primary-400);font-size:0.8rem;" title="Verified"></i><?php endif; ?>
                    </div>
                    <div class="mc-title"><?php echo htmlspecialchars($m['specialization'] ?: 'Mentor'); ?></div>
                    <div class="mc-company"><?php echo htmlspecialchars($m['current_company'] ?: ($m['field_name'] ?: '')); ?></div>
                    <div class="mc-rating"><?php echo $stars; ?> <span>(<?php echo (int)($m['reviews_count'] ?? 0); ?> reviews)</span></div>
                    <div class="mc-stats">
                        <span><i class="fa-solid fa-user-graduate"></i> <?php echo (int)($m['total_students'] ?? 0); ?></span>
                        <span><i class="fa-solid fa-clock"></i> <?php echo htmlspecialchars($m['experience_years'] ? $m['experience_years'] . ' yrs' : 'N/A'); ?></span>
                    </div>
                    <div class="mc-rate">$<?php echo number_format($m['hourly_rate'] ?? 0, 2); ?> <span>/ hour</span></div>
                    <a href="mentor.php?id=<?php echo (int)$m['id']; ?>" class="mc-btn"><i class="fa-solid fa-user-tie"></i> View Profile</a>
                </div>
            <?php endforeach; endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php include 'frontend/components/platform-footer.php'; ?>
