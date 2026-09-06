-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000012_create_resources_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `resources` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `mentor_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(500) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `file_url` VARCHAR(500) NOT NULL,
    `file_type` VARCHAR(100) DEFAULT NULL,
    `is_public` TINYINT(1) NOT NULL DEFAULT 1,
    `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_resources_course_id` (`course_id`),
    INDEX `idx_resources_mentor_id` (`mentor_id`),
    INDEX `idx_resources_is_public` (`is_public`),
    CONSTRAINT `fk_resources_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_resources_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
