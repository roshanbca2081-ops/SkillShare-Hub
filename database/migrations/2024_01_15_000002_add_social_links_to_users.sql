-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_15_000002_add_social_links_to_users.sql
-- =============================================

ALTER TABLE `users`
    ADD COLUMN `linkedin_url` VARCHAR(500) DEFAULT NULL AFTER `website`,
    ADD COLUMN `github_url` VARCHAR(500) DEFAULT NULL AFTER `linkedin_url`,
    ADD COLUMN `youtube_url` VARCHAR(500) DEFAULT NULL AFTER `github_url`;
