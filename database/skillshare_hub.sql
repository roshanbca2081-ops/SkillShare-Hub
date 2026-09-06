-- =============================================
-- SkillShare Hub - Complete Database Setup
-- =============================================
-- This file creates the complete database structure.
-- Run this file to initialize the database with all tables, views, procedures, triggers, and events.
-- =============================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `skillshare_hub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `skillshare_hub`;

-- =============================================
-- SCHEMA TABLES
-- =============================================
SOURCE schema/01_users.sql;
SOURCE schema/02_academic_fields.sql;
SOURCE schema/03_courses.sql;
SOURCE schema/04_course_modules.sql;
SOURCE schema/05_course_lessons.sql;
SOURCE schema/06_enrollments.sql;
SOURCE schema/07_lesson_progress.sql;
SOURCE schema/08_sessions.sql;
SOURCE schema/09_bookings.sql;
SOURCE schema/10_assignments.sql;
SOURCE schema/11_assignment_submissions.sql;
SOURCE schema/12_resources.sql;
SOURCE schema/13_research_projects.sql;
SOURCE schema/14_research_applications.sql;
SOURCE schema/15_interview_questions.sql;
SOURCE schema/16_certificates.sql;
SOURCE schema/17_payments.sql;
SOURCE schema/18_messages.sql;
SOURCE schema/19_notifications.sql;
SOURCE schema/20_ratings.sql;
SOURCE schema/21_wishlist.sql;
SOURCE schema/22_blog_posts.sql;
SOURCE schema/23_blog_comments.sql;
SOURCE schema/24_system_logs.sql;
SOURCE schema/25_settings.sql;

-- =============================================
-- VIEWS
-- =============================================
SOURCE views/v_course_details.sql;
SOURCE views/v_student_progress.sql;
SOURCE views/v_mentor_stats.sql;
SOURCE views/v_course_analytics.sql;
SOURCE views/v_revenue_summary.sql;
SOURCE views/v_user_activity.sql;

-- =============================================
-- PROCEDURES
-- =============================================
SOURCE procedures/sp_get_user_dashboard.sql;
SOURCE procedures/sp_get_mentor_dashboard.sql;
SOURCE procedures/sp_get_admin_dashboard.sql;
SOURCE procedures/sp_enroll_student.sql;
SOURCE procedures/sp_update_course_progress.sql;
SOURCE procedures/sp_generate_certificate.sql;
SOURCE procedures/sp_process_payment.sql;
SOURCE procedures/sp_get_course_recommendations.sql;
SOURCE procedures/sp_get_top_performers.sql;
SOURCE procedures/sp_cleanup_expired_sessions.sql;

-- =============================================
-- TRIGGERS
-- =============================================
SOURCE triggers/tr_update_course_rating.sql;
SOURCE triggers/tr_update_enrollment_progress.sql;
SOURCE triggers/tr_update_course_student_count.sql;
SOURCE triggers/tr_log_user_activity.sql;
SOURCE triggers/tr_update_certificate_status.sql;

-- =============================================
-- EVENTS (requires MySQL Event Scheduler)
-- =============================================
SOURCE events/ev_update_session_status.sql;
SOURCE events/ev_cleanup_expired_tokens.sql;
SOURCE events/ev_generate_monthly_reports.sql;
SOURCE events/ev_archive_old_notifications.sql;

-- =============================================
-- SEEDERS (optional)
-- =============================================
SOURCE seeders/01_users_seeder.sql;
SOURCE seeders/02_academic_fields_seeder.sql;
SOURCE seeders/03_courses_seeder.sql;
SOURCE seeders/04_sessions_seeder.sql;
SOURCE seeders/05_enrollments_seeder.sql;
SOURCE seeders/06_notifications_seeder.sql;
SOURCE seeders/07_sample_data.sql;

-- =============================================
-- COMPLETE
-- =============================================
SELECT 'SkillShare Hub database setup completed successfully!' AS message;
