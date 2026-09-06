-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_update_course_progress.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_update_course_progress`(
    IN p_fresher_id INT,
    IN p_course_id INT
)
BEGIN
    DECLARE v_total_lessons INT DEFAULT 0;
    DECLARE v_completed_lessons INT DEFAULT 0;
    DECLARE v_progress INT DEFAULT 0;

    SELECT COUNT(*) INTO v_total_lessons
    FROM course_lessons cl
    JOIN course_modules cm ON cl.module_id = cm.id
    WHERE cm.course_id = p_course_id AND cl.deleted_at IS NULL;

    SELECT COUNT(*) INTO v_completed_lessons
    FROM lesson_progress lp
    JOIN course_lessons cl ON lp.lesson_id = cl.id
    JOIN course_modules cm ON cl.module_id = cm.id
    WHERE lp.fresher_id = p_fresher_id AND lp.completed = 1 AND cm.course_id = p_course_id AND lp.deleted_at IS NULL;

    IF v_total_lessons > 0 THEN
        SET v_progress = ROUND((v_completed_lessons / v_total_lessons) * 100);
    END IF;

    UPDATE enrollments
    SET progress = v_progress,
        updated_at = NOW()
    WHERE fresher_id = p_fresher_id AND course_id = p_course_id AND deleted_at IS NULL;

    IF v_progress = 100 THEN
        UPDATE enrollments
        SET status = 'completed', completed_at = NOW(), updated_at = NOW()
        WHERE fresher_id = p_fresher_id AND course_id = p_course_id AND deleted_at IS NULL;
    END IF;

    SELECT v_progress AS progress, v_total_lessons AS total_lessons, v_completed_lessons AS completed_lessons;
END$$

DELIMITER ;
