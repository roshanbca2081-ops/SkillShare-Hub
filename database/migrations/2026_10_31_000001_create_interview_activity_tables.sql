-- Adds the optional persistence tables used by Fresher Interview Prep.
-- Safe to apply to an existing database; it creates tables only and preserves existing rows.

CREATE TABLE IF NOT EXISTS `interview_saved` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fresher_id` INT UNSIGNED NOT NULL,
    `question_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_interview_saved_fresher_question` (`fresher_id`, `question_id`),
    INDEX `idx_interview_saved_question_id` (`question_id`),
    CONSTRAINT `fk_interview_saved_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_interview_saved_question` FOREIGN KEY (`question_id`) REFERENCES `interview_questions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `interview_practice` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fresher_id` INT UNSIGNED NOT NULL,
    `question_id` INT UNSIGNED NOT NULL,
    `difficulty` ENUM('easy', 'medium', 'hard') NOT NULL,
    `notes` TEXT DEFAULT NULL,
    `practiced_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_interview_practice_fresher_date` (`fresher_id`, `practiced_at`),
    INDEX `idx_interview_practice_question_id` (`question_id`),
    CONSTRAINT `fk_interview_practice_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_interview_practice_question` FOREIGN KEY (`question_id`) REFERENCES `interview_questions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
