-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_get_course_recommendations.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_get_course_recommendations`(IN p_fresher_id INT, IN p_limit INT)
BEGIN
    SELECT 
        c.id,
        c.title,
        c.slug,
        c.description,
        c.price,
        c.duration,
        c.level,
        c.thumbnail,
        c.total_students,
        f.name AS field_name,
        u.full_name AS mentor_name,
        AVG(r.rating) AS avg_rating,
        COUNT(DISTINCT e.id) AS enrollment_count
    FROM courses c
    JOIN academic_fields f ON c.field_id = f.id
    JOIN users u ON c.mentor_id = u.id
    LEFT JOIN enrollments e ON c.id = e.course_id AND e.fresher_id = p_fresher_id AND e.deleted_at IS NULL
    LEFT JOIN ratings r ON u.id = r.mentor_id AND r.deleted_at IS NULL
    WHERE c.status = 'active' AND c.deleted_at IS NULL
      AND e.id IS NULL
    GROUP BY c.id
    ORDER BY c.featured DESC, c.total_students DESC, avg_rating DESC
    LIMIT p_limit;
END$$

DELIMITER ;
