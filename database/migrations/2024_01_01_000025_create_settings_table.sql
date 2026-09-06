-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000025_create_settings_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) NOT NULL UNIQUE,
    `value` TEXT DEFAULT NULL,
    `type` VARCHAR(50) NOT NULL DEFAULT 'string',
    `description` TEXT DEFAULT NULL,
    `auto_load` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_settings_key` (`key`),
    INDEX `idx_settings_auto_load` (`auto_load`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
