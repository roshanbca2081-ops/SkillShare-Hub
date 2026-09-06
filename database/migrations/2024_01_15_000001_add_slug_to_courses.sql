-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_15_000001_add_slug_to_courses.sql
-- =============================================

ALTER TABLE `courses`
    ADD COLUMN `slug` VARCHAR(500) NOT NULL AFTER `title`,
    ADD UNIQUE KEY `uk_courses_slug` (`slug`);
