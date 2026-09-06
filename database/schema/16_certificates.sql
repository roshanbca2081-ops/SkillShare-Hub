-- =============================================
-- SkillShare Hub - Database Schema
-- Table 16: certificates
-- =============================================

CREATE TABLE IF NOT EXISTS `certificates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `fresher_id` INT UNSIGNED NOT NULL,
    `course_id` INT UNSIGNED NOT NULL,
    `certificate_code` VARCHAR(255) NOT NULL UNIQUE,
    `is_valid` TINYINT(1) NOT NULL DEFAULT 1,
    `issued_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_certificates_fresher_id` (`fresher_id`),
    INDEX `idx_certificates_course_id` (`course_id`),
    INDEX `idx_certificates_certificate_code` (`certificate_code`),
    CONSTRAINT `fk_certificates_fresher` FOREIGN KEY (`fresher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_certificates_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
