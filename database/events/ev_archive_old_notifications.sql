-- =============================================
-- SkillShare Hub - Events
-- ev_archive_old_notifications.sql
-- =============================================

-- Event: Archive/soft delete old notifications
-- Runs every week on Sunday at 2:00 AM
-- Note: MySQL Event Scheduler must be enabled: SET GLOBAL event_scheduler = ON;

DELIMITER $$

CREATE EVENT IF NOT EXISTS `ev_archive_old_notifications`
ON SCHEDULE EVERY 1 WEEK
STARTS CURRENT_TIMESTAMP + INTERVAL 1 WEEK
DO
BEGIN
    UPDATE notifications
    SET deleted_at = NOW()
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
      AND deleted_at IS NULL;
END$$

DELIMITER ;
