-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_get_top_performers.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_get_top_performers`(IN p_limit INT, IN p_course_id INT)
BEGIN
    IF p_course_id > 0 THEN
        SELECT 
            u.id AS fresher_id,
            u.full_name,
            u.avatar,
            e.progress,
            e.completed_at,
            COUNT(DISTINCT lp.id) AS completed_lessons,
            AVG(rt.rating) AS avg_rating_given
        FROM enrollments e
        JOIN users u ON e.fresher_id = u.id
        LEFT JOIN lesson_progress lp ON e.fresher_id = lp.fresher_id AND lp.completed = 1 AND lp.deleted_at IS NULL
        LEFT JOIN ratings rt ON u.id = rt.fresher_id AND rt.deleted_at IS NULL
        WHERE e.course_id = p_course_id AND e.deleted_at IS NULL
        GROUP BY u.id
        ORDER BY e.progress DESC, e.completed_at DESC
        LIMIT p_limit;
    ELSE
        SELECT 
            u.id AS fresher_id,
            u.full_name,
            u.avatar,
            COUNT(DISTINCT e.id) AS courses_completed,
            AVG(e.progress) AS avg_progress
        FROM enrollments e
        JOIN users u ON e.fresher_id = u.id
        WHERE e.status = 'completed' AND e.deleted_at IS NULL
        GROUP BY u.id
        ORDER BY courses_completed DESC
        LIMIT p_limit;
    END IF;
END$$

DELIMITER ;
