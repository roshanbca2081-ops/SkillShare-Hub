<?php
/**
 * Migration 001 - Create users table
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance();
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(30) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','mentor','fresher') NOT NULL DEFAULT 'fresher',
    academic_field INT UNSIGNED DEFAULT NULL,
    course INT UNSIGNED DEFAULT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default.png',
    bio TEXT,
    address VARCHAR(255) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    state VARCHAR(100) DEFAULT NULL,
    country VARCHAR(100) DEFAULT NULL,
    postal_code VARCHAR(20) DEFAULT NULL,
    hourly_rate DECIMAL(10,2) DEFAULT 0.00,
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    verification_token VARCHAR(255) DEFAULT NULL,
    email_verified_at DATETIME DEFAULT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_token_expiry DATETIME DEFAULT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    remember_expiry DATETIME DEFAULT NULL,
    status ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
    last_login DATETIME DEFAULT NULL,
    last_ip VARCHAR(45) DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    INDEX idx_users_role (role),
    INDEX idx_users_status (status),
    INDEX idx_users_academic_field (academic_field),
    INDEX idx_users_course (course)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$db->execute($sql);
echo "Migration 001 ran successfully\n";