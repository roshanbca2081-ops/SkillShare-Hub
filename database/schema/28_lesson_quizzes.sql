-- =============================================
-- SkillShare Hub - Database Schema
-- Table 28: lesson quiz questions and attempts
-- =============================================

CREATE TABLE IF NOT EXISTS `lesson_quiz_questions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `lesson_id` INT UNSIGNED NOT NULL,
    `question_text` TEXT NOT NULL,
    `option_a` VARCHAR(500) NOT NULL,
    `option_b` VARCHAR(500) NOT NULL,
    `option_c` VARCHAR(500) NOT NULL,
    `option_d` VARCHAR(500) NOT NULL,
    `correct_option` ENUM('A', 'B', 'C', 'D') NOT NULL,
    `order_number` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_lesson_quiz_questions_lesson_order` (`lesson_id`, `order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lesson_quiz_attempts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fresher_id` INT UNSIGNED NOT NULL,
    `lesson_id` INT UNSIGNED NOT NULL,
    `quiz_version` INT UNSIGNED NOT NULL,
    `score_percent` TINYINT UNSIGNED NOT NULL,
    `passed` TINYINT(1) NOT NULL DEFAULT 0,
    `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_lesson_quiz_attempts_lookup` (`fresher_id`, `lesson_id`, `quiz_version`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
