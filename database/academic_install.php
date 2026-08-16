<?php
// SkillShare Hub - Academic Navigation Installer
// Creates the acad_* tables and seeds data from academic_data.php
// (30 fields, ~200 courses, ~400 subjects, auto-generated skills + mentors).
// Run ONCE in browser or via CLI.

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(600);
ini_set('memory_limit', '512M');

// ---- Write log ----
$installLog = __DIR__ . '/install_log.txt';
file_put_contents($installLog, date('Y-m-d H:i:s') . " Install started\n");

function alog($msg) {
    global $installLog;
    file_put_contents($installLog, $msg . "\n", FILE_APPEND);
}

$data = require __DIR__ . '/academic_data.php';

// ---- Database config ----
$db_host = defined('DB_HOST') ? DB_HOST : 'localhost';
$db_name = defined('DB_NAME') ? DB_NAME : 'skillshare_hub';
$db_user = defined('DB_USER') ? DB_USER : 'root';
$db_pass = defined('DB_PASS') ? DB_PASS : '';

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    alog('DB connect error: ' . $e->getMessage());
    die('Database connection failed: ' . $e->getMessage());
}

$out = "<html><head><title>Academic Installer</title>
<style>body{font-family:Arial;background:#0a0a1a;color:#e5e7eb;padding:30px;max-width:900px;margin:auto;}h1{color:#60a5fa;}h2{color:#a78bfa;margin-top:30px;}li{margin:3px 0;}code{background:#1a1a2e;padding:2px 6px;border-radius:4px;}table{border-collapse:collapse;width:100%;font-size:.85rem;margin-top:10px;}th,td{border:1px solid #2a2a4a;padding:6px 10px;text-align:left;}th{color:#a78bfa;}td{color:#e5e7eb;}.ok{color:#22c55e;}.err{color:#ef4444;}</style></head><body>
<h1>SkillShare Hub - Academic Navigation Installer</h1>";

// ============================================================
// HELPER - duplicate-tolerant insert
// Returns inserted id, or existing id on duplicate.
// ============================================================
function acad_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    if ($text === '') $text = 'item';
    return $text;
}

function acad_insert_dupe($pdo, $table, $lookupWhere, $lookupParams, $sql, $params) {
    // Try insert first
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$pdo->lastInsertId();
    } catch (PDOException $e) {
        // Duplicate - fetch existing id by lookup
        try {
            $stmt = $pdo->prepare("SELECT id FROM {$table} WHERE {$lookupWhere} LIMIT 1");
            $stmt->execute($lookupParams);
            $row = $stmt->fetch();
            if ($row) return (int)$row['id'];
        } catch (Exception $e2) {}
        alog('Insert failed for ' . $table . ': ' . $e->getMessage());
        throw $e;
    }
}

// ============================================================
// 1. CREATE TABLES
// ============================================================
$out .= "<h2>Step 1: Creating tables</h2>";
alog('Step 1: Creating tables');

$tables = ['acad_mentor_skills', 'acad_mentors', 'acad_skills', 'acad_subjects', 'acad_courses', 'acad_fields'];
foreach ($tables as $t) { $pdo->exec("DROP TABLE IF EXISTS $t"); }

$pdo->exec("CREATE TABLE acad_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    icon VARCHAR(255) DEFAULT 'fa-layer-group',
    color VARCHAR(20) DEFAULT '#3b82f6',
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE acad_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    field_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL,
    icon VARCHAR(255) DEFAULT 'fa-graduation-cap',
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_acad_course_field_slug (field_id, slug),
    CONSTRAINT fk_acad_course_field FOREIGN KEY (field_id) REFERENCES acad_fields(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE acad_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL,
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_acad_subject_course_slug (course_id, slug),
    CONSTRAINT fk_acad_subject_course FOREIGN KEY (course_id) REFERENCES acad_courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE acad_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_acad_skill_subject FOREIGN KEY (subject_id) REFERENCES acad_subjects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE acad_mentors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    title VARCHAR(255),
    company VARCHAR(150),
    avatar VARCHAR(20) DEFAULT 'blue',
    color VARCHAR(20) DEFAULT 'blue',
    rating DECIMAL(3,2) DEFAULT 0.00,
    reviews INT DEFAULT 0,
    students INT DEFAULT 0,
    experience VARCHAR(50),
    price DECIMAL(10,2) DEFAULT 0.00,
    online TINYINT(1) DEFAULT 1,
    verified TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE acad_mentor_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mentor_id INT NOT NULL,
    skill_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_acad_ms_mentor FOREIGN KEY (mentor_id) REFERENCES acad_mentors(id) ON DELETE CASCADE,
    CONSTRAINT fk_acad_ms_skill FOREIGN KEY (skill_id) REFERENCES acad_skills(id) ON DELETE CASCADE,
    UNIQUE KEY uq_mentor_skill (mentor_id, skill_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$out .= "<p class='ok'>Tables created successfully.</p>";
alog('Tables created ok');

// ============================================================
// 2. SEED FIELDS
// ============================================================
$out .= "<h2>Step 2: Seeding academic fields</h2>";
alog('Seeding fields');

$fieldIds = [];
foreach ($data['fields'] as $f) {
    $slug = acad_slug($f[0]);
    $fieldIds[$f[0]] = acad_insert_dupe($pdo, 'acad_fields', 'slug=?', [$slug],
        "INSERT INTO acad_fields (name, slug, icon, color, description, status) VALUES (?,?,?,?,?,'active')",
        [$f[0], $slug, $f[1], $f[2], $f[3]]);
}
$out .= "<p class='ok'>" . count($data['fields']) . " fields seeded.</p>";
alog(count($data['fields']) . ' fields seeded');

// ============================================================
// 3. SEED COURSES
// ============================================================
$out .= "<h2>Step 3: Seeding courses</h2>";
alog('Seeding courses');

$courseCount = 0;
$courseKeyToId = []; // key = fieldId|name
foreach ($data['courses'] as $fieldName => $list) {
    if (!isset($fieldIds[$fieldName])) continue;
    $fid = $fieldIds[$fieldName];
    foreach ($list as $c) {
        $slug = acad_slug($c[0]);
        $id = acad_insert_dupe($pdo, 'acad_courses', 'field_id=? AND slug=?', [$fid, $slug],
            "INSERT INTO acad_courses (field_id, name, slug, icon, description, status) VALUES (?,?,?,?,?,'active')",
            [$fid, $c[0], $slug, $c[1], $c[2]]);
        // map unique key fieldId|name -> id (last wins if name repeats across fields)
        $courseKeyToId[$fid . '|' . $c[0]] = $id;
        $courseCount++;
    }
}
$out .= "<p class='ok'>{$courseCount} courses seeded.</p>";
alog($courseCount . ' courses seeded');

// ============================================================
// 4. SEED SUBJECTS
// ============================================================
$out .= "<h2>Step 4: Seeding subjects</h2>";
alog('Seeding subjects');

// Build a course-name -> field-id lookup to disambiguate repeated course names
$subjectIds = [];
$subjectCount = 0;

// We need field_id for each course in subjects data. Build reverse map by name on first occurrence.
// subjects data is keyed by course NAME (which may repeat across fields). We'll attach to all matching course ids.
$courseNameToFieldFid = [];
foreach ($data['courses'] as $fieldName => $list) {
    if (!isset($fieldIds[$fieldName])) continue;
    foreach ($list as $c) {
        $courseNameToFieldFid[$c[0]] = $fieldIds[$fieldName]; // last wins
    }
}

foreach ($data['subjects'] as $courseName => $list) {
    $fid = isset($courseNameToFieldFid[$courseName]) ? $courseNameToFieldFid[$courseName] : null;
    $cid = isset($courseKeyToId[$fid . '|' . $courseName]) ? $courseKeyToId[$fid . '|' . $courseName] : 0;
    if (!$cid) {
        alog('  [skip] no course id for subject group: ' . $courseName);
        continue;
    }
    foreach ($list as $subj) {
        $slug = acad_slug($subj);
        $id = acad_insert_dupe($pdo, 'acad_subjects', 'course_id=? AND slug=?', [$cid, $slug],
            "INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES (?,?,?,?,'active')",
            [$cid, $subj, $slug, $subj]);
        // Key by subject name + course id for uniqueness
        $subjectIds[$cid . '|' . $subj] = $id;
        $subjectCount++;
    }
}
$out .= "<p class='ok'>{$subjectCount} subjects seeded.</p>";
alog($subjectCount . ' subjects seeded');

// ============================================================
// 5. SEED SKILLS (auto-generated from subject name)
// ============================================================
$out .= "<h2>Step 5: Seeding skills</h2>";
alog('Seeding skills');

function acad_skillWords($subject) {
    $keywords = [];
    foreach (['Programming', 'Design', 'Analysis', 'Development', 'Management', 'Engineering', 'Systems', 'Fundamentals', 'Techniques', 'Applications', 'Tools', 'Theory', 'Practicals', 'Projects'] as $k) {
        $keywords[] = $subject . ' ' . $k;
    }
    $keywords[] = 'Introduction to ' . $subject;
    $keywords[] = 'Advanced ' . $subject;
    $keywords[] = $subject . ' Essentials';
    $keywords[] = 'Applied ' . $subject;
    $keywords[] = $subject . ' Lab';
    return array_slice($keywords, 0, 5);
}

$skillCount = 0;
$allSkillIds = [];
foreach ($subjectIds as $key => $subjectId) {
    $subjName = explode('|', $key, 2)[1];
    foreach (acad_skillWords($subjName) as $skill) {
        $stmt = $pdo->prepare("INSERT INTO acad_skills (subject_id, name, description) VALUES (?,?,?)");
        $stmt->execute([$subjectId, $skill, $skill]);
        $allSkillIds[] = (int)$pdo->lastInsertId();
        $skillCount++;
    }
}
$out .= "<p class='ok'>{$skillCount} skills seeded.</p>";
alog($skillCount . ' skills seeded');

// ============================================================
// 6. SEED MENTORS
// ============================================================
$out .= "<h2>Step 6: Seeding mentors</h2>";
alog('Seeding mentors');

$mentorNames = [
    ['John Doe', 'Senior Software Engineer', 'Google'],
    ['Sarah Miller', 'Full Stack Developer', 'Microsoft'],
    ['Alex Kumar', 'Data Scientist', 'Amazon'],
    ['Priya Sharma', 'DevOps Engineer', 'Netflix'],
    ['Mike Roberts', 'Civil Engineer', 'AECOM'],
    ['Lisa Thompson', 'Structural Engineer', 'Arup'],
    ['David Park', 'Embedded Systems Engineer', 'Bosch'],
    ['Emma Brown', 'Network Administrator', 'Cisco'],
    ['Rohan Gupta', 'AI Engineer', 'Tesla'],
    ['Maya Patel', 'Database Administrator', 'Oracle'],
    ['Aisha Khan', 'Mechanical Engineer', 'Tesla'],
    ['Carlos Gomez', 'Electrical Engineer', 'GE'],
    ['Sophia Kim', 'Biotechnologist', 'Novartis'],
    ['James Carter', 'Physicist', 'CERN'],
    ['Anna Martinez', 'Finance Analyst', 'Goldman Sachs'],
    ['Tom Lee', 'Aerospace Engineer', 'SpaceX'],
    ['Laura Adams', 'Public Health Expert', 'WHO'],
    ['David Wilson', 'Architect', 'Foster+Partners'],
    ['Emily Chen', 'Marketing Director', 'HubSpot'],
    ['Richard Green', 'Education Consultant', 'UNESCO'],
];
$colors = ['purple', 'blue', 'green', 'pink', 'orange', 'teal'];
$mentorIds = [];
$count = 0;
foreach ($mentorNames as $i => $m) {
    $c = $colors[$i % count($colors)];
    $stmt = $pdo->prepare("INSERT INTO acad_mentors (name, title, company, avatar, color, rating, reviews, students, experience, price, online, verified) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$m[0], $m[1], $m[2], $c, $c, 4.4 + ($i % 5) * 0.1, 60 + $i * 8, 150 + $i * 12, (4 + $i % 5) . ' years', 32 + ($i % 8) * 4, ($i % 2 === 0 ? 1 : 0), 1]);
    $mentorIds[] = (int)$pdo->lastInsertId();
    $count++;
}
$out .= "<p class='ok'>{$count} mentors seeded.</p>";
alog($count . ' mentors seeded');

// ============================================================
// 7. LINK MENTORS TO SKILLS
// ============================================================
$out .= "<h2>Step 7: Linking mentors to skills</h2>";
alog('Linking mentors to skills');

$linkCount = 0;
if (count($allSkillIds) && count($mentorIds)) {
    $perSlice = max(1, intdiv(count($allSkillIds), count($mentorIds)));
    $idx = 0;
    foreach ($mentorIds as $mid) {
        $slice = array_slice($allSkillIds, $idx, $perSlice);
        $idx += $perSlice;
        if (empty($slice)) $slice = [$allSkillIds[array_rand($allSkillIds)]];
        $stmtL = $pdo->prepare("INSERT IGNORE INTO acad_mentor_skills (mentor_id, skill_id) VALUES (?,?)");
        foreach ($slice as $sid) {
            $stmtL->execute([$mid, $sid]);
            $linkCount++;
        }
    }
}
$out .= "<p class='ok'>{$linkCount} mentor-skill links created.</p>";
alog($linkCount . ' mentor-skill links created');

// ============================================================
// SUMMARY
// ============================================================
$summary = "
<h2>Installation Complete</h2>
<table>
<tr><th>Entity</th><th>Count</th></tr>
<tr><td>Academic Fields</td><td>" . count($data['fields']) . "</td></tr>
<tr><td>Courses</td><td>{$courseCount}</td></tr>
<tr><td>Subjects</td><td>{$subjectCount}</td></tr>
<tr><td>Skills</td><td>{$skillCount}</td></tr>
<tr><td>Mentors</td><td>{$count}</td></tr>
<tr><td>Mentor-Skill Links</td><td>{$linkCount}</td></tr>
</table>
<p>Browse:</p><ul>
<li><a href='../academic-fields.php'>Academic Fields</a></li>
<li><a href='../courses.php'>Courses</a></li>
<li><a href='../subjects.php'>Subjects</a></li>
<li><a href='../skills.php'>Skills</a></li>
<li><a href='../mentors.php'>Mentors</a></li>
</ul><p style='color:#9ca3af'>Tip: Delete this file after installation for security.</p>";

$out .= $summary . "</body></html>";
echo $out;

alog(date('Y-m-d H:i:s') . " Install finished\n");

