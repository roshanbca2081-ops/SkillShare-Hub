-- =============================================
-- SkillShare Hub - Stored Procedure
-- sp_get_user_dashboard.sql
-- =============================================

DELIMITER $$

CREATE PROCEDURE `sp_get_user_dashboard`(IN p_user_id INT, IN p_role VARCHAR(50))
BEGIN
    DECLARE v_total_courses INT DEFAULT 0;
    DECLARE v_active_courses INT DEFAULT 0;
    DECLARE v_completed_courses INT DEFAULT 0;
    DECLARE v_upcoming_sessions INT DEFAULT 0;
    DECLARE v_unread_messages INT DEFAULT 0;
    DECLARE v_unread_notifications INT DEFAULT 0;

    IF p_role = 'fresher' THEN
        SELECT COUNT(*) INTO v_total_courses FROM enrollments WHERE fresher_id = p_user_id AND status != 'dropped' AND deleted_at IS NULL;
        SELECT COUNT(*) INTO v_active_courses FROM enrollments WHERE fresher_id = p_user_id AND status = 'active' AND deleted_at IS NULL;
        SELECT COUNT(*) INTO v_completed_courses FROM enrollments WHERE fresher_id = p_user_id AND status = 'completed' AND deleted_at IS NULL;
        SELECT COUNT(*) INTO v_upcoming_sessions FROM bookings b JOIN sessions s ON b.session_id = s.id WHERE b.fresher_id = p_user_id AND b.status = 'approved' AND s.scheduled_at > NOW() AND b.deleted_at IS NULL AND s.deleted_at IS NULL;
        SELECT COUNT(*) INTO v_unread_messages FROM messages WHERE receiver_id = p_user_id AND is_read = 0 AND deleted_at IS NULL;
        SELECT COUNT(*) INTO v_unread_notifications FROM notifications WHERE user_id = p_user_id AND is_read = 0 AND deleted_at IS NULL;
    ELSEIF p_role = 'mentor' THEN
        SELECT COUNT(*) INTO v_total_courses FROM courses WHERE mentor_id = p_user_id AND deleted_at IS NULL;
        SELECT COUNT(*) INTO v_active_courses FROM sessions WHERE mentor_id = p_user_id AND status = 'scheduled' AND deleted_at IS NULL;
        SELECT COUNT(*) INTO v_upcoming_sessions FROM sessions WHERE mentor_id = p_user_id AND status = 'scheduled' AND scheduled_at > NOW() AND deleted_at IS NULL;
        SELECT COUNT(*) INTO v_unread_messages FROM messages WHERE receiver_id = p_user_id AND is_read = 0 AND deleted_at IS NULL;
        SELECT COUNT(*) INTO v_unread_notifications FROM notifications WHERE user_id = p_user_id AND is_read = 0 AND deleted_at IS NULL;
    END IF;

    SELECT 
        v_total_courses AS total_courses,
        v_active_courses AS active_courses,
        v_completed_courses AS completed_courses,
        v_upcoming_sessions AS upcoming_sessions,
        v_unread_messages AS unread_messages,
        v_unread_notifications AS unread_notifications;
END$$

DELIMITER ;
