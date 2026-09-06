-- =============================================
-- SkillShare Hub - Triggers
-- tr_log_user_activity.sql
-- =============================================

DELIMITER $$

CREATE TRIGGER `tr_log_user_activity` AFTER INSERT ON `users`
FOR EACH ROW
BEGIN
    INSERT INTO system_logs (user_id, action, description, ip_address, created_at)
    VALUES (NEW.id, 'user_registered', CONCAT('New user registered: ', NEW.email), NULL, NOW());
END$$

DELIMITER ;
