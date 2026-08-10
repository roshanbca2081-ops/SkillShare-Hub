<?php

/**
 * Admin Model
 */

require_once __DIR__ . '/../config/database.php';

class Admin
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getSystemStats()
    {
        $stats = [
            'users' => 0,
            'mentors' => 0,
            'courses' => 0,
            'fields' => 0
        ];

        try {
            $u = $this->db->query("SELECT COUNT(*) as c FROM users")->fetch();
            $stats['users'] = $u['c'] ?? 0;
        } catch (Exception $e) {
        }

        try {
            $m = $this->db->query("SELECT COUNT(*) as c FROM acad_mentors")->fetch();
            $stats['mentors'] = $m['c'] ?? 0;
        } catch (Exception $e) {
        }

        try {
            $c = $this->db->query("SELECT COUNT(*) as c FROM acad_courses")->fetch();
            $stats['courses'] = $c['c'] ?? 0;
        } catch (Exception $e) {
        }

        try {
            $f = $this->db->query("SELECT COUNT(*) as c FROM acad_fields")->fetch();
            $stats['fields'] = $f['c'] ?? 0;
        } catch (Exception $e) {
        }

        return $stats;
    }
}
