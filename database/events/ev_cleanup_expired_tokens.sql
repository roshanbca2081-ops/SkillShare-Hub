-- =============================================
-- SkillShare Hub - Events
-- ev_cleanup_expired_tokens.sql
-- =============================================

-- Event: Clean up expired password reset tokens
-- Runs every day at midnight
-- Note: MySQL Event Scheduler must be enabled: SET GLOBAL event_scheduler = ON;

DELIMITER $$

CREATE EVENT IF NOT EXISTS `ev_cleanup_expired_tokens`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP + INTERVAL 1 DAY
DO
BEGIN
    UPDATE users
    SET reset_token = NULL, reset_token_expiry = NULL
    WHERE reset_token_expiry < NOW()
      AND deleted_at IS NULL;

    DELETE FROM system_logs
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
      AND deleted_at IS NULL;
END$$

DELIMITER ;
