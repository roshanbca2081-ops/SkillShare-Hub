-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000005_create_course_lessons_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `course_lessons` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `module_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(500) NOT NULL,
    `content` TEXT DEFAULT NULL,
    `video_url` VARCHAR(500) DEFAULT NULL,
    `resource_url` VARCHAR(500) DEFAULT NULL,
    `duration` INT UNSIGNED DEFAULT NULL,
    `is_free` TINYINT(1) NOT NULL DEFAULT 0,
    `is_published` TINYINT(1) NOT NULL DEFAULT 0,
    `order_number` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_course_lessons_module_id` (`module_id`),
    INDEX `idx_course_lessons_order` (`module_id`, `order_number`),
    CONSTRAINT `fk_course_lessons_module` FOREIGN KEY (`module_id`) REFERENCES `course_modules`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
