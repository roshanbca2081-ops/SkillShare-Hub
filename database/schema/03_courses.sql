-- =============================================
-- SkillShare Hub - Database Schema
-- Table 03: courses
-- =============================================

CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(500) NOT NULL,
    `slug` VARCHAR(500) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `mentor_id` INT UNSIGNED NOT NULL,
    `field_id` INT UNSIGNED NOT NULL,
    `status` ENUM('active', 'inactive', 'draft') NOT NULL DEFAULT 'draft',
    `level` ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner',
    `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `duration` INT UNSIGNED DEFAULT NULL COMMENT 'Duration in hours',
    `thumbnail` VARCHAR(500) DEFAULT NULL,
    `featured` TINYINT(1) NOT NULL DEFAULT 0,
    `discount_price` DECIMAL(10, 2) DEFAULT NULL,
    `total_students` INT UNSIGNED NOT NULL DEFAULT 0,
    `progress` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_published` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_courses_mentor_id` (`mentor_id`),
    INDEX `idx_courses_field_id` (`field_id`),
    INDEX `idx_courses_status` (`status`),
    INDEX `idx_courses_slug` (`slug`),
    INDEX `idx_courses_created_at` (`created_at`),
    CONSTRAINT `fk_courses_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_courses_field` FOREIGN KEY (`field_id`) REFERENCES `academic_fields`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
