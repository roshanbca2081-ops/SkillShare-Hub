-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000006_create_enrollments_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `enrollments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fresher_id` INT UNSIGNED NOT NULL,
    `course_id` INT UNSIGNED NOT NULL,
    `status` ENUM('active', 'completed', 'dropped', 'paused') NOT NULL DEFAULT 'active',
    `enrolled_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_accessed_at` DATETIME DEFAULT NULL,
    `progress` INT UNSIGNED NOT NULL DEFAULT 0,
    `completed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    UNIQUE KEY `uk_enrollments_fresher_course` (`fresher_id`, `course_id`),
    INDEX `idx_enrollments_fresher_id` (`fresher_id`),
    INDEX `idx_enrollments_course_id` (`course_id`),
    INDEX `idx_enrollments_status` (`status`),
    CONSTRAINT `fk_enrollments_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_enrollments_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
