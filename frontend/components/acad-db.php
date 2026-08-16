<?php
// SkillShare Hub - Academic Navigation DB Helper
// Provides a PDO connection + reusable query helper for the acad tables.
// Used by: academic-fields.php, courses.php, subjects.php, skills.php, mentors.php

if (!function_exists('acad_pdo')) {
    function acad_pdo() {
        static $pdo = null;
        if ($pdo === null) {
            if (defined('DB_HOST')) {
                $db_host = DB_HOST;
                $db_name = DB_NAME;
                $db_user = DB_USER;
                $db_pass = DB_PASS;
            } else {
                $db_host = 'localhost';
                $db_name = 'skillshare_hub';
                $db_user = 'root';
                $db_pass = '';
            }
            try {
                $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log('Acad DB connection failed: ' . $e->getMessage());
                $pdo = null;
            }
        }
        return $pdo;
    }
}

if (!function_exists('acad_query')) {
    /**
     * Run a query with optional params. Returns all rows (or single row if $single).
     */
    function acad_query($sql, $params = [], $single = false) {
        $pdo = acad_pdo();
        if (!$pdo) return $single ? null : [];
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $single ? $stmt->fetch() : $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Acad query failed: ' . $e->getMessage());
            return $single ? null : [];
        }
    }
}

