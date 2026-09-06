-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000008_create_sessions_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `sessions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(500) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `mentor_id` INT UNSIGNED NOT NULL,
    `course_id` INT UNSIGNED DEFAULT NULL,
    `status` ENUM('scheduled', 'ongoing', 'completed', 'cancelled', 'pending') NOT NULL DEFAULT 'pending',
    `scheduled_at` DATETIME NOT NULL,
    `duration` INT UNSIGNED DEFAULT NULL,
    `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `is_free` TINYINT(1) NOT NULL DEFAULT 0,
    `max_participants` INT UNSIGNED DEFAULT NULL,
    `meeting_link` VARCHAR(500) DEFAULT NULL,
    `recording_url` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_sessions_mentor_id` (`mentor_id`),
    INDEX `idx_sessions_course_id` (`course_id`),
    INDEX `idx_sessions_status` (`status`),
    INDEX `idx_sessions_scheduled_at` (`scheduled_at`),
    CONSTRAINT `fk_sessions_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_sessions_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
