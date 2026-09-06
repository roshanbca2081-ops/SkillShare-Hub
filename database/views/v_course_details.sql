-- =============================================
-- SkillShare Hub - Database Views
-- v_course_details.sql
-- =============================================

CREATE OR REPLACE VIEW `v_course_details` AS
SELECT 
    c.id,
    c.title,
    c.slug,
    c.description,
    c.mentor_id,
    c.field_id,
    c.status,
    c.level,
    c.price,
    c.duration,
    c.thumbnail,
    c.featured,
    c.discount_price,
    c.total_students,
    c.progress,
    c.is_published,
    c.created_at,
    c.updated_at,
    u.full_name AS mentor_name,
    u.avatar AS mentor_avatar,
    f.name AS field_name,
    f.icon AS field_icon,
    f.color AS field_color,
    (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id AND e.status = 'active') AS active_students,
    (SELECT AVG(rating) FROM ratings r WHERE r.mentor_id = c.mentor_id) AS avg_mentor_rating
FROM courses c
JOIN users u ON c.mentor_id = u.id
LEFT JOIN academic_fields f ON c.field_id = f.id
WHERE c.deleted_at IS NULL;
