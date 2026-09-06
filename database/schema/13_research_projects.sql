-- =============================================
-- SkillShare Hub - Database Schema
-- Table 13: research_projects
-- =============================================

CREATE TABLE IF NOT EXISTS `research_projects` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `mentor_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(500) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `objectives` TEXT DEFAULT NULL,
    `required_skills` TEXT DEFAULT NULL,
    `status` ENUM('open', 'in_progress', 'closed') NOT NULL DEFAULT 'open',
    `max_applicants` INT UNSIGNED DEFAULT NULL,
    `start_date` DATE DEFAULT NULL,
    `end_date` DATE DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_research_projects_mentor_id` (`mentor_id`),
    INDEX `idx_research_projects_status` (`status`),
    CONSTRAINT `fk_research_projects_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
