-- =============================================
-- SkillShare Hub - Migration
-- 2024_02_01_000001_add_indexes_for_performance.sql
-- =============================================

-- Add composite indexes for common query patterns
ALTER TABLE `enrollments`
    ADD INDEX `idx_enrollments_fresher_status` (`fresher_id`, `status`),
    ADD INDEX `idx_enrollments_course_status` (`course_id`, `status`);

ALTER TABLE `bookings`
    ADD INDEX `idx_bookings_fresher_status` (`fresher_id`, `status`),
    ADD INDEX `idx_bookings_session_status` (`session_id`, `status`);

ALTER TABLE `sessions`
    ADD INDEX `idx_sessions_mentor_status` (`mentor_id`, `status`);

ALTER TABLE `courses`
    ADD INDEX `idx_courses_field_status` (`field_id`, `status`),
    ADD INDEX `idx_courses_mentor_status` (`mentor_id`, `status`);

ALTER TABLE `notifications`
    ADD INDEX `idx_notifications_user_read` (`user_id`, `is_read`);

ALTER TABLE `messages`
    ADD INDEX `idx_messages_conversation` (`sender_id`, `receiver_id`, `created_at`);
