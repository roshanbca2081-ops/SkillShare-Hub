-- =============================================
-- SkillShare Hub - Database Views
-- v_student_progress.sql
-- =============================================

CREATE OR REPLACE VIEW `v_student_progress` AS
SELECT 
    e.id AS enrollment_id,
    e.fresher_id,
    u.full_name AS fresher_name,
    u.email AS fresher_email,
    e.course_id,
    c.title AS course_title,
    e.status AS enrollment_status,
    e.progress,
    e.enrolled_at,
    e.last_accessed_at,
    e.completed_at,
    (SELECT COUNT(*) FROM lesson_progress lp WHERE lp.fresher_id = e.fresher_id AND lp.completed = 1) AS completed_lessons,
    (SELECT COUNT(*) FROM course_lessons cl JOIN course_modules cm ON cl.module_id = cm.id WHERE cm.course_id = e.course_id) AS total_lessons,
    (SELECT COUNT(*) FROM assignments a WHERE a.course_id = e.course_id) AS total_assignments,
    (SELECT COUNT(*) FROM assignment_submissions s WHERE s.fresher_id = e.fresher_id AND s.assignment_id IN (SELECT id FROM assignments WHERE course_id = e.course_id)) AS submitted_assignments
FROM enrollments e
JOIN users u ON e.fresher_id = u.id
JOIN courses c ON e.course_id = c.id
WHERE e.deleted_at IS NULL;
