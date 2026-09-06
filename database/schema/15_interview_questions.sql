-- =============================================
-- SkillShare Hub - Database Schema
-- Table 15: interview_questions
-- =============================================

CREATE TABLE IF NOT EXISTS `interview_questions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `mentor_id` INT UNSIGNED NOT NULL,
    `field_id` INT UNSIGNED DEFAULT NULL,
    `question` TEXT NOT NULL,
    `answer` TEXT DEFAULT NULL,
    `difficulty` ENUM('easy', 'medium', 'hard') NOT NULL DEFAULT 'medium',
    `category` VARCHAR(255) DEFAULT NULL,
    `is_published` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_interview_questions_mentor_id` (`mentor_id`),
    INDEX `idx_interview_questions_field_id` (`field_id`),
    INDEX `idx_interview_questions_difficulty` (`difficulty`),
    INDEX `idx_interview_questions_category` (`category`),
    CONSTRAINT `fk_interview_questions_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_interview_questions_field` FOREIGN KEY (`field_id`) REFERENCES `academic_fields`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
