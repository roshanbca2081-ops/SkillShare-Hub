-- =============================================
-- SkillShare Hub - Database Schema
-- Table 14: research_applications
-- =============================================

CREATE TABLE IF NOT EXISTS `research_applications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `project_id` INT UNSIGNED NOT NULL,
    `fresher_id` INT UNSIGNED NOT NULL,
    `cover_letter` TEXT DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_research_applications_project_id` (`project_id`),
    INDEX `idx_research_applications_fresher_id` (`fresher_id`),
    INDEX `idx_research_applications_status` (`status`),
    UNIQUE KEY `uk_research_applications_project_fresher` (`project_id`, `fresher_id`),
    CONSTRAINT `fk_research_applications_project` FOREIGN KEY (`project_id`) REFERENCES `research_projects`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_research_applications_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
