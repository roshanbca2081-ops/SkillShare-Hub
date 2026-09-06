-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_cleanup_expired_sessions.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_cleanup_expired_sessions`()
BEGIN
    UPDATE sessions
    SET status = 'completed'
    WHERE status = 'scheduled' 
      AND scheduled_at < NOW() 
      AND deleted_at IS NULL;

    UPDATE sessions
    SET status = 'cancelled'
    WHERE status = 'pending' 
      AND scheduled_at < DATE_SUB(NOW(), INTERVAL 24 HOUR) 
      AND deleted_at IS NULL;

    UPDATE bookings
    SET status = 'completed'
    WHERE status = 'approved' 
      AND session_id IN (SELECT id FROM sessions WHERE status = 'completed')
      AND deleted_at IS NULL;

    SELECT ROW_COUNT() AS updated_sessions;
END$$

DELIMITER ;
