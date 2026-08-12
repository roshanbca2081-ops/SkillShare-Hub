<?php
/**
 * Migration 009 - Create sessions table
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance();
$sql = "CREATE TABLE IF NOT EXISTS sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    mentor_id INT UNSIGNED NOT NULL,
    fresher_id INT UNSIGNED NOT NULL,
    session_date DATE NOT NULL,
    session_time TIME NOT NULL,
    duration INT NOT NULL DEFAULT 60,
    topic VARCHAR(255) NOT NULL,
    notes TEXT,
    status ENUM('scheduled','in_progress','completed','cancelled','no_show') NOT NULL DEFAULT 'scheduled',
    meeting_link VARCHAR(255) DEFAULT NULL,
    started_at DATETIME DEFAULT NULL,
    ended_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    INDEX idx_sessions_booking (booking_id),
    INDEX idx_sessions_mentor (mentor_id),
    INDEX idx_sessions_fresher (fresher_id),
    INDEX idx_sessions_status (status),
    CONSTRAINT fk_sessions_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_sessions_mentor FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_sessions_fresher FOREIGN KEY (fresher_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$db->execute($sql);
echo "Migration 009 ran successfully\n";