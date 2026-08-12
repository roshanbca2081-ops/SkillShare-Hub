<?php
/**
 * Migration 002 - Create mentors table
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance();
$sql = "CREATE TABLE IF NOT EXISTS mentors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    specialization VARCHAR(255) DEFAULT NULL,
    experience_years INT DEFAULT 0,
    mentoring_years INT DEFAULT 0,
    current_company VARCHAR(150) DEFAULT NULL,
    current_position VARCHAR(150) DEFAULT NULL,
    qualification VARCHAR(255) DEFAULT NULL,
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    verified_at DATETIME DEFAULT NULL,
    verified_by INT UNSIGNED DEFAULT NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    reviews_count INT NOT NULL DEFAULT 0,
    total_sessions INT NOT NULL DEFAULT 0,
    total_students INT NOT NULL DEFAULT 0,
    portfolio_url VARCHAR(255) DEFAULT NULL,
    linkedin_url VARCHAR(255) DEFAULT NULL,
    github_url VARCHAR(255) DEFAULT NULL,
    website_url VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mentors_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$db->execute($sql);
echo "Migration 002 ran successfully\n";