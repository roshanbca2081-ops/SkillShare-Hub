-- =============================================
-- SkillShare Hub - Events
-- ev_generate_monthly_reports.sql
-- =============================================

-- Event: Generate monthly analytics reports
-- Runs on the first day of every month at 1:00 AM
-- Note: MySQL Event Scheduler must be enabled: SET GLOBAL event_scheduler = ON;

DELIMITER $$

CREATE EVENT IF NOT EXISTS `ev_generate_monthly_reports`
ON SCHEDULE EVERY 1 MONTH
STARTS CURRENT_TIMESTAMP + INTERVAL 1 MONTH
DO
BEGIN
    INSERT INTO system_logs (action, description, created_at)
    VALUES (
        'monthly_report',
        CONCAT('Monthly report generated for ', DATE_FORMAT(NOW(), '%Y-%m')),
        NOW()
    );

    UPDATE courses
    SET total_students = (
        SELECT COUNT(*) FROM enrollments 
        WHERE course_id = courses.id AND status != 'dropped' AND deleted_at IS NULL
    )
    WHERE deleted_at IS NULL;
END$$

DELIMITER ;
