-- SkillShare Hub core schema
-- Creates core tables for users, profiles, bookings, sessions, assignments, messages, notifications, payments, certificates

CREATE DATABASE IF NOT EXISTS `skillshare_hub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `skillshare_hub`;

-- USERS
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(200) DEFAULT NULL,
  `firstname` VARCHAR(100) DEFAULT NULL,
  `lastname` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(200) NOT NULL UNIQUE,
  `password` VARCHAR(255) DEFAULT NULL,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('admin','mentor','fresher') NOT NULL DEFAULT 'fresher',
  `status` ENUM('active','inactive','pending') NOT NULL DEFAULT 'active',
  `profile_picture` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `academic_field_id` INT DEFAULT NULL,
  `course_id` INT DEFAULT NULL,
  `hourly_rate` DECIMAL(10,2) DEFAULT 0.00,
  `is_verified` TINYINT(1) DEFAULT 0,
  `email_verified` TINYINT(1) DEFAULT 0,
  `last_login` DATETIME DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT NULL,
  `date_of_birth` DATE DEFAULT NULL,
  `gender` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- USER PROFILES
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `bio` TEXT,
  `avatar` VARCHAR(255),
  `education` TEXT,
  `experience` TEXT,
  `skills` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_up_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- BOOKINGS
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `mentor_id` INT NOT NULL,
  `course_id` INT NULL,
  `booking_date` DATE NOT NULL,
  `booking_time` TIME NULL,
  `topic` VARCHAR(255) DEFAULT 'Mentorship Session',
  `notes` TEXT,
  `status` ENUM('pending','accepted','rejected','cancelled','completed') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SESSIONS
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `mentor_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `course_id` INT NULL,
  `start_at` DATETIME,
  `end_at` DATETIME,
  `meeting_link` VARCHAR(500),
  `status` ENUM('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_session_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ASSIGNMENTS
CREATE TABLE IF NOT EXISTS `assignments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `mentor_id` INT NOT NULL,
  `course_id` INT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `deadline` DATETIME,
  `attachment` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `assignment_submissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `assignment_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `file_path` VARCHAR(255),
  `status` ENUM('submitted','reviewed','graded') DEFAULT 'submitted',
  `marks` DECIMAL(5,2) DEFAULT NULL,
  `feedback` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_sub_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- MESSAGES & THREADS
CREATE TABLE IF NOT EXISTS `message_threads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `thread_id` INT NOT NULL,
  `from_user` INT NOT NULL,
  `to_user` INT NOT NULL,
  `content` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_msg_thread` FOREIGN KEY (`thread_id`) REFERENCES `message_threads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- NOTIFICATIONS
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `type` VARCHAR(100),
  `payload` JSON,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PAYMENTS
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `booking_id` INT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `method` VARCHAR(50),
  `transaction_ref` VARCHAR(255),
  `status` ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CERTIFICATES
CREATE TABLE IF NOT EXISTS `certificates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `course_id` INT NULL,
  `certificate_number` VARCHAR(100) UNIQUE,
  `issue_date` DATE,
  `status` ENUM('issued','revoked') DEFAULT 'issued',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- REVIEWS
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `mentor_id` INT NOT NULL,
  `rating` DECIMAL(2,1) NOT NULL,
  `comment` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SIMPLE ACTIVITY LOG
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(255),
  `meta` JSON,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure foreign keys to users where appropriate (best-effort)
ALTER TABLE `bookings` ADD CONSTRAINT IF NOT EXISTS `fk_booking_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `sessions` ADD CONSTRAINT IF NOT EXISTS `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `sessions` ADD CONSTRAINT IF NOT EXISTS `fk_session_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `messages` ADD CONSTRAINT IF NOT EXISTS `fk_message_from` FOREIGN KEY (`from_user`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `messages` ADD CONSTRAINT IF NOT EXISTS `fk_message_to` FOREIGN KEY (`to_user`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `payments` ADD CONSTRAINT IF NOT EXISTS `fk_pay_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `certificates` ADD CONSTRAINT IF NOT EXISTS `fk_cert_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `reviews` ADD CONSTRAINT IF NOT EXISTS `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `reviews` ADD CONSTRAINT IF NOT EXISTS `fk_review_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;

-- End of schema
