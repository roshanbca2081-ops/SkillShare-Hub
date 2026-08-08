<?php
// SkillShare Hub - Generate a complete .sql seed file from academic_data.php
// Run via CLI: php academic_export_sql.php  ->  writes academic_seed_full.sql
// Then import: mysql -u root skillsharehub < academic_seed_full.sql

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(600);

$data = require __DIR__ . '/academic_data.php';

$sql = "-- SkillShare Hub Academic Navigation - Full Seed Dump\n";
$sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

// Helpers
function acad_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    if ($text === '') $text = 'item';
    return $text;
}
function q($v) {
    return "'" . str_replace("'", "''", $v) . "'";
}

// ---- Fields ----
$sql .= "-- Fields\n";
$sql .= "INSERT INTO acad_fields (name, slug, icon, color, description, status) VALUES\n";
$rows = [];
foreach ($data['fields'] as $i => $f) {
    $rows[] = "(" . q($f[0]) . "," . q(acad_slug($f[0])) . "," . q($f[1]) . "," . q($f[2]) . "," . q($f[3]) . ",'active')";
}
$sql .= implode(",\n", $rows) . ";\n\n";

// Map field name -> id (assume auto increments 1..30 in order)
// We'll use variable-free approach: assume ids 1..N in insertion order.
$fieldNames = array_column($data['fields'], 0);
$fieldIdByName = [];
foreach ($fieldNames as $idx => $name) { $fieldIdByName[$name] = $idx + 1; }

// ---- Courses ----
$sql .= "-- Courses\n";
$courseRows = [];
$courseKeyToId = [];
$cid = 1;
foreach ($data['courses'] as $fieldName => $list) {
    if (!isset($fieldIdByName[$fieldName])) continue;
    $fid = $fieldIdByName[$fieldName];
    foreach ($list as $c) {
        $courseRows[] = "(" . $fid . "," . q($c[0]) . "," . q(acad_slug($c[0])) . "," . q($c[1]) . "," . q($c[2]) . ",'active')";
        $courseKeyToId[$fid . '|' . $c[0]] = $cid;
        $cid++;
    }
}
$sql .= "INSERT INTO acad_courses (field_id, name, slug, icon, description, status) VALUES\n";
$sql .= implode(",\n", $courseRows) . ";\n\n";

// ---- Subjects ----
$sql .= "-- Subjects\n";
$subjectRows = [];
$subjectId = 1;
$listOfSubject = [];
foreach ($data['subjects'] as $courseName => $list) {
    // find field id for this course name (first field that has it)
    $fid = null;
    foreach ($data['courses'] as $fn => $clist) {
        foreach ($clist as $c) {
            if ($c[0] === $courseName) { $fid = (isset($fieldIdByName[$fn]) ? $fieldIdByName[$fn] : null); break 2; }
        }
    }
    if ($fid === null) continue;
    $cKey = $fid . '|' . $courseName;
    $cid = isset($courseKeyToId[$cKey]) ? $courseKeyToId[$cKey] : 0;
    if (!$cid) continue;
    foreach ($list as $subj) {
        $subjectRows[] = "(" . $cid . "," . q($subj) . "," . q(acad_slug($subj)) . "," . q($subj) . ",'active')";
        $listOfSubject[] = $subj;
        $subjectId++;
    }
}
$sql .= "INSERT INTO acad_subjects (course_id, name, slug, description, status) VALUES\n";
$sql .= implode(",\n", $subjectRows) . ";\n\n";

// ---- Skills (auto-generated) ----
$sql .= "-- Skills\n";
function acad_skillWords2($subject) {
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
$skillRows = [];
$skillId = 1;
$subjectStartId = 1;
foreach ($data['subjects'] as $courseName => $list) {
    foreach ($list as $subj) {
        foreach (acad_skillWords2($subj) as $skill) {
            $skillRows[] = "(" . $subjectStartId . "," . q($skill) . "," . q($skill) . ")";
            $skillId++;
        }
        $subjectStartId++;
    }
}
$sql .= "INSERT INTO acad_skills (subject_id, name, description) VALUES\n";
$sql .= implode(",\n", $skillRows) . ";\n\n";

// ---- Mentors ----
$sql .= "-- Mentors\n";
$mentorRows = [];
$mentorNames = [
    ['John Doe', 'Senior Software Engineer', 'Google'], ['Sarah Miller', 'Full Stack Developer', 'Microsoft'],
    ['Alex Kumar', 'Data Scientist', 'Amazon'], ['Priya Sharma', 'DevOps Engineer', 'Netflix'],
    ['Mike Roberts', 'Civil Engineer', 'AECOM'], ['Lisa Thompson', 'Structural Engineer', 'Arup'],
    ['David Park', 'Embedded Systems Engineer', 'Bosch'], ['Emma Brown', 'Network Administrator', 'Cisco'],
    ['Rohan Gupta', 'AI Engineer', 'Tesla'], ['Maya Patel', 'Database Administrator', 'Oracle'],
    ['Aisha Khan', 'Mechanical Engineer', 'Tesla'], ['Carlos Gomez', 'Electrical Engineer', 'GE'],
    ['Sophia Kim', 'Biotechnologist', 'Novartis'], ['James Carter', 'Physicist', 'CERN'],
    ['Anna Martinez', 'Finance Analyst', 'Goldman Sachs'], ['Tom Lee', 'Aerospace Engineer', 'SpaceX'],
    ['Laura Adams', 'Public Health Expert', 'WHO'], ['David Wilson', 'Architect', 'Foster+Partners'],
    ['Emily Chen', 'Marketing Director', 'HubSpot'], ['Richard Green', 'Education Consultant', 'UNESCO'],
];
$colors = ['purple', 'blue', 'green', 'pink', 'orange', 'teal'];
foreach ($mentorNames as $i => $m) {
    $c = $colors[$i % count($colors)];
    $mentorRows[] = "(" . q($m[0]) . "," . q($m[1]) . "," . q($m[2]) . "," . q($c) . "," . q($c) . "," . number_format(4.4 + ($i % 5) * 0.1, 2) . "," . (60 + $i * 8) . "," . (150 + $i * 12) . "," . q((4 + $i % 5) . ' years') . "," . (32 + ($i % 8) * 4) . "," . ($i % 2 === 0 ? 1 : 0) . ",1)";
}
$sql .= "INSERT INTO acad_mentors (name, title, company, avatar, color, rating, reviews, students, experience, price, online, verified) VALUES\n";
$sql .= implode(",\n", $mentorRows) . ";\n\n";

// ---- Mentor Skills links ----
$sql .= "-- Mentor-Skill Links (distribute skills across mentors)\n";
$totalSkills = $skillId - 1;
$numMentors = count($mentorNames);
$linkRows = [];
$perSlice = max(1, intdiv($totalSkills, $numMentors));
$currentSkill = 1;
for ($m = 1; $m <= $numMentors; $m++) {
    $start = $currentSkill;
    $end = min($totalSkills, $currentSkill + $perSlice - 1);
    for ($s = $start; $s <= $end; $s++) {
        $linkRows[] = "(" . $m . "," . $s . ")";
    }
    $currentSkill += $perSlice;
}
$sql .= "INSERT INTO acad_mentor_skills (mentor_id, skill_id) VALUES\n";
$sql .= implode(",\n", $linkRows) . ";\n\n";

$sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

$file = __DIR__ . '/academic_seed_full.sql';
file_put_contents($file, $sql);
echo "Wrote " . filesize($file) . " bytes to $file\n";
