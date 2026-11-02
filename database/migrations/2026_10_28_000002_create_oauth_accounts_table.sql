-- Run once on existing SkillShare Hub databases to enable provider identity linking.
CREATE TABLE IF NOT EXISTS `oauth_accounts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    -- Keep signed INT for compatibility with existing installs whose users.id is signed.
    `user_id` INT NOT NULL,
    `provider` ENUM('google', 'github', 'facebook') NOT NULL,
    `provider_user_id` VARCHAR(191) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_oauth_provider_subject` (`provider`, `provider_user_id`),
    UNIQUE KEY `uk_oauth_user_provider` (`user_id`, `provider`),
    INDEX `idx_oauth_accounts_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
