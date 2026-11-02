-- =============================================
-- SkillShare Hub - Database Schema
-- Table 26: oauth_accounts
-- =============================================

CREATE TABLE IF NOT EXISTS `oauth_accounts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `provider` ENUM('google', 'github', 'facebook') NOT NULL,
    `provider_user_id` VARCHAR(191) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_oauth_provider_subject` (`provider`, `provider_user_id`),
    UNIQUE KEY `uk_oauth_user_provider` (`user_id`, `provider`),
    INDEX `idx_oauth_accounts_user_id` (`user_id`),
    CONSTRAINT `fk_oauth_accounts_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
