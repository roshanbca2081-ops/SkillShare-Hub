-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_enroll_student.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_enroll_student`(
    IN p_fresher_id INT,
    IN p_course_id INT,
    IN p_status VARCHAR(50)
)
BEGIN
    DECLARE v_enrollment_id INT;

    INSERT INTO enrollments (fresher_id, course_id, status, enrolled_at)
    VALUES (p_fresher_id, p_course_id, p_status, NOW())
    ON DUPLICATE KEY UPDATE
        status = VALUES(status),
        updated_at = NOW();

    SET v_enrollment_id = LAST_INSERT_ID();

    IF v_enrollment_id = 0 THEN
        SELECT id INTO v_enrollment_id FROM enrollments WHERE fresher_id = p_fresher_id AND course_id = p_course_id;
    END IF;

    SELECT 
        e.*,
        c.title AS course_title,
        u.full_name AS fresher_name
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    JOIN users u ON e.fresher_id = u.id
    WHERE e.id = v_enrollment_id;
END$$

DELIMITER ;
