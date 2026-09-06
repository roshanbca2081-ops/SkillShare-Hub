-- =============================================
-- SkillShare Hub - Triggers
-- tr_update_course_rating.sql
-- =============================================

DELIMITER $$

CREATE TRIGGER `tr_update_course_rating` AFTER INSERT ON `ratings`
FOR EACH ROW
BEGIN
    IF NEW.mentor_id IS NOT NULL AND NEW.mentor_id > 0 THEN
        UPDATE users
        SET updated_at = NOW()
        WHERE id = NEW.mentor_id AND deleted_at IS NULL;
    END IF;
END$$

DELIMITER ;
