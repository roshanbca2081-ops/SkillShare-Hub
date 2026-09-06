-- =============================================
-- SkillShare Hub - Database Views
-- v_mentor_stats.sql
-- =============================================

CREATE OR REPLACE VIEW `v_mentor_stats` AS
SELECT 
    u.id AS mentor_id,
    u.full_name AS mentor_name,
    u.email,
    u.avatar,
    u.bio,
    u.skills,
    u.title,
    u.location,
    COUNT(DISTINCT c.id) AS total_courses,
    COUNT(DISTINCT s.id) AS total_sessions,
    COUNT(DISTINCT b.id) AS total_bookings,
    COUNT(DISTINCT r.id) AS total_reviews,
    COALESCE(AVG(r.rating), 0) AS average_rating,
    COUNT(DISTINCT res.id) AS total_resources,
    COUNT(DISTINCT rp.id) AS total_research_projects,
    COUNT(DISTINCT a.id) AS total_assignments,
    u.created_at AS member_since
FROM users u
LEFT JOIN courses c ON u.id = c.mentor_id AND c.deleted_at IS NULL
LEFT JOIN sessions s ON u.id = s.mentor_id AND s.deleted_at IS NULL
LEFT JOIN bookings b ON s.id = b.session_id AND b.deleted_at IS NULL
LEFT JOIN ratings r ON u.id = r.mentor_id AND r.deleted_at IS NULL
LEFT JOIN resources res ON u.id = res.mentor_id AND res.deleted_at IS NULL
LEFT JOIN research_projects rp ON u.id = rp.mentor_id AND rp.deleted_at IS NULL
LEFT JOIN assignments a ON u.id = a.mentor_id AND a.deleted_at IS NULL
WHERE u.role = 'mentor' AND u.deleted_at IS NULL
GROUP BY u.id;
