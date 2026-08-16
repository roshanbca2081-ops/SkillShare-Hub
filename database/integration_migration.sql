-- SkillShare Hub - Integration Migration
-- Adds missing columns to support existing API and frontend code

USE `skillshare_hub`;

-- BOOKINGS: Add columns expected by api/bookings.php and admin actions
ALTER TABLE `bookings`
    ADD COLUMN `booking_number` VARCHAR(50) NULL AFTER `id`,
    ADD COLUMN `fresher_id` INT NULL AFTER `mentor_id`,
    ADD COLUMN `skill_id` INT NULL AFTER `course_id`,
    ADD COLUMN `session_title` VARCHAR(255) DEFAULT 'Mentorship Session' AFTER `skill_id`,
    ADD COLUMN `session_description` TEXT NULL AFTER `session_title`,
    ADD COLUMN `session_date` DATE NULL AFTER `booking_date`,
    ADD COLUMN `session_time` TIME NULL AFTER `booking_time`,
    ADD COLUMN `duration` INT DEFAULT 60 AFTER `session_time`,
    ADD COLUMN `hourly_rate` DECIMAL(10,2) DEFAULT 0.00 AFTER `duration`,
    ADD COLUMN `total_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `hourly_rate`,
    ADD COLUMN `payment_status` ENUM('pending','paid','failed','refunded') DEFAULT 'pending' AFTER `status`,
    ADD COLUMN `cancellation_reason` TEXT NULL AFTER `notes`,
    ADD COLUMN `cancelled_at` DATETIME NULL AFTER `cancellation_reason`,
    ADD COLUMN `cancelled_by` INT NULL AFTER `cancelled_at`;

-- SESSIONS: Add columns expected by api/sessions.php
ALTER TABLE `sessions`
    ADD COLUMN `session_title` VARCHAR(255) NULL AFTER `booking_id`,
    ADD COLUMN `session_date` DATE NULL AFTER `mentor_id`,
    ADD COLUMN `session_time` TIME NULL AFTER `session_date`,
    ADD COLUMN `duration` INT DEFAULT 60 AFTER `session_time`,
    ADD COLUMN `feedback_mentor` TEXT NULL AFTER `status`,
    ADD COLUMN `feedback_fresher` TEXT NULL AFTER `feedback_mentor`,
    ADD COLUMN `rating_mentor` DECIMAL(2,1) NULL AFTER `feedback_fresher`,
    ADD COLUMN `rating_fresher` DECIMAL(2,1) NULL AFTER `rating_mentor`;

-- NOTIFICATIONS: Add columns expected by various APIs
ALTER TABLE `notifications`
    ADD COLUMN `title` VARCHAR(255) NULL AFTER `user_id`,
    ADD COLUMN `message` TEXT NULL AFTER `title`,
    ADD COLUMN `link` VARCHAR(500) NULL AFTER `payload`,
    ADD COLUMN `icon` VARCHAR(50) NULL AFTER `link`,
    ADD COLUMN `color` VARCHAR(20) NULL AFTER `icon`,
    ADD COLUMN `read_at` DATETIME NULL AFTER `is_read`;

-- REVIEWS: Add columns expected by Backend/models/Review.php and api/mentors.php
ALTER TABLE `reviews`
    ADD COLUMN `reviewer_id` INT NOT NULL AFTER `id`,
    ADD COLUMN `reviewee_id` INT NOT NULL AFTER `reviewer_id`,
    ADD COLUMN `booking_id` INT NULL AFTER `reviewee_id`,
    ADD COLUMN `is_public` TINYINT(1) DEFAULT 1 AFTER `comment`;

-- PAYMENTS: Add columns expected by api/payments.php
ALTER TABLE `payments`
    ADD COLUMN `invoice_number` VARCHAR(100) NULL AFTER `id`,
    ADD COLUMN `tax_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `amount`,
    ADD COLUMN `total_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `tax_amount`,
    ADD COLUMN `payment_method` VARCHAR(50) NULL AFTER `total_amount`,
    ADD COLUMN `payment_type` VARCHAR(50) NULL AFTER `payment_method`,
    ADD COLUMN `transaction_id` VARCHAR(255) NULL AFTER `payment_type`,
    ADD COLUMN `payment_gateway` VARCHAR(50) NULL AFTER `transaction_id`,
    ADD COLUMN `payment_date` DATETIME NULL AFTER `payment_gateway`;

-- SKILL_LEARNERS: Create table referenced by api/mentors.php
CREATE TABLE IF NOT EXISTS `skill_learners` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `skill_id` INT NOT NULL,
    `status` ENUM('in_progress','completed','certified') DEFAULT 'in_progress',
    `progress` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sl_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sl_skill` FOREIGN KEY (`skill_id`) REFERENCES `skills`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- MIGRATE EXISTING DATA: Copy user_id to fresher_id for bookings where user_id is the fresher
UPDATE `bookings` SET `fresher_id` = `user_id` WHERE `fresher_id` IS NULL;

-- MIGRATE EXISTING DATA: Copy reviewer/reviewee for reviews
UPDATE `reviews` SET `reviewer_id` = `user_id`, `reviewee_id` = `mentor_id` WHERE `reviewer_id` IS NULL;

-- MIGRATE EXISTING DATA: Copy booking_date to session_date for sessions
UPDATE `sessions` SET `session_date` = DATE(`start_at`) WHERE `session_date` IS NULL AND `start_at` IS NOT NULL;

-- Indexes for performance
ALTER TABLE `bookings` ADD INDEX `idx_booking_mentor_date` (`mentor_id`, `session_date`, `status`);
ALTER TABLE `bookings` ADD INDEX `idx_booking_fresher` (`fresher_id`);
ALTER TABLE `sessions` ADD INDEX `idx_session_booking` (`booking_id`);
ALTER TABLE `reviews` ADD INDEX `idx_review_mentor` (`reviewee_id`);
ALTER TABLE `notifications` ADD INDEX `idx_notif_user` (`user_id`, `is_read`);
