-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000020_create_ratings_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `ratings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `mentor_id` INT UNSIGNED NOT NULL,
    `fresher_id` INT UNSIGNED NOT NULL,
    `rating` TINYINT UNSIGNED NOT NULL,
    `review` TEXT DEFAULT NULL,
    `is_public` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    UNIQUE KEY `uk_ratings_mentor_fresher` (`mentor_id`, `fresher_id`),
    INDEX `idx_ratings_mentor_id` (`mentor_id`),
    INDEX `idx_ratings_fresher_id` (`fresher_id`),
    INDEX `idx_ratings_rating` (`rating`),
    CONSTRAINT `fk_ratings_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ratings_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
