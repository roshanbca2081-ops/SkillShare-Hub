-- =============================================
-- SkillShare Hub - Triggers
-- tr_update_certificate_status.sql
-- =============================================

DELIMITER $$

CREATE TRIGGER `tr_update_certificate_status` AFTER UPDATE ON `enrollments`
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' AND NEW.deleted_at IS NULL THEN
        CALL sp_generate_certificate(NEW.fresher_id, NEW.course_id);
    END IF;
END$$

DELIMITER ;
