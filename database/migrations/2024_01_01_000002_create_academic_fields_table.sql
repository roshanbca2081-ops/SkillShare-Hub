-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000002_create_academic_fields_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `academic_fields` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `icon` VARCHAR(50) DEFAULT NULL,
    `color` VARCHAR(20) DEFAULT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    UNIQUE KEY `idx_academic_fields_slug` (`slug`),
    INDEX `idx_academic_fields_is_active` (`is_active`),
    INDEX `idx_academic_fields_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
