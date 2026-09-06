-- =============================================
-- SkillShare Hub - Database Views
-- v_user_activity.sql
-- =============================================

CREATE OR REPLACE VIEW `v_user_activity` AS
SELECT 
    u.id AS user_id,
    u.full_name,
    u.email,
    u.role,
    u.is_active,
    u.created_at AS registered_at,
    COUNT(DISTINCT e.id) AS courses_enrolled,
    COUNT(DISTINCT b.id) AS sessions_booked,
    COUNT(DISTINCT m.id) AS messages_sent,
    COUNT(DISTINCT n.id) AS notifications_received,
    MAX(CASE WHEN m.sender_id = u.id THEN m.created_at END) AS last_message_sent,
    MAX(CASE WHEN b.fresher_id = u.id THEN b.booking_date END) AS last_booking,
    MAX(e.last_accessed_at) AS last_accessed
FROM users u
LEFT JOIN enrollments e ON u.id = e.fresher_id AND e.deleted_at IS NULL
LEFT JOIN bookings b ON u.id = b.fresher_id AND b.deleted_at IS NULL
LEFT JOIN messages m ON u.id = m.sender_id AND m.deleted_at IS NULL
LEFT JOIN notifications n ON u.id = n.user_id AND n.deleted_at IS NULL
WHERE u.deleted_at IS NULL
GROUP BY u.id;
