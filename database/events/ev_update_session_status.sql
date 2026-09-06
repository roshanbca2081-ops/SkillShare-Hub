-- =============================================
-- SkillShare Hub - Events
-- ev_update_session_status.sql
-- =============================================

-- Event: Update session status based on scheduled time
-- Runs every hour
-- Note: MySQL Event Scheduler must be enabled: SET GLOBAL event_scheduler = ON;

DELIMITER $$

CREATE EVENT IF NOT EXISTS `ev_update_session_status`
ON SCHEDULE EVERY 1 HOUR
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    UPDATE sessions
    SET status = 'ongoing'
    WHERE status = 'scheduled' 
      AND scheduled_at <= NOW() 
      AND scheduled_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
      AND deleted_at IS NULL;

    UPDATE sessions
    SET status = 'completed'
    WHERE status IN ('scheduled', 'ongoing')
      AND scheduled_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)
      AND deleted_at IS NULL;

    UPDATE bookings
    SET status = 'completed'
    WHERE status = 'approved'
      AND session_id IN (SELECT id FROM sessions WHERE status = 'completed' AND deleted_at IS NULL)
      AND deleted_at IS NULL;
END$$

DELIMITER ;
