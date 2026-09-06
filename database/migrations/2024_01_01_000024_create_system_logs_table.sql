-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000024_create_system_logs_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `system_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_system_logs_user_id` (`user_id`),
    INDEX `idx_system_logs_action` (`action`),
    INDEX `idx_system_logs_created_at` (`created_at`),
    CONSTRAINT `fk_system_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
