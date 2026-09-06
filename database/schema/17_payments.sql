-- =============================================
-- SkillShare Hub - Database Schema
-- Table 17: payments
-- =============================================

CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fresher_id` INT UNSIGNED NOT NULL,
    `course_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `payment_method` VARCHAR(100) NOT NULL,
    `status` ENUM('completed', 'pending', 'failed') NOT NULL DEFAULT 'pending',
    `payment_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `transaction_id` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_payments_fresher_id` (`fresher_id`),
    INDEX `idx_payments_course_id` (`course_id`),
    INDEX `idx_payments_status` (`status`),
    INDEX `idx_payments_payment_date` (`payment_date`),
    CONSTRAINT `fk_payments_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_payments_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
