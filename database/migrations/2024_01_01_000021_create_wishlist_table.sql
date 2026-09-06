-- =============================================
-- SkillShare Hub - Migration
-- 2024_01_01_000021_create_wishlist_table.sql
-- =============================================

CREATE TABLE IF NOT EXISTS `wishlist` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fresher_id` INT UNSIGNED NOT NULL,
    `course_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    UNIQUE KEY `uk_wishlist_fresher_course` (`fresher_id`, `course_id`),
    INDEX `idx_wishlist_fresher_id` (`fresher_id`),
    INDEX `idx_wishlist_course_id` (`course_id`),
    CONSTRAINT `fk_wishlist_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_wishlist_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
