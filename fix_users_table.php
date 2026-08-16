<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getDB();
    echo "Connected to database successfully.\n";

    // 1. Get existing columns
    $stmt = $pdo->query("DESCRIBE users");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $columnsToAdd = [
        'full_name' => "VARCHAR(200) NULL AFTER id",
        'profile_picture' => "VARCHAR(255) NULL",
        'bio' => "TEXT NULL",
        'academic_field_id' => "INT NULL",
        'course_id' => "INT NULL",
        'hourly_rate' => "DECIMAL(10,2) DEFAULT 0.00",
        'is_verified' => "TINYINT(1) DEFAULT 0",
        'last_login' => "DATETIME NULL",
        'phone' => "VARCHAR(50) NULL",
        'address' => "VARCHAR(255) NULL",
        'city' => "VARCHAR(100) NULL",
        'state' => "VARCHAR(100) NULL",
        'country' => "VARCHAR(100) NULL",
        'date_of_birth' => "DATE NULL",
        'gender' => "VARCHAR(20) NULL",
        'password_hash' => "VARCHAR(255) NULL"
    ];

    foreach ($columnsToAdd as $col => $definition) {
        if (!in_array($col, $existingColumns)) {
            echo "Adding column '$col'...\n";
            $pdo->exec("ALTER TABLE users ADD COLUMN `$col` $definition");
        } else {
            echo "Column '$col' already exists.\n";
        }
    }

    // 2. Populate full_name where empty/null using firstname and lastname
    echo "Updating full_name for existing records...\n";
    $pdo->exec("UPDATE users SET full_name = TRIM(CONCAT(IFNULL(firstname, ''), ' ', IFNULL(lastname, ''))) WHERE full_name IS NULL OR full_name = ''");

    // 3. Populate firstname / lastname from full_name if firstname is null/empty
    $pdo->exec("UPDATE users SET firstname = SUBSTRING_INDEX(full_name, ' ', 1), lastname = SUBSTRING_INDEX(full_name, ' ', -1) WHERE (firstname IS NULL OR firstname = '') AND full_name IS NOT NULL AND full_name != ''");

    echo "Database schema update completed successfully!\n";
} catch (Exception $e) {
    echo "Error updating database schema: " . $e->getMessage() . "\n";
}
