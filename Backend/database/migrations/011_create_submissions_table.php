<?php
/**
 * Migration 011 - Create submissions table
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance();
$sql = "CREATE TABLE IF NOT EXISTS submissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT UNSIGNED NOT NULL,
    fresher_id INT UNSIGNED NOT NULL,
    submission_text TEXT,
    file_path VARCHAR(255) DEFAULT NULL,
    submitted_at DATETIME NOT NULL,
    grade DECIMAL(5,2) DEFAULT NULL,
    feedback TEXT,
    graded_by INT UNSIGNED DEFAULT NULL,
    graded_at DATETIME DEFAULT NULL,
    status ENUM('submitted','graded','returned') NOT NULL DEFAULT 'submitted',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    INDEX idx_submissions_assignment (assignment_id),
    INDEX idx_submissions_fresher (fresher_id),
    INDEX idx_submissions_status (status),
    CONSTRAINT fk_submissions_assignment FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    CONSTRAINT fk_submissions_fresher FOREIGN KEY (fresher_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_submissions_graded_by FOREIGN KEY (graded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$db->execute($sql);
echo "Migration 011 ran successfully\n";