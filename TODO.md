# TODO - Academic Fields DB-Driven Navigation

## Steps
- [x] Create dedicated DB tables (acad_fields, acad_courses, acad_subjects, acad_skills, acad_mentors, acad_mentor_skills) in skillsharehub
- [x] Create shared DB helper (`frontend/components/acad-db.php`)
- [x] Build `academic-fields.php` (field cards, no skill lists, shared navbar)
- [x] Build `courses.php?field_id=` (courses for field)
- [x] Build `subjects.php?course_id=` (subjects for course)
- [x] Build `skills.php?subject_id=` (skills for subject)
- [x] Build `mentors.php?skill_id=` (mentors for skill)
- [x] Breadcrumbs + Back buttons on each level
- [x] Update navbar link (academic-filed.php -> academic-fields.php)
- [x] Fix api/config.php DB name to skillsharehub
- [x] Replace old academic-filed.php behavior (redirect)

## Expanded data (user request)
- [x] 30 academic fields
- [x] ~200 courses (194 seeded)
- [x] ~400+ subjects (795 records, each course has its subjects)
- [x] Auto-generated skills (3976) for every subject
- [x] 20 mentors + mentor-skill links (3960)
- [x] `database/academic_data.php` - central data source
- [x] `database/academic_install.php` - idempotent installer
- [x] `database/academic_export_sql.php` - generates full SQL seed
- [x] `database/academic_seed_full.sql` - complete SQL dump (imported)

## Final fix (this session)
- [x] Rewrote root `courses.php` as DB-driven PHP/MySQL page matching existing page style
  - [x] Reads & validates field_id from GET
  - [x] Queries acad_courses WHERE field_id = ? (prepared statements)
  - [x] Breadcrumb: Academic Fields > [Field] > Courses
  - [x] Title: "Courses in [Field]"
  - [x] Shows ONLY that field's courses -> subjects.php?course_id=X
  - [x] "No courses available for this academic field." empty state
  - [x] "Back to Academic Fields" button
- [x] Verified php -l passes for courses.php, subjects.php, skills.php, mentors.php, academic-fields.php

