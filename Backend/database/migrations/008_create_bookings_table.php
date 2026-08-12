<?php
/**
 * Migration 008 - Create bookings table
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance();
$sql = "CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_number VARCHAR(30) NOT NULL UNIQUE,
    mentor_id INT UNSIGNED NOT NULL,
    fresher_id INT UNSIGNED NOT NULL,
    skill_id INT UNSIGNED NOT NULL,
    session_date DATE NOT NULL,
    session_time TIME NOT NULL,
    duration INT UNSIGNED NOT NULL DEFAULT 60,
    hourly_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending','confirmed','completed','cancelled','rejected') NOT NULL DEFAULT 'pending',
    payment_status ENUM('pending','paid','refunded','failed') NOT NULL DEFAULT 'pending',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_mentor FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_fresher FOREIGN KEY (fresher_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_skill FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$db->execute($sql);
echo "Migration 008 ran successfully\n";