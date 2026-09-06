-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000007_create_lesson_progress_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `lesson_progress` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fresher_id` INT UNSIGNED NOT NULL,
    `lesson_id` INT UNSIGNED NOT NULL,
    `completed` TINYINT(1) NOT NULL DEFAULT 0,
    `completed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_lesson_progress_fresher_lesson` (`fresher_id`, `lesson_id`),
    INDEX `idx_lesson_progress_fresher_id` (`fresher_id`),
    INDEX `idx_lesson_progress_lesson_id` (`lesson_id`),
    CONSTRAINT `fk_lesson_progress_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_lesson_progress_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `course_lessons`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
