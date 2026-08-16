<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getDB();

    // Check if academic_fields exists or create it
    $pdo->exec("CREATE TABLE IF NOT EXISTS `academic_fields` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(150) NOT NULL,
        `slug` VARCHAR(180) NOT NULL UNIQUE,
        `icon` VARCHAR(255) DEFAULT 'fa-layer-group',
        `color` VARCHAR(20) DEFAULT '#3b82f6',
        `description` TEXT,
        `total_courses` INT DEFAULT 0,
        `total_students` INT DEFAULT 0,
        `sort_order` INT DEFAULT 0,
        `status` ENUM('active','inactive') DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Check if courses table exists or create it
    $pdo->exec("CREATE TABLE IF NOT EXISTS `courses` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `academic_field_id` INT NOT NULL,
        `name` VARCHAR(200) NOT NULL,
        `slug` VARCHAR(220) NOT NULL UNIQUE,
        `icon` VARCHAR(255) DEFAULT 'fa-graduation-cap',
        `description` TEXT,
        `duration` VARCHAR(50) DEFAULT '4 Years',
        `level` VARCHAR(50) DEFAULT 'Bachelor',
        `thumbnail` VARCHAR(255) DEFAULT NULL,
        `rating` DECIMAL(3,2) DEFAULT 0.00,
        `reviews_count` INT DEFAULT 0,
        `total_mentors` INT DEFAULT 0,
        `total_students` INT DEFAULT 0,
        `status` ENUM('active','inactive') DEFAULT 'active',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    echo "Tables 'academic_fields' and 'courses' verified/created.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
