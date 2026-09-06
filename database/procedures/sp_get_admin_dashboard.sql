-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_get_admin_dashboard.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_get_admin_dashboard`()
BEGIN
    SELECT COUNT(*) AS total_users FROM users WHERE deleted_at IS NULL;
    SELECT COUNT(*) AS total_mentors FROM users WHERE role = 'mentor' AND deleted_at IS NULL;
    SELECT COUNT(*) AS total_freshers FROM users WHERE role = 'fresher' AND deleted_at IS NULL;
    SELECT COUNT(*) AS total_courses FROM courses WHERE deleted_at IS NULL;
    SELECT COUNT(*) AS total_sessions FROM sessions WHERE deleted_at IS NULL;
    SELECT COUNT(*) AS total_bookings FROM bookings WHERE deleted_at IS NULL;
    SELECT COUNT(*) AS total_enrollments FROM enrollments WHERE deleted_at IS NULL;
    SELECT COALESCE(SUM(amount), 0) AS total_revenue FROM payments WHERE status = 'completed' AND deleted_at IS NULL;

    SELECT 
        DATE(created_at) AS date,
        COUNT(*) AS registrations
    FROM users
    WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date DESC;

    SELECT 
        c.title,
        COUNT(e.id) AS enrollments,
        AVG(r.rating) AS rating
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id AND e.deleted_at IS NULL
    LEFT JOIN ratings r ON c.mentor_id = r.mentor_id AND r.deleted_at IS NULL
    WHERE c.deleted_at IS NULL
    GROUP BY c.id
    ORDER BY enrollments DESC
    LIMIT 10;
END$$

DELIMITER ;
