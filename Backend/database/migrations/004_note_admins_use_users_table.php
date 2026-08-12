<?php
/**
 * Migration 004 - No separate admins table
 *
 * Admin users are stored in the `users` table with role = 'admin'.
 * No table creation needed for this migration.
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance();
echo "Migration 004 ran successfully (admins use users table with role='admin')\n";