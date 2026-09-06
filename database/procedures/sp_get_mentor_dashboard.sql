-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_get_mentor_dashboard.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_get_mentor_dashboard`(IN p_mentor_id INT)
BEGIN
    SELECT 
        COUNT(DISTINCT c.id) AS total_courses,
        COUNT(DISTINCT s.id) AS total_sessions,
        COUNT(DISTINCT b.id) AS total_bookings,
        COUNT(DISTINCT a.id) AS total_assignments,
        COUNT(DISTINCT r.id) AS total_resources,
        COALESCE(AVG(rt.rating), 0) AS average_rating,
        COUNT(DISTINCT rt.id) AS total_reviews
    FROM users u
    LEFT JOIN courses c ON u.id = c.mentor_id AND c.deleted_at IS NULL
    LEFT JOIN sessions s ON u.id = s.mentor_id AND s.deleted_at IS NULL
    LEFT JOIN bookings b ON s.id = b.session_id AND b.deleted_at IS NULL
    LEFT JOIN assignments a ON u.id = a.mentor_id AND a.deleted_at IS NULL
    LEFT JOIN resources r ON u.id = r.mentor_id AND r.deleted_at IS NULL
    LEFT JOIN ratings rt ON u.id = rt.mentor_id AND rt.deleted_at IS NULL
    WHERE u.id = p_mentor_id AND u.role = 'mentor' AND u.deleted_at IS NULL;

    SELECT 
        c.id, c.title, c.status, c.level, c.price, c.created_at,
        COUNT(DISTINCT e.id) AS enrollments,
        AVG(rt.rating) AS rating
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id AND e.deleted_at IS NULL
    LEFT JOIN ratings rt ON c.mentor_id = rt.mentor_id AND rt.deleted_at IS NULL
    WHERE c.mentor_id = p_mentor_id AND c.deleted_at IS NULL
    GROUP BY c.id
    ORDER BY c.created_at DESC
    LIMIT 10;

    SELECT 
        s.id, s.title, s.status, s.scheduled_at, s.duration, s.price,
        COUNT(b.id) AS bookings_count
    FROM sessions s
    LEFT JOIN bookings b ON s.id = b.session_id AND b.deleted_at IS NULL
    WHERE s.mentor_id = p_mentor_id AND s.deleted_at IS NULL
    ORDER BY s.scheduled_at DESC
    LIMIT 10;
END$$

DELIMITER ;
