-- =============================================
-- SkillShare Hub - Migration
-- 2024_02_15_000001_add_soft_deletes.sql
-- =============================================

-- All tables already have `deleted_at` column in base schema.
-- This migration ensures all tables have soft deletes properly indexed.

ALTER TABLE `users` ADD INDEX `idx_users_deleted_at` (`deleted_at`);
ALTER TABLE `academic_fields` ADD INDEX `idx_academic_fields_deleted_at` (`deleted_at`);
ALTER TABLE `courses` ADD INDEX `idx_courses_deleted_at` (`deleted_at`);
ALTER TABLE `course_modules` ADD INDEX `idx_course_modules_deleted_at` (`deleted_at`);
ALTER TABLE `course_lessons` ADD INDEX `idx_course_lessons_deleted_at` (`deleted_at`);
ALTER TABLE `enrollments` ADD INDEX `idx_enrollments_deleted_at` (`deleted_at`);
ALTER TABLE `lesson_progress` ADD INDEX `idx_lesson_progress_deleted_at` (`deleted_at`);
ALTER TABLE `sessions` ADD INDEX `idx_sessions_deleted_at` (`deleted_at`);
ALTER TABLE `bookings` ADD INDEX `idx_bookings_deleted_at` (`deleted_at`);
ALTER TABLE `assignments` ADD INDEX `idx_assignments_deleted_at` (`deleted_at`);
ALTER TABLE `assignment_submissions` ADD INDEX `idx_assignment_submissions_deleted_at` (`deleted_at`);
ALTER TABLE `resources` ADD INDEX `idx_resources_deleted_at` (`deleted_at`);
ALTER TABLE `research_projects` ADD INDEX `idx_research_projects_deleted_at` (`deleted_at`);
ALTER TABLE `research_applications` ADD INDEX `idx_research_applications_deleted_at` (`deleted_at`);
ALTER TABLE `interview_questions` ADD INDEX `idx_interview_questions_deleted_at` (`deleted_at`);
ALTER TABLE `certificates` ADD INDEX `idx_certificates_deleted_at` (`deleted_at`);
ALTER TABLE `payments` ADD INDEX `idx_payments_deleted_at` (`deleted_at`);
ALTER TABLE `messages` ADD INDEX `idx_messages_deleted_at` (`deleted_at`);
ALTER TABLE `notifications` ADD INDEX `idx_notifications_deleted_at` (`deleted_at`);
ALTER TABLE `ratings` ADD INDEX `idx_ratings_deleted_at` (`deleted_at`);
ALTER TABLE `wishlist` ADD INDEX `idx_wishlist_deleted_at` (`deleted_at`);
ALTER TABLE `blog_posts` ADD INDEX `idx_blog_posts_deleted_at` (`deleted_at`);
ALTER TABLE `blog_comments` ADD INDEX `idx_blog_comments_deleted_at` (`deleted_at`);
ALTER TABLE `system_logs` ADD INDEX `idx_system_logs_deleted_at` (`deleted_at`);
ALTER TABLE `settings` ADD INDEX `idx_settings_deleted_at` (`deleted_at`);
