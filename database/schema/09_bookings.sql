-- =============================================
-- SkillShare Hub - Database Schema
-- Table 09: bookings
-- =============================================

CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fresher_id` INT UNSIGNED NOT NULL,
    `session_id` INT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    `notes` TEXT DEFAULT NULL,
    `rating` TINYINT UNSIGNED DEFAULT NULL COMMENT 'Rating 1-5 given by fresher',
    `booking_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_bookings_fresher_id` (`fresher_id`),
    INDEX `idx_bookings_session_id` (`session_id`),
    INDEX `idx_bookings_status` (`status`),
    UNIQUE KEY `uk_bookings_fresher_session` (`fresher_id`, `session_id`),
    CONSTRAINT `fk_bookings_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_bookings_session` FOREIGN KEY (`session_id`) REFERENCES `sessions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
