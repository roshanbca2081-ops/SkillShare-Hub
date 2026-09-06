-- =============================================
-- SkillShare Hub - Database Views
-- v_course_analytics.sql
-- =============================================

CREATE OR REPLACE VIEW `v_course_analytics` AS
SELECT 
    c.id AS course_id,
    c.title AS course_title,
    c.status,
    c.level,
    c.price,
    c.created_at,
    u.full_name AS mentor_name,
    f.name AS field_name,
    COUNT(DISTINCT e.id) AS total_enrollments,
    COUNT(DISTINCT CASE WHEN e.status = 'active' THEN e.id END) AS active_enrollments,
    COUNT(DISTINCT CASE WHEN e.status = 'completed' THEN e.id END) AS completed_enrollments,
    COUNT(DISTINCT b.id) AS total_bookings,
    COALESCE(SUM(p.amount), 0) AS total_revenue,
    AVG(r.rating) AS average_rating,
    COUNT(DISTINCT r.id) AS total_reviews,
    COUNT(DISTINCT a.id) AS total_assignments,
    COUNT(DISTINCT res.id) AS total_resources
FROM courses c
JOIN users u ON c.mentor_id = u.id
LEFT JOIN academic_fields f ON c.field_id = f.id
LEFT JOIN enrollments e ON c.id = e.course_id AND e.deleted_at IS NULL
LEFT JOIN sessions s ON c.id = s.course_id AND s.deleted_at IS NULL
LEFT JOIN bookings b ON s.id = b.session_id AND b.deleted_at IS NULL
LEFT JOIN payments p ON c.id = p.course_id AND p.status = 'completed' AND p.deleted_at IS NULL
LEFT JOIN ratings r ON u.id = r.mentor_id AND r.deleted_at IS NULL
LEFT JOIN assignments a ON c.id = a.course_id AND a.deleted_at IS NULL
LEFT JOIN resources res ON c.id = res.course_id AND res.deleted_at IS NULL
WHERE c.deleted_at IS NULL
GROUP BY c.id;
