-- =============================================
-- SkillShare Hub - Database Schema
-- Table 11: assignment_submissions
-- =============================================

CREATE TABLE IF NOT EXISTS `assignment_submissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `assignment_id` INT UNSIGNED NOT NULL,
    `fresher_id` INT UNSIGNED NOT NULL,
    `submission_text` TEXT DEFAULT NULL,
    `submission_file` VARCHAR(500) DEFAULT NULL,
    `status` ENUM('submitted', 'graded', 'returned') NOT NULL DEFAULT 'submitted',
    `score` INT UNSIGNED DEFAULT NULL,
    `feedback` TEXT DEFAULT NULL,
    `submitted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_assignment_submissions_assignment_id` (`assignment_id`),
    INDEX `idx_assignment_submissions_fresher_id` (`fresher_id`),
    INDEX `idx_assignment_submissions_status` (`status`),
    UNIQUE KEY `uk_assignment_submissions_assignment_fresher` (`assignment_id`, `fresher_id`),
    CONSTRAINT `fk_assignment_submissions_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_assignment_submissions_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
