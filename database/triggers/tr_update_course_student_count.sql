-- =============================================
-- SkillShare Hub - Triggers
-- tr_update_course_student_count.sql
-- =============================================

DELIMITER $$

CREATE TRIGGER `tr_update_course_student_count` AFTER INSERT ON `enrollments`
FOR EACH ROW
BEGIN
    IF NEW.status != 'dropped' AND NEW.deleted_at IS NULL THEN
        UPDATE courses
        SET total_students = total_students + 1,
            updated_at = NOW()
        WHERE id = NEW.course_id AND deleted_at IS NULL;
    END IF;
END$$

DELIMITER ;
