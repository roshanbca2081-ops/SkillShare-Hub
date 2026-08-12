<?php
/**
 * Migration 004 - Admins table (intentionally skipped)
 *
 * There is no separate 'admins' table in this application.
 * Admin accounts are stored in the 'users' table with role = 'admin'.
 * See shareskill_hub.sql - the users table role ENUM includes 'admin'.
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance();
$sql = "SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name = 'users'";
$result = $db->fetch($sql);
if ($result && (int) $result['total'] > 0) {
    echo "Migration 004: admins use users table (role='admin') - no separate table needed\n";
} else {
    echo "Migration 004 warning: users table not found. Run migration 001 first.\n";
}