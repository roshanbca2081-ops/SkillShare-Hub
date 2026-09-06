-- =============================================
-- SkillShare Hub - Triggers
-- tr_update_enrollment_progress.sql
-- =============================================

DELIMITER $$

CREATE TRIGGER `tr_update_enrollment_progress` AFTER UPDATE ON `lesson_progress`
FOR EACH ROW
BEGIN
    IF NEW.completed = 1 AND OLD.completed = 0 THEN
        CALL sp_update_course_progress(NEW.fresher_id, 
            (SELECT cm.course_id FROM course_lessons cl JOIN course_modules cm ON cl.module_id = cm.id WHERE cl.id = NEW.lesson_id)
        );
    END IF;
END$$

DELIMITER ;
