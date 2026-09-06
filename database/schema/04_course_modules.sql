-- =============================================
-- SkillShare Hub - Database Schema
-- Table 04: course_modules
-- =============================================

CREATE TABLE IF NOT EXISTS `course_modules` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(500) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `is_published` TINYINT(1) NOT NULL DEFAULT 0,
    `order_number` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_course_modules_course_id` (`course_id`),
    INDEX `idx_course_modules_order` (`course_id`, `order_number`),
    CONSTRAINT `fk_course_modules_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
