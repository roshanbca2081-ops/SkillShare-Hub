-- =============================================
-- SkillShare Hub - Migration
-- Add booking details columns
-- =============================================

ALTER TABLE `bookings`
    ADD COLUMN `name` VARCHAR(255) DEFAULT NULL AFTER `notes`,
    ADD COLUMN `email` VARCHAR(255) DEFAULT NULL AFTER `name`,
    ADD COLUMN `address` TEXT DEFAULT NULL AFTER `email`,
    ADD COLUMN `contact` VARCHAR(100) DEFAULT NULL AFTER `address`,
    ADD COLUMN `interested_course` VARCHAR(500) DEFAULT NULL AFTER `contact`,
    ADD COLUMN `interested_skill` VARCHAR(255) DEFAULT NULL AFTER `interested_course`,
    ADD COLUMN `why_choose_skill` TEXT DEFAULT NULL AFTER `interested_skill`,
    ADD COLUMN `preferred_time` VARCHAR(255) DEFAULT NULL AFTER `why_choose_skill`,
    ADD COLUMN `payment_method` ENUM('esewa', 'khalti', 'fonepay', 'bank', 'card') DEFAULT NULL AFTER `preferred_time`,
    ADD COLUMN `payment_status` ENUM('pending', 'paid', 'failed', 'after_session') NOT NULL DEFAULT 'pending' AFTER `payment_method`,
    ADD COLUMN `payment_date` DATETIME DEFAULT NULL AFTER `payment_status`,
    ADD COLUMN `transaction_id` VARCHAR(255) DEFAULT NULL AFTER `payment_date`,
    ADD INDEX `idx_bookings_payment_status` (`payment_status`),
    ADD INDEX `idx_bookings_payment_method` (`payment_method`);
